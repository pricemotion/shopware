<?php declare(strict_types=1);

namespace Pricemotion\Shopware\Subscriber;

use Pricemotion\Shopware\Extension\Content\Product\PricemotionProductDefinition;
use Pricemotion\Shopware\MessageQueue\Message\ProductWrittenMessage;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenContainerEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class ProductWriteSubscriber implements EventSubscriberInterface {
    private MessageBusInterface $bus;

    private int $disabled = 0;

    public function __construct(MessageBusInterface $bus) {
        $this->bus = $bus;
    }

    public static function getSubscribedEvents(): array {
        return [
            EntityWrittenEvent::class => 'onEntityWritten',
            EntityWrittenContainerEvent::class => 'onEntitiesWritten',
        ];
    }

    public function onEntityWritten(EntityWrittenEvent $entityWrittenEvent): void {
        foreach ($this->getProductIds($entityWrittenEvent) as $productId) {
            $this->dispatchMessageForProductId($productId);
        }
    }

    private function getProductIds(EntityWrittenEvent $entityWrittenEvent): \Generator {
        if ($entityWrittenEvent->getEntityName() === PricemotionProductDefinition::ENTITY_NAME) {
            yield from $entityWrittenEvent->getIds();
        }
    }

    private function dispatchMessageForProductId(string $productId): void {
        if ($this->disabled) {
            return;
        }
        $this->bus->dispatch(new ProductWrittenMessage($productId));
    }

    public function onEntitiesWritten(EntityWrittenContainerEvent $containerEvent): void {
        foreach ($containerEvent->getEvents() as $event) {
            if ($event instanceof EntityWrittenEvent) {
                $this->onEntityWritten($event);
            }
        }
    }

    public function quietly(\Closure $fn) {
        $this->disabled++;

        try {
            return $fn();
        } finally {
            $this->disabled--;
        }
    }
}
