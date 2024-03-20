<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

#[Route('/api', name: 'api_')]
class ApiController extends AbstractController{
   
	#[Route('/list/{type}', name: 'api_list')]
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
	
	#[Route('/get/{type}/{data}', name: 'api_show', methods:['get'] )]
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

	#[Route('/set/{type}', name: 'api_create', methods: ['POST'])]
	public function create(EntityManagerInterface $entityManager, Request $request, string $type): JsonResponse{
		$entityClass = 'App\Entity\\' . ucfirst($type);

		if (!class_exists($entityClass)) {
			return $this->json("Entity class {$type} does not exist.", 404);
		}

		$row = new $entityClass();
		$reflectionClass = new \ReflectionClass($entityClass);
		$methods = $reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC);
		$leagueId = $this->randomPassword(20);
		$requestData = json_decode($request->getContent(), true);
		if ($requestData === null) {
			return $this->json('Invalid JSON data provided', 400);
		}
		if($type === 'leagues'){
			$requestData['leagueId'] = $leagueId;
		}

		foreach ($methods as $method) {
			if (strpos($method->name, 'set') === 0) {
				$propertyName = lcfirst(substr($method->name, 3));
				$value = $requestData[$propertyName];
				if($value === '' || $value === ' ' || $value === NULL || $value === 'NULL'){
					$value = $leagueId;
				}
				
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

		$entityManager->persist($row);
		$entityManager->flush();

		$data = $this->setData($row);
		if($type === 'leagues'){
			$data['id'] = $leagueId;
		}

		return $this->json($data);
	}

	#[Route('/change/{type}/{data}', name: 'api_update', methods:['PUT', 'PATCH'] )]
	public function update(EntityManagerInterface $entityManager, Request $request, string $type, string $data): JsonResponse{
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

		$entity = $entityManager->getRepository($entityClass)->findOneBy($jsonData);


		if (!$entity) {
			return $this->json("No {$type} found", 404);
		}

		$requestData = json_decode($request->getContent(), true);
		if ($requestData === null) {
			return $this->json('Invalid JSON data provided', 400);
		}

		$reflectionClass = new \ReflectionClass($entityClass);
		$methods = $reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC);

		foreach ($methods as $method) {
			if (strpos($method->name, 'set') === 0) {
				$propertyName = lcfirst(substr($method->name, 3));
				if (isset($requestData[$propertyName])) {
					$value = $requestData[$propertyName];
					if (is_array($value)) {
						$value = '{'.implode(',', $value).'}';
					}
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

	/**
	 * generating random characters
	 * @param int $length character's length
	 * @return string the random characther chain 
	*/
	function randomPassword($length) {  
		$possibleCharacters = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";  
		$characterLength = strlen($possibleCharacters);  
		$password = "";  
		
		for ($i = 0; $i < $length; $i++) {  
			$character = $possibleCharacters[rand(0, $characterLength - 1)];  
			$password .= $character;  
		}  
		
		return $password;  
	}

}
