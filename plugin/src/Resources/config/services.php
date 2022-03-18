<?php declare(strict_types=1);

namespace Pricemotion\Shopware;

use Pricemotion\Shopware\Extension\Content\Product\PricemotionProductDefinition;
use Pricemotion\Shopware\Extension\Content\Product\PricemotionProductExtension;
use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTask;
use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Command\Command;
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
    $services->instanceof(Command::class)->tag('console.command');
    $services->instanceof(ScheduledTask::class)->tag('shopware.scheduled.task');
    $services->instanceof(ScheduledTaskHandler::class)->tag('messenger.message_handler');

    $services->instanceof(AbstractController::class)->public();

    $services->load('Pricemotion\\Shopware\\', '../src/*')->exclude('../src/Resources/*');

    $services->set(PricemotionProductExtension::class)->tag('shopware.entity.extension');
    $services->set(PricemotionProductDefinition::class)->tag('shopware.entity.definition');
};
