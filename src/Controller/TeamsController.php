<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Teams;
 
 
#[Route('/api', name: 'api_')]
class TeamsController extends AbstractController{
   
	#[Route('/teams', name: 'app_teams')]
    public function index(EntityManagerInterface $entityManager): JsonResponse{
        $teams = $entityManager
            ->getRepository(Teams::class)
            ->findAll();
    
        $data = [];
    
        foreach ($teams as $team) {
			$data[] = $this->setData($team);
        }
    
        return $this->json($data);
    }

	#[Route('/teams', name: 'teams_create', methods:['post'] )]
	public function create(EntityManagerInterface $entityManager, Request $request): JsonResponse
    {
        $team = new Teams();
        $team->setAccountId($request->request->get('account_id'));
        $team->setTeamsId($request->request->get('teams_id'));
		$team->setLeagueId($request->request->get('league_id'));
		$team->setUserEmail($request->request->get('user_email'));
    
        $entityManager->persist($team);
        $entityManager->flush();
    
        $data = $this->setData($team);
            
        return $this->json($data);
    }

	#[Route('/teams/{teamsId}', name: 'leagues_show', methods: ['GET'])]
	public function show(EntityManagerInterface $entityManager, string $teamsId): JsonResponse{
		$team = $entityManager->getRepository(Teams::class)->findOneBy(['teams_id' => $teamsId]);

		if (!$team) {
			return $this->json('No team found for teamsId ' . $teamsId, 404);
		}

		$data = $this->setData($team);

		return $this->json($data);
	}

	protected function setData($team){
		return [
            'id' => $team->getId(),
			'account_id' => $team->getAccountId(),
			'teams_id' => $team->getTeamsId(),
			'league_id' => $team->getLeagueId(),
			'user_email' => $team->getUserEmail()
        ];
	}
}
