<?php

namespace App\Controller;

use App\Entity\StoreOrder;
use Doctrine\ORM\EntityNotFoundException;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use App\Service\OrderService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use App\Exception\ValidationException;


class OrderController extends FOSRestController
{
    /**
     * @var OrderService 
     */
    private $orderService;

    /**
     * OrderController constructor.
     * @param OrderService $orderService
     */
    public function __construct(OrderService $orderService)
    {
        $this->orderService= $orderService;
    }

    /**
     * Retrieves an Order resource
     * @Rest\Get("/order/{orderId}")
     * @param int $orderId
     * @return View
     * @throws \Doctrine\ORM\ORMException
     */
    public function getOrder(int $orderId): View
    {
        $order = $this->orderService->getOrder($orderId);
        if (!$order) {
            throw new EntityNotFoundException('Order with id '.$orderId.' does not exist!');
        }

        return View::create($order, Response::HTTP_OK);
    }
    
    /**
     * Gets the complete order list
     * @Rest\Get("/order")
     * @param Request $request
     * @return View
     */
    public function orderList(Request $request): View {
        return View::create($this->orderService->getAllOrders(), Response::HTTP_OK);
    }

    /**
     * Creates an Order resource
     * @param StoreOrder $order
     * @param ConstraintViolationListInterface $validationErrors
     * @ParamConverter("order", converter="fos_rest.request_body")
     * @Rest\Post("/order")
     * @return View
     */
    public function postOrder(StoreOrder $order, ConstraintViolationListInterface $validationErrors): View
    {
        if (count($validationErrors) > 0) {
            return View::create($validationErrors, Response::HTTP_BAD_REQUEST);
        }

        $order = $this->orderService->addOrder($order);
        return View::create($order, Response::HTTP_CREATED);
    }

    /**
     * @Rest\Put("/order/{orderId}")
     * @param int $orderId
     * @param StoreOrder $order
     * @param ConstraintViolationListInterface $validationErrors
     * @ParamConverter("order", converter="fos_rest.request_body")
     * @return View
     * @throws \Doctrine\ORM\ORMException
     */
    public function putOrder(int $orderId, StoreOrder $order, ConstraintViolationListInterface $validationErrors): View
    {
        if (count($validationErrors) > 0) {
            return View::create($validationErrors, Response::HTTP_BAD_REQUEST);
        }

        $updatedOrder = $this->orderService->updateOrder($orderId, $order);
        if (!$updatedOrder) {
            throw new EntityNotFoundException('Order with id '.$orderId.' does not exist!');
        }

        return View::create($updatedOrder, Response::HTTP_OK);
    }

    /**
     * Removes a Order resource
     * @Rest\Delete("/order/{orderId}")
     * @param int $orderId
     * @return View
     * @throws \Doctrine\ORM\ORMException
     */
    public function deleteOrder(int $orderId): View
    {
        $this->orderService->deleteOrder($orderId);
        return View::create([], Response::HTTP_NO_CONTENT);
    }
}