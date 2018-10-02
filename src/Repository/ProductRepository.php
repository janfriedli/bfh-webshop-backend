<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @todo this can be generalized ....
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    /**
     * ProductRepository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * Persist a product
     * @param Product $product
     * @throws \Doctrine\ORM\ORMException
     * @return Product
     */
    public function save(Product $product) {
        $this->getEntityManager()->persist($product);
        $this->getEntityManager()->flush($product);
        $this->getEntityManager()->refresh($product);

        return $product;
    }

    /**
     * @param Product $product
     * @throws \Doctrine\ORM\ORMException
     */
    public function delete(Product $product) {
        $this->getEntityManager()->remove($product);
        $this->getEntityManager()->flush($product);
    }

    /**
     * @param int $id
     * @param Product $product
     * @return object
     * @throws \Doctrine\ORM\ORMException
     */
    public function update(int $id, Product $product) {
        if ($id < 0) {
            throw new BadRequestHttpException('The product id can\'t be lower than zero');
        }

        $product->setId($id);
        $product = $this->getEntityManager()->merge($product);
        $this->getEntityManager()->flush();

        return $product;
    }
}
