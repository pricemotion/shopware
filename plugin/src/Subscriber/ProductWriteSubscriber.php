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

    private $handledProductIds = [];

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
        if ($entityWrittenEvent->getEntityName() === 'product') {
            yield from $entityWrittenEvent->getIds();
        } elseif ($entityWrittenEvent->getEntityName() === PricemotionProductDefinition::ENTITY_NAME) {
            foreach ($entityWrittenEvent->getPayloads() as $payload) {
                if (isset($payload['productId'])) {
                    yield (string) $payload['productId'];
                }
            }
        }
    }

    private function dispatchMessageForProductId(string $productId): void {
        if (isset($this->handledProductIds[$productId])) {
            return;
        }
        $this->bus->dispatch(new ProductWrittenMessage($productId));
        $this->handledProductIds[$productId] = true;
    }

    public function onEntitiesWritten(EntityWrittenContainerEvent $containerEvent): void {
        foreach ($containerEvent->getEvents() as $event) {
            if ($event instanceof EntityWrittenEvent) {
                $this->onEntityWritten($event);
            }
        }
    }
}
