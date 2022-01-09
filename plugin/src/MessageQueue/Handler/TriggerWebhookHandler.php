<?php declare(strict_types=1);

namespace Pricemotion\Shopware\MessageQueue\Handler;

use Pricemotion\Shopware\Exception\ConfigurationException;
use Pricemotion\Shopware\Extension\Content\Product\PricemotionProductEntity;
use Pricemotion\Shopware\Extension\Content\Product\PricemotionProductExtension;
use Pricemotion\Shopware\MessageQueue\Message\ProductWrittenMessage;
use Pricemotion\Shopware\Service\WebhookService;
use Psr\Log\LoggerInterface;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\MessageQueue\Handler\AbstractMessageHandler;

class TriggerWebhookHandler extends AbstractMessageHandler {
    private WebhookService $webhookService;

    private LoggerInterface $logger;

    private EntityRepositoryInterface $productRepository;

    public function __construct(
        WebhookService $webhookService,
        LoggerInterface $logger,
        EntityRepositoryInterface $productRepository
    ) {
        $this->webhookService = $webhookService;
        $this->logger = $logger;
        $this->productRepository = $productRepository;
    }

    public function handle($message): void {
        if ($message instanceof ProductWrittenMessage) {
            $this->onProductWritten($message);
        }
    }

    private function onProductWritten(ProductWrittenMessage $message): void {
        $product = $this->getProductById($message->getProductId());
        if (!$product) {
            return;
        }
        $pricemotion = $product->getExtension(PricemotionProductExtension::NAME);
        if (!$pricemotion instanceof PricemotionProductEntity) {
            return;
        }
        if (!$pricemotion->getEan()) {
            return;
        }
        try {
            $this->webhookService->trigger($ean);
            $this->logger->info(sprintf('Triggered Pricemotion webhook for EAN: %s', $ean));
        } catch (ConfigurationException $e) {
            $this->logger->warning(
                sprintf('Pricemotion webhook was not triggered due to configuration issue: %s', $e->getMessage()),
            );
        }
    }

    private function getProductById(string $productId): ?ProductEntity {
        $criteria = new Criteria();
        $criteria->setIds([$productId]);
        /** @phan-suppress-next-line PhanAccessMethodInternal */
        return $this->productRepository->search($criteria, Context::createDefaultContext())->first();
    }

    public static function getHandledMessages(): iterable {
        return [ProductWrittenMessage::class];
    }
}
