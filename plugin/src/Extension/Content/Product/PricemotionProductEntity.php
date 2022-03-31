<?php declare(strict_types=1);

namespace Pricemotion\Shopware\Extension\Content\Product;

use Pricemotion\Sdk\Data\Ean;
use Pricemotion\Sdk\InvalidArgumentException;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;

class PricemotionProductEntity extends Entity {
    protected string $productId;

    protected ?ProductEntity $product;

    protected ?string $ean;

    protected ?array $settings;

    protected ?\DateTimeImmutable $appliedAt;

    protected ?float $lowestPrice;

    protected ?\DateTimeImmutable $refreshedAt;

    public function getProductId(): string {
        return $this->productId;
    }

    public function getEan(): ?Ean {
        if ($this->ean === null) {
            return null;
        }
        try {
            return Ean::fromString($this->ean);
        } catch (InvalidArgumentException $e) {
            return null;
        }
    }

    public function getSettings(): ?array {
        return $this->settings;
    }

    public function getAppliedAt(): ?\DateTimeImmutable {
        return $this->appliedAt;
    }

    public function getLowestPrice(): ?float {
        return $this->lowestPrice;
    }

    public function getRefreshedAt(): ?\DateTimeImmutable {
        return $this->refreshedAt;
    }
}
