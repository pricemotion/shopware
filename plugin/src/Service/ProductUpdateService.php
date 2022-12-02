<?php declare(strict_types=1);

namespace Pricemotion\Shopware\Service;

use Pricemotion\Sdk\Data\Product;
use Pricemotion\Sdk\Product\Settings;
use Pricemotion\Shopware\Extension\Content\Product\PricemotionProductEntity;
use Pricemotion\Shopware\Extension\Content\Product\PricemotionProductExtension;
use Pricemotion\Shopware\SdkBridge\ProductAdapter;
use Pricemotion\Shopware\Subscriber\ProductWriteSubscriber;
use Psr\Log\LoggerInterface;
use Shopware\Core\Checkout\Cart\Price\GrossPriceCalculator;
use Shopware\Core\Checkout\Cart\Price\Struct\CalculatedPrice;
use Shopware\Core\Checkout\Cart\Price\Struct\QuantityPriceDefinition;
use Shopware\Core\Checkout\Cart\Tax\Struct\TaxRule;
use Shopware\Core\Checkout\Cart\Tax\Struct\TaxRuleCollection;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Pricing\CashRoundingConfig;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;

class ProductUpdateService {
    private EntityRepositoryInterface $productRepository;

    private LoggerInterface $logger;

    private GrossPriceCalculator $grossPriceCalculator;

    private ProductWriteSubscriber $productWriteSubscriber;

    public function __construct(
        EntityRepositoryInterface $productRepository,
        LoggerInterface $logger,
        GrossPriceCalculator $grossPriceCalculator,
        ProductWriteSubscriber $productWriteSubscriber
    ) {
        $this->productRepository = $productRepository;
        $this->logger = $logger;
        $this->grossPriceCalculator = $grossPriceCalculator;
        $this->productWriteSubscriber = $productWriteSubscriber;
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
        $this->logger->info(
            sprintf('Found %d products matching EAN %s', $products->count(), $pricemotionProduct->getEan()),
        );
        foreach ($products as $productEntity) {
            if (!$productEntity instanceof ProductEntity) {
                throw new \LogicException('Product repository should return ProductEntity instances');
            }
            $this->updateLowestPrice($productEntity, $pricemotionProduct);
            $this->applyPriceRules($productEntity, $pricemotionProduct);
        }
    }

    private function updateLowestPrice(ProductEntity $productEntity, Product $pricemotionProduct): void {
        $this->logger->debug(sprintf('Updating lowest price on product %s', $productEntity->getId()));
        $pricemotion = $productEntity->getExtension(PricemotionProductExtension::NAME);
        if (
            $pricemotion instanceof PricemotionProductEntity &&
            $pricemotion->getRefreshedAt() &&
            ($lowestPrice = $pricemotion->getLowestPrice()) !== null &&
            abs($lowestPrice - $pricemotionProduct->getLowestPrice()) < 0.0001
        ) {
            $this->logger->debug(
                sprintf(
                    'No change in lowest price %.2f for product %s, and refreshed at is already set',
                    $lowestPrice,
                    $productEntity->getId(),
                ),
            );
            return;
        }
        $this->productWriteSubscriber->quietly(
            fn() => $this->productRepository->upsert(
                [
                    [
                        'id' => $productEntity->getId(),
                        PricemotionProductExtension::NAME => [
                            'lowestPrice' => $pricemotionProduct->getLowestPrice(),
                            'refreshedAt' => new \DateTimeImmutable(),
                        ],
                    ],
                ],
                Context::createDefaultContext(), // @phan-suppress-current-line PhanAccessMethodInternal
            ),
        );
    }

    private function applyPriceRules(ProductEntity $productEntity, Product $pricemotionProduct): void {
        $pricemotionExtension = $productEntity->getExtension(PricemotionProductExtension::NAME);
        if (!$pricemotionExtension instanceof PricemotionProductEntity) {
            return;
        }
        $settings = $pricemotionExtension->getSettings();
        if (empty($settings)) {
            $this->logger->info(sprintf('Product %s does not have settings', $productEntity->getId()));
            return;
        }
        $settings = Settings::fromArray($settings);
        $settings->setLogger($this->logger);
        $newPrice = $settings->getNewPrice(new ProductAdapter($productEntity), $pricemotionProduct);
        if ($newPrice === null) {
            return;
        }
        $currentPrice = $productEntity->getCurrencyPrice(Defaults::CURRENCY);
        // @phan-suppress-next-line PhanAccessMethodInternal
        $context = Context::createDefaultContext();
        $this->productWriteSubscriber->quietly(
            fn() => $this->productRepository->upsert(
                [
                    [
                        'id' => $productEntity->getId(),
                        'price' => [
                            [
                                'currencyId' => Defaults::CURRENCY,
                                'gross' => $newPrice,
                                'net' => $this->getNetPrice($productEntity, $newPrice),
                                'linked' => true,
                                'listPrice' =>
                                    $currentPrice && $currentPrice->getListPrice()
                                        ? [
                                            'currencyId' => $currentPrice->getListPrice()->getCurrencyId(),
                                            'gross' => $currentPrice->getListPrice()->getGross(),
                                            'net' => $currentPrice->getListPrice()->getNet(),
                                            'linked' => $currentPrice->getListPrice()->getLinked(),
                                        ]
                                        : null,
                            ],
                        ],
                        PricemotionProductExtension::NAME => [
                            'appliedAt' => new \DateTimeImmutable(),
                        ],
                    ],
                ],
                $context,
            ),
        );
    }

    private function getNetPrice(ProductEntity $productEntity, float $gross): float {
        $taxRules = new TaxRuleCollection([new TaxRule($productEntity->getTax()->getTaxRate())]);
        $quantityPriceDefinition = new QuantityPriceDefinition($gross, $taxRules, 1);
        $config = new CashRoundingConfig(50, 0.01, true);
        $result = $this->grossPriceCalculator->calculate($quantityPriceDefinition, $config);
        return $gross - $this->sumTaxes($result);
    }

    private function sumTaxes(CalculatedPrice $calculatedPrice): float {
        $result = 0.0;
        foreach ($calculatedPrice->getCalculatedTaxes() as $tax) {
            $result += $tax->getTax();
        }
        return $result;
    }
}
