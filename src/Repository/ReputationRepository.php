<?php

namespace App\Repository;

use App\Entity\Reputation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\AsDoctrineRepository;

#[AsDoctrineRepository]
class ReputationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reputation::class);
    }
}