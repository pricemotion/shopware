<?php declare(strict_types=1);

namespace Pricemotion\Shopware\MessageQueue\Handler;

use Pricemotion\Shopware\Exception\ConfigurationException;
use Pricemotion\Shopware\MessageQueue\Message\ApiKeyChangedMessage;
use Pricemotion\Shopware\Service\WebhookService;
use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\MessageQueue\Handler\AbstractMessageHandler;

class ConfigureWebhookHandler extends AbstractMessageHandler {
    private WebhookService $webhookService;

    private LoggerInterface $logger;

    public function __construct(WebhookService $webhookService, LoggerInterface $logger) {
        $this->webhookService = $webhookService;
        $this->logger = $logger;
    }

    public function handle($message): void {
        try {
            $this->webhookService->registerWebhook();
        } catch (ConfigurationException $e) {
            $this->logger->info(
                sprintf('Pricemotion webhook was not configured due to configuration issue: %s', $e->getMessage()),
            );
        }
    }

    public static function getHandledMessages(): iterable {
        return [ApiKeyChangedMessage::class];
    }
}
