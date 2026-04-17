<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\Thread;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ApiStatsController extends AbstractController
{
    #[Route('/api/stats/overview', name: 'api_stats_overview')]
    public function overview(EntityManagerInterface $em): JsonResponse
    {
        try {
            $threadCount = $em->getRepository(Thread::class)->count([]);
            $commentCount = $em->getRepository(Commentaire::class)->count([]);
            $userCount = $em->getRepository(User::class)->count([]);
            $flaggedCount = $em->getRepository(Commentaire::class)->count(['flagge' => true]);

            $last7Days = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = (new \DateTime())->modify("-$i days");
                $start = (clone $date)->setTime(0, 0, 0);
                $end = (clone $date)->setTime(23, 59, 59);
                
                $count = $em->createQueryBuilder()
                    ->select('COUNT(t.idThread)')
                    ->from(Thread::class, 't')
                    ->where('t.dateCreation BETWEEN :start AND :end')
                    ->setParameter('start', $start)
                    ->setParameter('end', $end)
                    ->getQuery()
                    ->getSingleScalarResult();
                $last7Days[] = ['date' => $date->format('Y-m-d'), 'count' => (int)$count];
            }

            return $this->json([
                'threads' => $threadCount,
                'comments' => $commentCount,
                'users' => $userCount,
                'flagged_comments' => $flaggedCount,
                'evolution' => $last7Days,
            ]);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()], 500);
        }
    }
}