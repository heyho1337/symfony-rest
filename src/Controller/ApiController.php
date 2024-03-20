<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

#[Route('/api', name: 'api_')]
class ApiController extends AbstractController{
   
	#[Route('/{type}', name: 'api_list')]
    public function index(EntityManagerInterface $entityManager, string $type): JsonResponse{

		$entityClass = 'App\Entity\\' . ucfirst($type);
		if (!class_exists($entityClass)) {
            return $this->json("Entity class {$type} does not exist.", 404);
        }
		
		$rows = $entityManager
            ->getRepository($entityClass)
            ->findAll();
    
        $data = [];
    
        foreach ($rows as $row) {
			$data[] = $this->setData($row);
        }
    
        return $this->json($data);
    }
	
	#[Route('/{type}/{data}', name: 'api_show', methods:['get'] )]
	public function show(EntityManagerInterface $entityManager, string $type, string $data): JsonResponse{
		
		$entityClass = 'App\Entity\\' . ucfirst($type);

		if (!class_exists($entityClass)) {
			return $this->json("Entity class {$type} does not exist.", 404);
		}

		$pairs = explode('&', $data);
		$jsonData = [];
		
		foreach ($pairs as $pair) {
			$parts = explode('=', $pair, 2);
			$paramName = urldecode($parts[0]);
			$paramValue = urldecode($parts[1] ?? '');
			$jsonData[$paramName] = $paramValue;
		}

		$rows = $entityManager->getRepository($entityClass)->findBy($jsonData);

		if (!$rows) {
			$paramsString = implode(', ', array_map(function ($key, $value) {
				return "$key '$value'";
			}, array_keys($jsonData), $jsonData));
			return $this->json("No {$type} found for {$paramsString}.", 404);
		}

		$data = [];
    
        foreach ($rows as $row) {
			$data[] = $this->setData($row);
        }
			
		return $this->json($data);
	}

	#[Route('/{type}', name: 'api_create', methods: ['POST'])]
	public function create(EntityManagerInterface $entityManager, Request $request, string $type): JsonResponse{
		$entityClass = 'App\Entity\\' . ucfirst($type);

		if (!class_exists($entityClass)) {
			return $this->json("Entity class {$type} does not exist.", 404);
		}

		$row = new $entityClass();
		$reflectionClass = new \ReflectionClass($entityClass);
		$methods = $reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC);

		foreach ($methods as $method) {
			if (strpos($method->name, 'set') === 0) {
				$propertyName = lcfirst(substr($method->name, 3));
				$value = $request->request->get($propertyName);

				if ($value !== null) {
					$parameters = $method->getParameters();
					if (count($parameters) === 1) {
						$parameterType = $parameters[0]->getType();
						if ($parameterType !== null) {
							$parameterTypeName = $parameterType->getName();
							settype($value, $parameterTypeName);
						}
					}
					$row->{$method->name}($value);
				}
			}
		}

		$entityManager->persist($row);
		$entityManager->flush();

		$data = $this->setData($row);

		return $this->json($data);
	}

	#[Route('/{type}/{id_name}/{id}', name: 'api_update', methods:['PUT', 'PATCH'] )]
	public function update(EntityManagerInterface $entityManager, Request $request, string $type, string $id_name, string $id): JsonResponse{
		$entityClass = 'App\Entity\\' . ucfirst($type);

		if (!class_exists($entityClass)) {
			return $this->json("Entity class {$type} does not exist.", 404);
		}

		$entity = $entityManager->getRepository($entityClass)->findOneBy([$id_name => $id]);

		if (!$entity) {
			return $this->json("No {$type} found for {$id_name} {$id}", 404);
		}

		$requestData = $request->request->all();

		$reflectionClass = new \ReflectionClass($entityClass);
		$methods = $reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC);

		foreach ($methods as $method) {
			if (strpos($method->name, 'set') === 0) {
				$propertyName = lcfirst(substr($method->name, 3));
				if (isset($requestData[$propertyName])) {
					$value = $requestData[$propertyName];
					$parameters = $method->getParameters();
					if (count($parameters) === 1) {
						$parameterType = $parameters[0]->getType();
						if ($parameterType !== null) {
							$parameterTypeName = $parameterType->getName();
							settype($value, $parameterTypeName);
						}
					}
					$entity->{$method->name}($value);
				}
			}
		}

		$entityManager->flush();

		$data = $this->setData($entity);

		return $this->json($data);
	}

	protected function setData($row){
        $data = [];
        $reflectionClass = new \ReflectionClass($row);
        $methods = $reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC);

        foreach ($methods as $method) {
            if (strpos($method->name, 'get') === 0) {
                $propertyName = lcfirst(substr($method->name, 3));
                $data[$propertyName] = $method->invoke($row);
            }
        }

        return $data;
    }
}
