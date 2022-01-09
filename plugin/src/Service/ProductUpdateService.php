<?php declare(strict_types=1);

namespace Pricemotion\Shopware\Service;

use Pricemotion\Sdk\Data\Product;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;

class ProductUpdateService {
    private EntityRepositoryInterface $productRepository;

    public function __construct(EntityRepositoryInterface $productRepository) {
        $this->productRepository = $productRepository;
    }

    public function updateProduct(Product $product) {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsAnyFilter('ean', $this->getEanPermutations()));
        $this->productRepository->search($criteria, Context::createDefaultContext());
    }
}
