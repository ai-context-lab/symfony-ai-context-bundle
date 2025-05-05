<?php

namespace AiContextBundle\Tests\fixtures\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<\stdClass>
 */
class DummyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, \stdClass::class);
    }

    /**
     * @return \stdClass[]
     */
    public function findSomething(): array
    {
        return [];
    }
}
