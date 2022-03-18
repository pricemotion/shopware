<?php declare(strict_types=1);

namespace Pricemotion\Shopware\Service;

use Pricemotion\Sdk\Api\Client;
use Pricemotion\Sdk\Data\EanCollection;
use Pricemotion\Sdk\PriceRule\Disabled;
use Pricemotion\Sdk\Product\Settings;
use Pricemotion\Shopware\Extension\Content\Product\PricemotionProductEntity;
use Pricemotion\Shopware\Extension\Content\Product\PricemotionProductExtension;
use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;

class FollowProductsService {
    private $config;

    private $logger;

    private $productRepository;

    public function __construct(
        ConfigService $config,
        LoggerInterface $logger,
        EntityRepositoryInterface $productRepository
    ) {
        $this->config = $config;
        $this->logger = $logger;
        $this->productRepository = $productRepository;
    }

    public function followProducts(): void {
        $client = new Client($this->config->getApiKey());
        $eans = $this->getEansToFollow();
        $this->logger->info('Subscribing to EANs: ' . implode(', ', $eans->toArray()));
        $client->followProducts($eans);
    }

    private function getEansToFollow(): EanCollection {
        $eans = [];
        foreach ($this->getAllProducts() as $product) {
            $pricemotion = $product->getExtension(PricemotionProductExtension::NAME);
            if (!$pricemotion instanceof PricemotionProductEntity) {
                continue;
            }
            $settings = $pricemotion->getSettings();
            if (!$settings) {
                continue;
            }
            $settings = Settings::fromArray($settings);
            if ($settings->getPriceRule() instanceof Disabled) {
                continue;
            }
            $ean = $pricemotion->getEan();
            if (!$ean) {
                continue;
            }
            $eans[] = $ean;
        }
        return new EanCollection($eans);
    }

    private function getAllProducts(): \Generator {
        /** @var \Shopware\Core\Content\Product\ProductEntity $item */
        $item = null;
        do {
            $criteria = new Criteria();
            $criteria->setLimit(1); // TODO -- Bump limit
            $criteria->addSorting(new FieldSorting('id'));
            if ($item) {
                $criteria->addFilter(new RangeFilter('id', [RangeFilter::GT => $item->getId()]));
            }
            /** @phan-suppress-next-line PhanAccessMethodInternal */
            $result = $this->productRepository->search($criteria, Context::createDefaultContext())->getEntities();
            foreach ($result as $item) {
                yield $item;
            }
        } while ($result);
    }
}
