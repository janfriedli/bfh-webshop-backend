<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Product
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
    private $title;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @Assert\Url()
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=255)
     */
    private $imgUrl;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="float")
     */
    private $price;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="integer")
     */
    private $quantity;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\OrderDetail", mappedBy="product", cascade={"detach"})
     * @JMS\Exclude()
     */
    private $orderDetails;

    public function __construct() {
        $this->orderDetails = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getImgUrl(): ?string
    {
        return $this->imgUrl;
    }

    public function setImgUrl(string $imgUrl): self
    {
        $this->imgUrl = $imgUrl;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getOrderDetails(): ArrayCollection
    {
        return $this->orderDetails;
    }

    public function setOrderDetails($orderDetails): self
    {
        $this->orderDetails = $orderDetails;
        return $this;
    }

    public function addOrderDetails(OrderDetail $orderDetail): self
    {
        if (!$this->orderDetails) {
            $this->orderDetails = new ArrayCollection();
        }

        $this->orderDetails->add($orderDetail);


        return $this;
    }

    /**
     * escape img Url
     * @ORM\PrePersist
     */
    public function escapeImgUrl()
    {
        $this->imgUrl = htmlspecialchars($this->getImgUrl());
    }
}
