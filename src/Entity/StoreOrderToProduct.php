<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 */
class StoreOrderToProduct
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Product", inversedBy="storeOrderToProduct")
     */
    private $product;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\StoreOrder", inversedBy="storeOrderToProduct")
     */
    private $storeOrder;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param mixed $product
     */
    public function setProduct($product): void
    {
        $this->product = $product;
    }

    /**
     * @return mixed
     */
    public function getStoreOrder()
    {
        return $this->storeOrder;
    }

    /**
     * @param mixed $storeOrder
     */
    public function setStoreOrder($storeOrder): void
    {
        $this->storeOrder = $storeOrder;
    }

}
