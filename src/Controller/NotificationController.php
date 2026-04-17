<?php

namespace App\Controller;

use App\Entity\Notification as NotificationEntity;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/notifications')]
class NotificationController extends AbstractController
{
    #[Route('/', name: 'notifications_index')]
    public function index(EntityManagerInterface $em, NotificationService $notif): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $notifications = $em->getRepository(NotificationEntity::class)->findBy(
            ['user' => $user],
            ['dateNotif' => 'DESC']
        );

        $notif->markAllAsRead($user->getIdUser());

        return $this->render('notification/index.html.twig', [
            'notifications' => $notifications,
        ]);
    }
}