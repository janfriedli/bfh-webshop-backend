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
     * ProductService constructor.
     * @param ProductRepository $productRepository
     */
    public function __construct(ProductRepository $productRepository){
        $this->productRepository = $productRepository;
    }

    /**
     * @param int $productId
     * @return null|Product
     */
    public function getProduct(int $productId): ?Product
    {
        return $this->productRepository->findById($productId);
    }

    /**
     * @return array|null
     */
    public function getAllProducts(): ?array
    {
        return $this->productRepository->findAll();
    }

    /**
     * @param string $title
     * @param string $content
     * @throws \Doctrine\ORM\ORMException
     * @return Product
     */
    public function addProduct(string $title, string $content): Product
    {
        $product = new Product();
        $product->setTitle($title);
        $product->setDescription($content);
        $product = $this->productRepository->save($product);
        return $product;
    }

    /**
     * @param int $productId
     * @param string $title
     * @param string $description
     * @throws \Doctrine\ORM\ORMException
     * @return null|Product
     */
    public function updateProduct(int $productId, string $title, string $description): ?Product
    {
        $product = $this->productRepository->findOneById($productId);
        if (!$product) {
            return null;
        }
        $product->setTitle($title);
        $product->setDescription($description);
        return $this->productRepository->save($product);
    }

    /**
     * @param int $productId
     */
    public function deleteProduct(int $productId): void
    {
        $product = $this->productRepository->findOneById($productId);
        if ($product) {
            $this->productRepository->delete($product);
        }
    }

}