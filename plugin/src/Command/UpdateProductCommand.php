<?php declare(strict_types=1);

namespace Pricemotion\Shopware\Command;

use Pricemotion\Sdk\Api\Client;
use Pricemotion\Sdk\Data\Ean;
use Pricemotion\Shopware\Service\ConfigService;
use Pricemotion\Shopware\Service\ProductUpdateService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateProductCommand extends Command {
    protected static $defaultName = 'pricemotion:update-product';

    protected static $defaultDescription = 'Manually update a product by EAN';

    private $config;

    private $productUpdateService;

    public function __construct(ConfigService $config, ProductUpdateService $productUpdateService) {
        parent::__construct();

        $this->config = $config;
        $this->productUpdateService = $productUpdateService;
    }

    protected function configure(): void {
        parent::configure();

        $this->addArgument('ean', InputArgument::REQUIRED, 'The EAN of the product to update.');
    }

    public function execute(InputInterface $input, OutputInterface $output): int {
        $ean = Ean::fromString($input->getArgument('ean'));
        $client = new Client($this->config->getApiKey());
        $product = $client->getProduct($ean);
        $this->productUpdateService->updateProducts($product);

        return 0;
    }
}
