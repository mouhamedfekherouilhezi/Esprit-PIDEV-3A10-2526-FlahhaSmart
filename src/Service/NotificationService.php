<?php

namespace App\Service;

use App\Entity\Notification as NotificationEntity;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;

class NotificationService
{
    public function __construct(
        private EntityManagerInterface $em,
        private NotifierInterface $notifier,
        private MailService $mailService,
    ) {}

    public function create(int $userId, string $message, string $type): void
    {
        $user = $this->em->getRepository(User::class)->find($userId);
        if (!$user) {
            return; // Ou logguer l'erreur
        }

        // Sauvegarde en base
        $notif = new NotificationEntity();
        $notif->setUser($user);
        $notif->setMessage($message);
        $notif->setType($type);
        $notif->setLu(false);
        $notif->setDateNotif(new \DateTime());
        $this->em->persist($notif);
        $this->em->flush();

        // Envoi d'email formaté
        if ($user->getEmail()) {
            $this->mailService->sendNotificationEmail($user->getEmail(), $user->getNom() ?? 'Client', $message);
        }
    }

    public function getUnreadCount(int $userId): int
    {
        return $this->em->getRepository(NotificationEntity::class)->count([
            'user' => $userId,
            'lu' => false,
        ]);
    }

    public function markAllAsRead(int $userId): void
    {
        $this->em->createQueryBuilder()
            ->update(NotificationEntity::class, 'n')
            ->set('n.lu', true)
            ->where('n.user = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->execute();
    }
}