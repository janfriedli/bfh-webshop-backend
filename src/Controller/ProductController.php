<?php

namespace App\Controller;

use Doctrine\ORM\EntityNotFoundException;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use App\Service\ProductService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends FOSRestController
{
    /**
     * @var ProductService 
     */
    private $productService;

    /**
     * ProductController constructor.
     * @param ProductService $productService
     */
    public function __construct(ProductService $productService)
    {
        $this->productService= $productService;
    }

    /**
     * Retrieves an Product resource
     * @Rest\Get("/product/{productId}")
     * @param int $productId
     * @return View
     * @throws \Doctrine\ORM\ORMException
     */
    public function getProduct(int $productId): View
    {
        $product = $this->productService->getProduct($productId);
        if (!$product) {
            throw new EntityNotFoundException('Product with id '.$productId.' does not exist!');
        }

        return View::create($product, Response::HTTP_OK);
    }
    
    /**
     * Gets the complete product list
     * @Rest\Get("/product")
     * @param Request $request
     * @return View
     */
    public function productList(Request $request): View {
        return View::create($this->productService->getAllProducts(), Response::HTTP_OK);
    }

    /**
     * Creates an Product resource
     * @param Request $request
     * @Rest\Post("/product")
     * @return View
     * @throws \Doctrine\ORM\ORMException
     */
    public function postProduct(Request $request): View
    {
        $product = $this->productService->addProduct($request->get('title'), $request->get('description'));
        return View::create($product, Response::HTTP_CREATED);
    }

    /**
     * @Rest\Put("/product/{productId}")
     * @param int $productId
     * @param Request $request
     * @return View
     * @throws \Doctrine\ORM\ORMException
     */
    public function putProduct(int $productId, Request $request): View
    {
        $product = $this->productService->updateProduct($productId, $request->get('title'), $request->get('description'));
        if (!$product) {
            throw new EntityNotFoundException('Product with id '.$productId.' does not exist!');
        }

        return View::create($product, Response::HTTP_OK);
    }

    /**
     * Removes a Product resource
     * @Rest\Delete("/product/{productId}")
     * @param int $productId
     * @return View
     * @throws \Doctrine\ORM\ORMException
     */
    public function deleteProduct(int $productId): View
    {
        $this->productService->deleteProduct($productId);
        return View::create([], Response::HTTP_NO_CONTENT);
    }
}