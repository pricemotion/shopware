<?php declare(strict_types=1);

namespace Pricemotion\Shopware\Extension\Content\Product;

use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;

class PricemotionProductEntity extends Entity {
    protected string $productId;

    protected ?ProductEntity $product;

    protected ?\DateTimeImmutable $appliedAt;

    protected ?array $settings;

    public function getProductId(): string {
        return $this->productId;
    }

    public function getAppliedAt(): ?\DateTimeImmutable {
        return $this->appliedAt;
    }

    public function getSettings(): ?array {
        return $this->settings;
    }
}