<?php

namespace App\Service;

use App\Entity\Reputation;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class ReputationService
{
    public const POINTS_THREAD = 5;
    public const POINTS_UPVOTE = 3;
    public const POINTS_COMMENTAIRE = 2;
    public const POINTS_LIKE = 1;

    public function __construct(private EntityManagerInterface $em) {}

    public function getReputation(int $userId): Reputation
    {
        return $this->getOrCreate($userId);
    }

    public function addPoints(int $userId, int $points): void
    {
        $rep = $this->getOrCreate($userId);
        $rep->setPoints($rep->getPoints() + $points);
        $rep->setBadge($this->computeBadge($rep->getPoints()));
        $this->em->flush();
    }

    public function deductPoints(int $userId, int $points): void
    {
        $rep = $this->getOrCreate($userId);
        $newPoints = max(0, $rep->getPoints() - $points);
        $rep->setPoints($newPoints);
        $rep->setBadge($this->computeBadge($newPoints));
        $this->em->flush();
    }

    private function getOrCreate(int $userId): Reputation
    {
        $user = $this->em->getRepository(User::class)->find($userId);
        if (!$user) {
            throw new \Exception("Utilisateur non trouvé pour la réputation.");
        }
        $rep = $this->em->getRepository(Reputation::class)->findOneBy(['user' => $user]);
        if (!$rep) {
            $rep = new Reputation();
            $rep->setUser($user);
            $rep->setPoints(0);
            $rep->setBadge('🌱 Débutant');
            $this->em->persist($rep);
            $this->em->flush();
        }
        return $rep;
    }

    private function computeBadge(int $points): string
    {
        if ($points >= 101) return '👑 Maître';
        if ($points >= 51) return '🌾 Expert';
        if ($points >= 11) return '🌿 Actif';
        return '🌱 Débutant';
    }
}