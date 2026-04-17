<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\Thread;
use App\Service\ReputationService;
use App\Entity\Notification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/dashboard')]
#[IsGranted('ROLE_USER')]
class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard')]
    public function index(EntityManagerInterface $em, ReputationService $repService): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        
        // Statistiques
        $threadCount = $em->getRepository(Thread::class)->count(['auteur' => $user]);
        $commentCount = $em->getRepository(Commentaire::class)->count(['auteur' => $user]);
        
        // Réputation
        $reputation = $repService->getReputation($user->getIdUser());
        
        // Dernières activités (Threads)
        $recentThreads = $em->getRepository(Thread::class)->findBy(
            ['auteur' => $user],
            ['dateCreation' => 'DESC'],
            5
        );

        // Notifications non lues
        $unreadNotifications = $em->getRepository(Notification::class)->findBy(
            ['user' => $user, 'lu' => false],
            ['dateNotif' => 'DESC'],
            5
        );

        return $this->render('dashboard/index.html.twig', [
            'threadCount' => $threadCount,
            'commentCount' => $commentCount,
            'reputation' => $reputation,
            'recentThreads' => $recentThreads,
            'unreadNotifications' => $unreadNotifications,
        ]);
    }
}
