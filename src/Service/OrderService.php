<?php
namespace App\Service;

use App\Entity\Product;
use App\Entity\StoreOrder;
use App\Repository\OrderRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class OrderService
{
    /**
     * @var ServiceEntityRepository
     */
    private $orderRepository;

    /**
     * @var CRUDService $crud
     */
    private $crud;

    /**
     * @var EntityManagerInterface $em
     */
    private $em;

    /**
     * OrderService constructor.
     * @param OrderRepository $orderRepository
     * @param CRUDService $crud
     * @param EntityManagerInterface $em
     */
    public function __construct(OrderRepository $orderRepository, CRUDService $crud, EntityManagerInterface $em) {
        $this->orderRepository = $orderRepository;
        $this->crud = $crud;
        $this->em = $em;
    }

    /**
     * @param int $orderId
     * @return null|StoreOrder
     */
    public function getOrder(int $orderId): ?StoreOrder
    {
        return $this->orderRepository->findOneById($orderId);
    }

    /**
     * @return array|null
     */
    public function getAllOrders(): ?array
    {
        return $this->orderRepository->findAll();
    }

    /**
     * @param StoreOrder $order
     * @return object
     */
    public function addOrder(StoreOrder $order)
    {
        $order = $this->crud->save($this->associateProducts($order));
        return $order;
    }

    /**
     * @param int $orderId
     * @param StoreOrder $updatedOrder
     * @return null|object
     */
    public function updateOrder(int $orderId, $updatedOrder): ?StoreOrder
    {
        $order = $this->orderRepository->findOneById($orderId);
        if (!$order) {
            return null;
        }

        return $this->crud->update($orderId, $this->associateProducts($updatedOrder));
    }

    /**
     * @param int $orderId
     */
    public function deleteOrder(int $orderId): void
    {
        $order = $this->orderRepository->findOneById($orderId);
        if ($order) {
            $this->crud->delete($order);
        }
    }

    /**
     * map the associated products to its doctrine instance
     * @param StoreOrder $order
     * @return StoreOrder
     */
    private function associateProducts(StoreOrder $order) {
        $products = new ArrayCollection();
        foreach ($order->getProducts() as $product) {
            $storedProduct = $this->em->getRepository(Product::class)->findOneById($product->getId());
            if (!$storedProduct) {
                throw new BadRequestHttpException(
                    'The product with the id: ' . $product->getId() . ' does not exist!'
                );
            }

            $products->add($storedProduct);
        }
        $order->setProducts($products);

        return $order;
    }
}