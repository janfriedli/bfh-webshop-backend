<?php
namespace App\Service;

use App\Entity\StoreOrder;
use App\Repository\OrderRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

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
     * OrderService constructor.
     * @param OrderRepository $orderRepository
     * @param CRUDService $crud
     */
    public function __construct(OrderRepository $orderRepository, CRUDService $crud) {
        $this->orderRepository = $orderRepository;
        $this->crud = $crud;
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
     * @return StoreOrder
     */
    public function addOrder(StoreOrder $order): StoreOrder
    {
        $order = $this->crud->save($order);
        return $order;
    }

    /**
     * @param int $orderId
     * @param StoreOrder $updatedOrder
     * @return null|StoreOrder
     */
    public function updateOrder(int $orderId, $updatedOrder): ?StoreOrder
    {
        $order = $this->orderRepository->findOneById($orderId);
        if (!$order) {
            return null;
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

}