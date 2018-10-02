<?php
namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class CRUDService generalizing crud methods of entities
 * @package App\Service
 */
final class CRUDService
{
    /**
     * @var EntityManagerInterface $entityManager
     */
    private $entityManager;


    /**
     * CRUDService constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->entityManager = $em;  
    }

    /**
     * Persist a product
     * @param $entity
     * @return object
     */
    public function save($entity) {
        $this->entityManager->persist($entity);
        $this->entityManager->flush($entity);
        $this->entityManager->refresh($entity);

        return $entity;
    }

    /**
     * @param $entity
     */
    public function delete($entity) {
        $this->entityManager->remove($entity);
        $this->entityManager->flush($entity);
    }

    /**
     * @param int $id
     * @param $entity
     * @return object
     */
    public function update(int $id, $entity) {
        if ($id < 0) {
            throw new BadRequestHttpException('The entity id can\'t be lower than zero');
        }

        $entity->setId($id);
        $entity = $this->entityManager->merge($entity);
        $this->entityManager->flush();

        return $entity;
    }
}