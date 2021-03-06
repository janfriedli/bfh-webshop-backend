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
        foreach ($order->getDetails() as $detail) {

            $detail->setProduct($this->em->merge($detail->getProduct()));
        }
        $order = $this->crud->save($order);
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

        if ($updatedOrder->getPaid()) {
            $this->subtractProductQuantity($updatedOrder);
        }

        return $this->crud->update($orderId, $updatedOrder);
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
     * @param StoreOrder $order
     */
    private function subtractProductQuantity(StoreOrder $order) {
        foreach ($order->getDetails() as $detail) {
            $product = $detail->getProduct();
            if ($detail->getQuantity() <= $product->getQuantity()) {
                $product->setQuantity($product->getQuantity() - $detail->getQuantity());
                $this->crud->update($product->getId(),$product);
            } else {
                throw new BadRequestHttpException(
                    "Only " . $product->getQuantity() . " of " . $product->getTitle() . " available"
                );
            }

        }
    }
}