<?php

namespace App\Repository;

use App\Entity\Partner;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PartnerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Partner::class);
    }

    /**
     * @param $token
     * @return Partner|object|null
     */
    public function findPartnerByEmailConfirmationToken($token)
    {
        $partner = $this->findOneBy(['emailConfirmationToken' => $token]);

        return $partner ?? null;
    }
}
