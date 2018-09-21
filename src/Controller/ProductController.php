<?php

namespace App\Controller;

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
     * @Rest\Post("/product")
     */
    public function postProduct(Request $request): View
    {
        $product = $this->productService->addProduct($request->get('title'), $request->get('description'));
        return View::create($product, Response::HTTP_CREATED);
    }
}