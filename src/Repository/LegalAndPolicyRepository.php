<?php

// src/Repository/LegalAndPolicyPageRepository.php

namespace App\Repository;

use App\Entity\LegalAndPolicy;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<LegalAndPolicy>
 */
class LegalAndPolicyRepository extends ServiceEntityRepository
{
    private $entityManager;
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, LegalAndPolicy::class);
        $this->entityManager = $entityManager;
    }

    public function save(LegalAndPolicy $legalAndPolicy): void
    {
        $this->entityManager->persist($legalAndPolicy);
        $this->entityManager->flush();
    }

    public function remove(LegalAndPolicy $legalAndPolicy): void
    {
        $this->entityManager->remove($legalAndPolicy);
        $this->entityManager->flush();
    }

    public function findOne(): LegalAndPolicy
    {
        return $this->findOneBy([]);
    }

}
