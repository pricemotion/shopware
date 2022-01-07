<?php declare(strict_types=1);

namespace Pricemotion\Shopware\Extension\Content\Product;

use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\DateTimeField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\JsonField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class PricemotionProductDefinition extends EntityDefinition {
    const ENTITY_NAME = 'kibo_pricemotion_product';

    public function getEntityName(): string {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string {
        return PricemotionProductEntity::class;
    }

    protected function defineFields(): FieldCollection {
        return new FieldCollection([
            (new FkField('product_id', 'productId', ProductDefinition::class))->addFlags(
                new Required(),
                new PrimaryKey(),
            ),
            new OneToOneAssociationField('product', 'product_id', 'id', ProductDefinition::class, false),
            new DateTimeField('applied_at', 'appliedAt'),
            new JsonField('settings', 'settings'),
        ]);
    }
}
