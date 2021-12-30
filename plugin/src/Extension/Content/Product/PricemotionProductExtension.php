<?php declare(strict_types=1);

namespace Pricemotion\Shopware\Extension\Content\Product;

use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class PricemotionProductExtension extends EntityExtension {
    const NAME = 'pricemotion';

    public function getDefinitionClass(): string {
        return ProductDefinition::class;
    }

    public function extendFields(FieldCollection $collection): void {
        $collection->add(
            new OneToOneAssociationField(self::NAME, 'id', 'product_id', PricemotionProductDefinition::class),
        );
    }
}
