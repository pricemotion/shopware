<?php declare(strict_types=1);

namespace Pricemotion\Shopware;

use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Shopware\Core\Kernel;

class KiboPricemotion extends Plugin {
    public function uninstall(UninstallContext $uninstallContext): void {
        parent::uninstall($uninstallContext);

        if ($uninstallContext->keepUserData()) {
            return;
        }

        Kernel::getConnection()->executeStatement('DROP TABLE IF EXISTS kibo_pricemotion_product');
    }
}
