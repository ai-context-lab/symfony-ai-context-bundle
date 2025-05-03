<?php

namespace AiContextBundle\Tests\fixtures\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DummyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, \stdClass::class);
    }

    public function findSomething(): array
    {
        return [];
    }
}
