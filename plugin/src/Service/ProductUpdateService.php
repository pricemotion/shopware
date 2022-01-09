<?php declare(strict_types=1);

namespace Pricemotion\Shopware\Service;

use Pricemotion\Sdk\Data\Product;
use Pricemotion\Sdk\Product\Settings;
use Pricemotion\Shopware\Extension\Content\Product\PricemotionProductEntity;
use Pricemotion\Shopware\Extension\Content\Product\PricemotionProductExtension;
use Pricemotion\Shopware\SdkBridge\ProductAdapter;
use Psr\Log\LoggerInterface;
use Shopware\Core\Checkout\Cart\Price\NetPriceCalculator;
use Shopware\Core\Checkout\Cart\Price\Struct\QuantityPriceDefinition;
use Shopware\Core\Checkout\Cart\Tax\Struct\TaxRule;
use Shopware\Core\Checkout\Cart\Tax\Struct\TaxRuleCollection;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Pricing\CashRoundingConfig;
use Shopware\Core\Framework\DataAbstractionLayer\Pricing\Price;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;

class ProductUpdateService {
    private EntityRepositoryInterface $productRepository;

    private LoggerInterface $logger;

    private NetPriceCalculator $netPriceCalculator;

    public function __construct(
        EntityRepositoryInterface $productRepository,
        LoggerInterface $logger,
        NetPriceCalculator $netPriceCalculator
    ) {
        $this->productRepository = $productRepository;
        $this->logger = $logger;
        $this->netPriceCalculator = $netPriceCalculator;
    }

    public function updateProducts(Product $pricemotionProduct): void {
        $criteria = new Criteria();
        $criteria->addFilter(
            new EqualsFilter(
                sprintf('%s.%s', PricemotionProductExtension::NAME, 'ean'),
                $pricemotionProduct->getEan()->toString(),
            ),
        );
        // @phan-suppress-next-line PhanAccessMethodInternal
        $products = $this->productRepository->search($criteria, Context::createDefaultContext());
        foreach ($products as $productEntity) {
            $this->updateProduct($productEntity, $pricemotionProduct);
        }
    }

    private function updateProduct(ProductEntity $productEntity, Product $pricemotionProduct): void {
        $pricemotionExtension = $productEntity->getExtension('pricemotion');
        if (!$pricemotionExtension instanceof PricemotionProductEntity) {
            return;
        }
        $settings = $pricemotionExtension->getSettings();
        if (empty($settings)) {
            return;
        }
        $settings = Settings::fromArray($settings);
        $settings->setLogger($this->logger);
        $newPrice = $settings->getNewPrice(new ProductAdapter($productEntity), $pricemotionProduct);
        if ($newPrice === null) {
            return;
        }
        $priceCollection = $productEntity->getPrice();
        $currencyPrice = $priceCollection->getCurrencyPrice(Defaults::CURRENCY);
        if (!$currencyPrice) {
            $currencyPrice = new Price(
                Defaults::CURRENCY,
                $this->getNetPrice($productEntity, $newPrice),
                $newPrice,
                true,
            );
            $priceCollection->add($currencyPrice);
        } else {
            $currencyPrice->setNet($this->getNetPrice($productEntity, $newPrice));
            $currencyPrice->setGross($newPrice);
            $currencyPrice->setLinked(true);
        }
        // @phan-suppress-next-line PhanAccessMethodInternal
        $context = Context::createDefaultContext();
        $this->productRepository->upsert(
            [
                [
                    'id' => $productEntity->getId(),
                    'price' => $priceCollection,
                ],
            ],
            $context,
        );
    }

    private function getNetPrice(ProductEntity $productEntity, float $gross): float {
        $taxRules = new TaxRuleCollection([new TaxRule($productEntity->getTax()->getTaxRate())]);
        $quantityPriceDefinition = new QuantityPriceDefinition($gross, $taxRules, 1);
        $config = new CashRoundingConfig(50, 0.01, true);
        $net = $this->netPriceCalculator->calculate($quantityPriceDefinition, $config);
        return $net->getUnitPrice();
    }
}
