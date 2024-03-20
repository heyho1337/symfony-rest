<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Leagues;

#[Route('/api', name: 'api_')]
class LeaguesController extends AbstractController{
    
	#[Route('/leagues', name: 'app_leagues')]
    public function index(EntityManagerInterface $entityManager): JsonResponse{
        $leagues = $entityManager
            ->getRepository(Leagues::class)
            ->findAll();
    
        $data = [];
    
        foreach ($leagues as $league) {
			$data[] = [
				'id' => $league->getId(),
				'league_id' => $league->getLeagueId(),
				'active' => $league->getActive(),
				'full' => $league->isFull(),
				'name' => $league->getName()
			];
        }
    
        return $this->json($data);
    }

	#[Route('/leagues/{leagueId}', name: 'leagues_show', methods: ['GET'])]
	public function show(EntityManagerInterface $entityManager, string $leagueId): JsonResponse{
		$league = $entityManager->getRepository(Leagues::class)->findOneBy(['league_id' => $leagueId]);

		if (!$league) {
			return $this->json('No league found for leagueId ' . $leagueId, 404);
		}

		$data = [
			'id' => $league->getId(),
			'league_id' => $league->getLeagueId(),
			'active' => $league->getActive(),
			'full' => $league->isFull(),
			'name' => $league->getName()
		];

		return $this->json($data);
	}
}
