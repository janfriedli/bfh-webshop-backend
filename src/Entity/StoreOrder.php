<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;
/**
 * @ORM\Entity(repositoryClass="App\Repository\OrderRepository")
 * @JMS\VirtualProperty(
 *     "storeOrderToProduct",
 *     exp="object.getProductsInApiFormat()",
 *     options={@JMS\SerializedName("products")}
 *  )
 */
class StoreOrder
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=255)
     */
    private $street;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=255)
     */
    private $zip;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=255)
     */
    private $fullname;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=255)
     */
    private $country;

    /**
     * that (public) field would not make sense in a real environment but for the sake of that project at the bfh...
     * @ORM\Column(type="boolean")
     */
    private $paid;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\StoreOrderToProduct", mappedBy="storeOrder", cascade={"All"})
     * @ORM\JoinColumn(nullable=true)
     * @JMS\SerializedName("products")
     */
    private $storeOrderToProduct;


    public function __construct() {
        $this->storeOrderToProduct = new \Doctrine\Common\Collections\ArrayCollection();
    }


    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(string $street): self
    {
        $this->street = $street;

        return $this;
    }

    public function getZip(): ?string
    {
        return $this->zip;
    }

    public function setZip(string $zip): self
    {
        $this->zip = $zip;

        return $this;
    }

    public function getFullname(): ?string
    {
        return $this->fullname;
    }

    public function setFullname(string $fullname): self
    {
        $this->fullname = $fullname;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getPaid(): ?bool
    {
        return $this->paid;
    }

    public function setPaid(bool $paid): self
    {
        $this->paid = $paid;

        return $this;
    }

    public function getProducts()
    {
        return $this->storeOrderToProduct;
    }

    /**
     * return ONLY the products
     * @return ArrayCollection
     */
    public function getProductsInApiFormat()
    {
        $products = new ArrayCollection();
        foreach ($this->storeOrderToProduct as $orderToProduct) {
            $products->add($orderToProduct->getProduct());
        }

        return $products;
    }

    public function setProducts($products): void
    {
        foreach ($products as $product) {
            $this->addProduct($product);
        }
    }

    public function addProduct(Product $product)
    {
        $storeOrderProduct = new StoreOrderToProduct();
        $storeOrderProduct->setProduct($product);
        $storeOrderProduct->setStoreOrder($this);
        $this->storeOrderToProduct->add($storeOrderProduct);
    }

}
