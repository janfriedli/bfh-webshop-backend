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
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;

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
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns a single Order",
     *      @SWG\Schema(ref=@Model(type=StoreOrder::class))
     * )
     * @SWG\Tag(name="Order")
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
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns a list of Orders",
     *     @SWG\Schema(ref=@Model(type=StoreOrder::class))
     * )
     * @SWG\Tag(name="Order")
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
     *
     * @SWG\Tag(name="Order")
     * @SWG\Response(
     *     response=201,
     *     description="Creates a new Order and returns it directly after",
     *     @SWG\Schema(ref=@Model(type=StoreOrder::class))
     * )
     * @SWG\Parameter(
     * 		name="Order",
     * 		in="body",
     * 		required=true,
     * 		@SWG\Schema(ref=@Model(type=StoreOrder::class)),
     * )
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
     *
     * @SWG\Tag(name="Order")
     * @SWG\Response(
     *     response=200,
     *     description="Updates an existing Order",
     *      @SWG\Schema(ref=@Model(type=StoreOrder::class))
     * )
     * @SWG\Parameter(
     * 		name="Order",
     * 		in="body",
     * 		required=true,
     * 		@SWG\Schema(ref=@Model(type=StoreOrder::class)),
     * )
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
     *
     * @SWG\Tag(name="Order")
     * @SWG\Response(
     *     response=204,
     *     description="Deletes the specified Order",
     *      @SWG\Schema(ref=@Model(type=StoreOrder::class))
     * )
     */
    public function deleteOrder(int $orderId): View
    {
        $this->orderService->deleteOrder($orderId);
        return View::create([], Response::HTTP_NO_CONTENT);
    }
}