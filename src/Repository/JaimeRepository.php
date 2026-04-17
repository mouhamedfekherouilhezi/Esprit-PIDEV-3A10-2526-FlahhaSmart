<?php

namespace App\Repository;

use App\Entity\Jaime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\AsDoctrineRepository;

#[AsDoctrineRepository]
class JaimeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Jaime::class);
    }
}