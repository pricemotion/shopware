<?php declare(strict_types=1);

namespace Pricemotion\Shopware\MessageQueue\Message;

class ProductWrittenMessage {
    private string $productId;

    public function __construct(string $productId) {
        $this->productId = $productId;
    }

    public function getProductId(): string {
        return $this->productId;
    }
}
