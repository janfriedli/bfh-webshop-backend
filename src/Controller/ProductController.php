<?php

namespace App\Controller;

use App\Entity\Product;
use Doctrine\ORM\EntityNotFoundException;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use App\Service\ProductService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use App\Exception\ValidationException;


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
     * @param Product $product
     * @param ConstraintViolationListInterface $validationErrors
     * @ParamConverter("product", converter="fos_rest.request_body")
     * @Rest\Post("/product")
     * @return View
     * @throws \Doctrine\ORM\ORMException
     */
    public function postProduct(Product $product, ConstraintViolationListInterface $validationErrors): View
    {

        if (count($validationErrors) > 0) {
            return View::create($validationErrors, Response::HTTP_BAD_REQUEST);
        }

        $product = $this->productService->addProduct($product);
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