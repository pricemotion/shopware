<?php declare(strict_types=1);

namespace Pricemotion\Shopware\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1640787895CreateProductTable extends MigrationStep {
    public function getCreationTimestamp(): int {
        return 1640787895;
    }

    public function update(Connection $connection): void {
        $connection->executeStatement("
            CREATE TABLE kibo_pricemotion_product (
                product_id BINARY(16) NOT NULL,
                ean CHAR(13) COLLATE ascii_bin,
                settings JSON,
                applied_at DATETIME(3),
                created_at DATETIME(3) NOT NULL,
                updated_at DATETIME(3),
                CONSTRAINT `json.kibo_pricemotion_product.settings` CHECK (JSON_VALID(settings)),
                CONSTRAINT `fk.kibo_pricemotion_product.product_id` FOREIGN KEY (product_id)
                    REFERENCES product (id) ON UPDATE CASCADE ON DELETE CASCADE,
                KEY `idx.kibo_pricemotion_product.ean` (ean)
            ) ENGINE=InnoDB COLLATE=utf8mb4_unicode_ci
        ");
    }

    public function updateDestructive(Connection $connection): void {
    }
}
