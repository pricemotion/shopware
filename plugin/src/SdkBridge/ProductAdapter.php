<?php declare(strict_types=1);

namespace Pricemotion\Shopware\SdkBridge;

use Pricemotion\Sdk\Product\ProductInterface;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Defaults;

class ProductAdapter implements ProductInterface {
    private ProductEntity $productEntity;

    public function __construct(ProductEntity $productEntity) {
        $this->productEntity = $productEntity;
    }

    public function getId(): string {
        return $this->productEntity->getId();
    }

    public function getPrice(): ?float {
        $price = $this->productEntity->getCurrencyPrice(Defaults::CURRENCY);
        if (!$price) {
            return null;
        }
        return $price->getGross();
    }

    public function getCostPrice(): ?float {
        $prices = $this->productEntity->getPurchasePrices();
        if (!$prices) {
            return null;
        }
        $price = $prices->getCurrencyPrice(Defaults::CURRENCY);
        if (!$price) {
            return null;
        }
        return $price->getGross();
    }

    public function getListPrice(): ?float {
        $price = $this->productEntity->getCurrencyPrice(Defaults::CURRENCY);
        if (!$price) {
            return null;
        }
        $price = $price->getListPrice();
        if (!$price) {
            return null;
        }
        return $price->getGross();
    }
}
