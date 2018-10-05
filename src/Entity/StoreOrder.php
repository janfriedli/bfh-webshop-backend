<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;
/**
 * @ORM\Entity(repositoryClass="App\Repository\OrderRepository")
 * @JMS\AccessType("public_method")
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
     * @ORM\OneToMany(targetEntity="App\Entity\OrderDetail", mappedBy="storeOrder", cascade={"all"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $details;

    public function __construct()
    {
        $this->details = new ArrayCollection();
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

    /**
     * @return Collection|OrderDetail[]
     */
    public function getDetails(): Collection
    {
        return $this->details;
    }

    public function setDetails(Collection $details): self
    {
        foreach ($details as $detail) {
            $this->addDetail($detail);
        }

        return $this;
    }

    public function addDetail(OrderDetail $detail): self
    {
        // strange hack but it seems that details aren't initialized when this method is called...
        // even tough we init it in the constructor
        if ($this->details == null) {
            $this->details = new ArrayCollection();
        }

        if (!$this->details->contains($detail)) {
            $this->details[] = $detail;
            $detail->setStoreOrder($this);
        }

        return $this;
    }

    public function removeDetail(OrderDetail $detail): self
    {
        if ($this->details->contains($detail)) {
            $this->details->removeElement($detail);
            // set the owning side to null (unless already changed)
            if ($detail->getStoreOrder() === $this) {
                $detail->setStoreOrder(null);
            }
        }

        return $this;
    }
}
