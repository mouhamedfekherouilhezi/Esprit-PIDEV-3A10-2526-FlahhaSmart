<?php

namespace App\Twig;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class NotificationExtension extends AbstractExtension
{
    private EntityManagerInterface $em;
    private Security $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('unread_notifications_count', [$this, 'getUnreadCount']),
        ];
    }

    public function getUnreadCount(): int
    {
        $user = $this->security->getUser();
        if (!$user instanceof User) {
            return 0;
        }
        $qb = $this->em->createQueryBuilder();
        $qb->select('COUNT(n.idNotif)')
            ->from('App\Entity\Notification', 'n')
            ->where('n.user = :user')
            ->andWhere('n.lu = :lu')
            ->setParameter('user', $user)
            ->setParameter('lu', false);
        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}