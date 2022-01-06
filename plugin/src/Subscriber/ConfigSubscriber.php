<?php declare(strict_types=1);

namespace Pricemotion\Shopware\Subscriber;

use Pricemotion\Shopware\KiboPricemotion;
use Pricemotion\Shopware\MessageQueue\Message\ApiKeyChangedMessage;
use Shopware\Core\System\SystemConfig\Event\SystemConfigChangedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class ConfigSubscriber implements EventSubscriberInterface {
    private MessageBusInterface $bus;

    public function __construct(MessageBusInterface $bus) {
        $this->bus = $bus;
    }

    public static function getSubscribedEvents(): array {
        return [
            SystemConfigChangedEvent::class => 'onConfigChanged',
        ];
    }

    public function onConfigChanged(SystemConfigChangedEvent $event): void {
        if ($event->getKey() !== KiboPricemotion::CONFIG_API_KEY) {
            return;
        }
        if (empty($event->getValue())) {
            return;
        }
        $this->bus->dispatch(new ApiKeyChangedMessage());
    }
}
