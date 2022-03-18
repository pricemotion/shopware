<?php declare(strict_types=1);

namespace Pricemotion\Shopware\Service\ScheduledTask;

use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTask;

class FollowProductsTask extends ScheduledTask {
    public static function getTaskName(): string {
        return 'pricemotion.follow_products';
    }

    public static function getDefaultInterval(): int {
        return 86400;
    }
}
