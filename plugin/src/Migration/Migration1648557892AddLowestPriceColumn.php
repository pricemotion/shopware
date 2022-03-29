<?php declare(strict_types=1);

namespace Pricemotion\Shopware\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1648557892AddLowestPriceColumn extends MigrationStep {
    public function getCreationTimestamp(): int {
        return 1648557892;
    }

    public function update(Connection $connection): void {
        $connection->executeStatement("
            ALTER TABLE kibo_pricemotion_product
                ADD lowest_price DECIMAL(10, 2) AFTER applied_at,
                ADD refreshed_at DATETIME(3) AFTER lowest_price
        ");
    }

    public function updateDestructive(Connection $connection): void {
    }
}
