<?php declare(strict_types=1);

namespace Pricemotion\Shopware\Service\ScheduledTask;

use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTask;

class RegisterWebhookTask extends ScheduledTask {
    public static function getTaskName(): string {
        return 'pricemotion.register_webhook';
    }

    public static function getDefaultInterval(): int {
        return 86400;
    }
}
