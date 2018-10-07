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
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;


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
     * @SWG\Response(
     *     response=200,
     *     description="Returns a single product",
     *      @SWG\Schema(ref=@Model(type=Product::class))
     * )
     * @SWG\Tag(name="Product")
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
     * @return View
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns a list of products",
     *     @SWG\Schema(ref=@Model(type=Product::class))
     * )
     * @SWG\Tag(name="Product")
     */
    public function productList(): View {
        return View::create($this->productService->getAllProducts(), Response::HTTP_OK);
    }

    /**
     * Creates an Product resource
     * @param Product $product
     * @param ConstraintViolationListInterface $validationErrors
     * @ParamConverter("product", converter="fos_rest.request_body")
     * @Rest\Post("/product")
     * @return View
     *
     * @SWG\Tag(name="Product")
     * @SWG\Response(
     *     response=201,
     *     description="Creates a new product and return it directly after",
     *      @SWG\Schema(ref=@Model(type=Product::class))
     * )
     * @SWG\Parameter(
     * 		name="user",
     * 		in="body",
     * 		required=true,
     * 		@SWG\Schema(ref=@Model(type=Product::class)),
     * )
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
     * @param Product $product
     * @param ConstraintViolationListInterface $validationErrors
     * @ParamConverter("product", converter="fos_rest.request_body")
     * @return View
     * @throws \Doctrine\ORM\ORMException
     *
     * @SWG\Tag(name="Product")
     * @SWG\Response(
     *     response=200,
     *     description="Updates an existing product",
     *      @SWG\Schema(ref=@Model(type=Product::class))
     * )
     * @SWG\Parameter(
     * 		name="user",
     * 		in="body",
     * 		required=true,
     * 		@SWG\Schema(ref=@Model(type=Product::class)),
     * )
     */
    public function putProduct(int $productId, Product $product, ConstraintViolationListInterface $validationErrors): View
    {
        if (count($validationErrors) > 0) {
            return View::create($validationErrors, Response::HTTP_BAD_REQUEST);
        }

        $updatedProduct = $this->productService->updateProduct($productId, $product);
        if (!$updatedProduct) {
            throw new EntityNotFoundException('Product with id '.$productId.' does not exist!');
        }

        return View::create($updatedProduct, Response::HTTP_OK);
    }

    /**
     * Removes a Product resource
     * @Rest\Delete("/product/{productId}")
     * @param int $productId
     * @return View
     *
     * @SWG\Tag(name="Product")
     * @SWG\Response(
     *     response=204,
     *     description="Deletes the specified product",
     *      @SWG\Schema(ref=@Model(type=Product::class))
     * )
     */
    public function deleteProduct(int $productId): View
    {
        $this->productService->deleteProduct($productId);
        return View::create([], Response::HTTP_NO_CONTENT);
    }
}