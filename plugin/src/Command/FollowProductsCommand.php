<?php declare(strict_types=1);

namespace Pricemotion\Shopware\Command;

use Pricemotion\Shopware\Service\FollowProductsService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FollowProductsCommand extends Command {
    protected static $defaultName = 'pricemotion:follow-products';

    protected static $defaultDescription = 'Subscribe to updates from Pricemotion for products that have price rules';

    private FollowProductsService $followProducts;

    public function __construct(FollowProductsService $followProducts) {
        parent::__construct();
        $this->followProducts = $followProducts;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $this->followProducts->followProducts();

        return 0;
    }
}
