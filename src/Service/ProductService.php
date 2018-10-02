<?php
namespace App\Service;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

final class ProductService
{
    /**
     * @var ServiceEntityRepository
     */
    private $productRepository;

    /**
     * @var CRUDService $crud
     */
    private $crud;

    /**
     * ProductService constructor.
     * @param ProductRepository $productRepository
     * @param CRUDService $crud
     */
    public function __construct(ProductRepository $productRepository, CRUDService $crud){
        $this->productRepository = $productRepository;
        $this->crud = $crud;
    }

    /**
     * @param int $productId
     * @return null|Product
     */
    public function getProduct(int $productId): ?Product
    {
        return $this->productRepository->findOneById($productId);
    }

    /**
     * @return array|null
     */
    public function getAllProducts(): ?array
    {
        return $this->productRepository->findAll();
    }

    /**
     * @param Product $product
     * @return object
     */
    public function addProduct(Product $product)
    {
        $product = $this->crud->save($product);
        return $product;
    }

    /**
     * @param int $productId
     * @param Product $updatedProduct
     * @return null|object
     */
    public function updateProduct(int $productId, $updatedProduct)
    {
        $product = $this->productRepository->findOneById($productId);
        if (!$product) {
            return null;
        }

        return $this->crud->update($productId, $updatedProduct);
    }

    /**
     * @param int $productId
     */
    public function deleteProduct(int $productId): void
    {
        $product = $this->productRepository->findOneById($productId);
        if ($product) {
            $this->crud->delete($product);
        }
    }

}