<?php declare(strict_types=1);

namespace Pricemotion\Shopware\Service\ScheduledTask;

use Pricemotion\Sdk\RuntimeException;
use Pricemotion\Shopware\Exception\ConfigurationException;
use Pricemotion\Shopware\Service\FollowProductsService;
use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskHandler;

class FollowProductsTaskHandler extends ScheduledTaskHandler {
    private $logger;

    private $followProducts;

    public function __construct(
        EntityRepositoryInterface $scheduledTaskRepository,
        LoggerInterface $logger,
        FollowProductsService $followProducts
    ) {
        parent::__construct($scheduledTaskRepository);
        $this->logger = $logger;
        $this->followProducts = $followProducts;
    }

    public static function getHandledMessages(): array {
        return [FollowProductsTask::class];
    }

    public function run(): void {
        try {
            $this->followProducts->followProducts();
        } catch (ConfigurationException $e) {
            return;
        } catch (RuntimeException $e) {
            $this->logger->error(
                sprintf(
                    'Caught %s while subscribing to products: (%d) %s',
                    get_class($e),
                    $e->getCode(),
                    $e->getMessage(),
                ),
            );
        }
    }
}
