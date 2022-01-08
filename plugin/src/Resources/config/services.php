<?php declare(strict_types=1);

namespace Pricemotion\Shopware;

use Pricemotion\Shopware\Command\TestCommand;
use Pricemotion\Shopware\Controller\ApiController;
use Pricemotion\Shopware\Extension\Content\Product\PricemotionProductDefinition;
use Pricemotion\Shopware\Extension\Content\Product\PricemotionProductExtension;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

return function (ContainerConfigurator $configurator): void {
    $services = $configurator
        ->services()
        ->defaults()
        ->tag('monolog.logger', ['channel' => 'pricemotion'])
        ->autowire();

    $services->instanceof(EventSubscriberInterface::class)->tag('kernel.event_subscriber');
    $services->instanceof(MessageHandlerInterface::class)->tag('messenger.message_handler');

    $services->load('Pricemotion\\Shopware\\', dirname(__DIR__, 2) . '/*');

    $services->set(PricemotionProductExtension::class)->tag('shopware.entity.extension');
    $services->set(PricemotionProductDefinition::class)->tag('shopware.entity.definition');

    $services->set(ApiController::class)->public();

    /** @phan-suppress-next-line PhanUndeclaredClassReference */
    $services->set(TestCommand::class)->tag('console.command');
};
