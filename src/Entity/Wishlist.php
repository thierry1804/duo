<?php

namespace App\Entity;

use App\Repository\WishlistRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WishlistRepository::class)]
class Wishlist
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'wishlists')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $customer = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $deletedAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $checkedOutAt = null;

    #[ORM\OneToMany(targetEntity: WishlistLine::class, mappedBy: 'wishlist', orphanRemoval: true)]
    private Collection $wishlistLines;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $quotedAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $quotePrintedAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $expeditionMode = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $transitType = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $transit = null;

    #[ORM\Column(nullable: true)]
    private ?float $expeditionCost = null;

    #[ORM\Column(nullable: true)]
    private ?float $transitCost = null;

    #[ORM\Column(nullable: true)]
    private ?float $commission = null;

    public function __construct()
    {
        $this->wishlistLines = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCustomer(): ?User
    {
        return $this->customer;
    }

    public function setCustomer(?User $customer): static
    {
        $this->customer = $customer;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTimeImmutable $deletedAt): static
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    public function getCheckedOutAt(): ?\DateTimeImmutable
    {
        return $this->checkedOutAt;
    }

    public function setCheckedOutAt(?\DateTimeImmutable $checkedOutAt): static
    {
        $this->checkedOutAt = $checkedOutAt;

        return $this;
    }

    /**
     * @return Collection<int, WishlistLine>
     */
    public function getWishlistLines(): Collection
    {
        return $this->wishlistLines;
    }

    public function addWishlistLine(WishlistLine $wishlistLine): static
    {
        if (!$this->wishlistLines->contains($wishlistLine)) {
            $this->wishlistLines->add($wishlistLine);
            $wishlistLine->setWishlist($this);
        }

        return $this;
    }

    public function removeWishlistLine(WishlistLine $wishlistLine): static
    {
        if ($this->wishlistLines->removeElement($wishlistLine)) {
            // set the owning side to null (unless already changed)
            if ($wishlistLine->getWishlist() === $this) {
                $wishlistLine->setWishlist(null);
            }
        }

        return $this;
    }

    public function getQuotedAt(): ?\DateTimeImmutable
    {
        return $this->quotedAt;
    }

    public function setQuotedAt(?\DateTimeImmutable $quotedAt): static
    {
        $this->quotedAt = $quotedAt;

        return $this;
    }

    public function getQuotePrintedAt(): ?\DateTimeImmutable
    {
        return $this->quotePrintedAt;
    }

    public function setQuotePrintedAt(?\DateTimeImmutable $quotePrintedAt): static
    {
        $this->quotePrintedAt = $quotePrintedAt;

        return $this;
    }

    public function getExpeditionMode(): ?string
    {
        return $this->expeditionMode;
    }

    public function setExpeditionMode(?string $expeditionMode): static
    {
        $this->expeditionMode = $expeditionMode;

        return $this;
    }

    public function getTransitType(): ?string
    {
        return $this->transitType;
    }

    public function setTransitType(?string $transitType): static
    {
        $this->transitType = $transitType;

        return $this;
    }

    public function getTransit(): ?string
    {
        return $this->transit;
    }

    public function setTransit(?string $transit): static
    {
        $this->transit = $transit;

        return $this;
    }

    public function getExpeditionCost(): ?float
    {
        return $this->expeditionCost;
    }

    public function setExpeditionCost(?float $expeditionCost): static
    {
        $this->expeditionCost = $expeditionCost;

        return $this;
    }

    public function getTransitCost(): ?float
    {
        return $this->transitCost;
    }

    public function setTransitCost(?float $transitCost): static
    {
        $this->transitCost = $transitCost;

        return $this;
    }

    public function getCommission(): ?float
    {
        return $this->commission;
    }

    public function setCommission(?float $commission): static
    {
        $this->commission = $commission;

        return $this;
    }
}
