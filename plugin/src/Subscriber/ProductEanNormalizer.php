<?php declare(strict_types=1);

namespace Pricemotion\Shopware\Subscriber;

use Pricemotion\Sdk\Data\Ean;
use Pricemotion\Sdk\InvalidArgumentException;
use Pricemotion\Shopware\Extension\Content\Product\PricemotionProductEntity;
use Pricemotion\Shopware\Extension\Content\Product\PricemotionProductExtension;
use Psr\Log\LoggerInterface;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenContainerEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProductEanNormalizer implements EventSubscriberInterface {
    private EntityRepositoryInterface $productRepository;

    private bool $inHandler = false;

    private $logger;

    public function __construct(EntityRepositoryInterface $productRepository, LoggerInterface $logger) {
        $this->productRepository = $productRepository;
        $this->logger = $logger;
    }

    public static function getSubscribedEvents(): array {
        return [
            EntityWrittenEvent::class => 'onEntityWritten',
            EntityWrittenContainerEvent::class => 'onEntitiesWritten',
        ];
    }

    public function onEntityWritten(EntityWrittenEvent $entityWrittenEvent): void {
        if ($this->inHandler) {
            return;
        }
        if ($entityWrittenEvent->getEntityName() !== 'product') {
            return;
        }
        try {
            $this->inHandler = true;
            foreach ($entityWrittenEvent->getIds() as $productId) {
                $this->updateProduct($productId, $entityWrittenEvent->getContext());
            }
        } finally {
            $this->inHandler = false;
        }
    }

    private function updateProduct(string $productId, Context $context): void {
        $criteria = new Criteria();
        $criteria->setIds([$productId]);
        $product = $this->productRepository->search($criteria, $context)->first();
        if (!$product instanceof ProductEntity) {
            return;
        }
        try {
            $ean = Ean::fromString((string) $product->getEan());
        } catch (InvalidArgumentException $e) {
            $ean = null;
        }
        $pricemotionExtension = $product->getExtension(PricemotionProductExtension::NAME);
        if (
            $pricemotionExtension instanceof PricemotionProductEntity &&
            (string) $pricemotionExtension->getEan() === (string) $ean
        ) {
            return;
        }
        $this->logger->debug(sprintf('Setting EAN and resetting data for product %s', $product->getId()));
        $this->productRepository->upsert(
            [
                [
                    'id' => $product->getId(),
                    PricemotionProductExtension::NAME => [
                        'ean' => $ean ? $ean->toString() : null,
                        'appliedAt' => null,
                        'lowestPrice' => null,
                        'refreshedAt' => null,
                    ],
                ],
            ],
            $context,
        );
    }

    public function onEntitiesWritten(EntityWrittenContainerEvent $containerEvent): void {
        foreach ($containerEvent->getEvents() as $event) {
            if ($event instanceof EntityWrittenEvent) {
                $this->onEntityWritten($event);
            }
        }
    }
}
