<?php

namespace App\Repository;

use App\Entity\StoreOrder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method StoreOrder|null find($id, $lockMode = null, $lockVersion = null)
 * @method StoreOrder|null findOneBy(array $criteria, array $orderBy = null)
 * @method StoreOrder[]    findAll()
 * @method StoreOrder[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{
    /**
     * OrderRepository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, StoreOrder::class);
    }
}
