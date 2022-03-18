<?php declare(strict_types=1);

namespace Pricemotion\Shopware\Service\ScheduledTask;

use Pricemotion\Shopware\Service\WebhookService;
use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskHandler;

class RegisterWebhookTaskHandler extends ScheduledTaskHandler {
    private WebhookService $webhookService;

    private LoggerInterface $logger;

    public function __construct(
        EntityRepositoryInterface $scheduledTaskRepository,
        WebhookService $webhookService,
        LoggerInterface $logger
    ) {
        parent::__construct($scheduledTaskRepository);
        $this->webhookService = $webhookService;
        $this->logger = $logger;
    }

    public static function getHandledMessages(): array {
        return [RegisterWebhookTask::class];
    }

    public function run(): void {
        try {
            $this->webhookService->registerWebhook();
        } catch (\Exception $e) {
            $this->logger->error(
                sprintf(
                    'Could not register Pricemotion webhook: Caught %s: (%s) %s',
                    get_class($e),
                    $e->getCode(),
                    $e->getMessage(),
                ),
            );
        }
    }
}
