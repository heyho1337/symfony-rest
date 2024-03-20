<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;

#[Route('/api', name: 'api_')]
class UserController extends AbstractController{
    #[Route('/user', name: 'app_user')]
    public function index(EntityManagerInterface $entityManager): JsonResponse{
        $users = $entityManager
            ->getRepository(User::class)
            ->findAll();
    
        $data = [];
    
        foreach ($users as $user) {
			$data[] =  [
				'id' => $user->getId(),
				'user_name' => $user->getUserName(),
				'user_email' => $user->getUserEmail(),
				'active_league' => $user->getActiveLeague(),
				'leagueId' => $user->getLeagueId()
			];
        }
    
        return $this->json($data);
    }

	#[Route('/user/{id}', name: 'user_show', methods:['get'] )]
    public function show(EntityManagerInterface $entityManager, int $id): JsonResponse{
        $user = $entityManager->getRepository(User::class)->find($id);
    
        if (!$user) {
    
            return $this->json('No user found for id ' . $id, 404);
        }
    
        $data =  [
            'id' => $user->getId(),
            'user_name' => $user->getUserName(),
            'user_email' => $user->getUserEmail(),
			'active_league' => $user->getActiveLeague(),
			'leagueId' => $user->getLeagueId()
        ];
            
        return $this->json($data);
    }
}
