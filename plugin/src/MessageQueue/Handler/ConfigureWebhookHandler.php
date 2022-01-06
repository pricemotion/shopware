<?php declare(strict_types=1);

namespace Pricemotion\Shopware\MessageQueue\Handler;

use Pricemotion\Shopware\MessageQueue\Message\ApiKeyChangedMessage;
use Shopware\Core\Framework\MessageQueue\Handler\AbstractMessageHandler;

class ConfigureWebhookHandler extends AbstractMessageHandler {
    public function handle($message): void {

    }

    public static function getHandledMessages(): iterable {
        return [ApiKeyChangedMessage::class];
    }
}