<?php declare(strict_types=1);

namespace Pricemotion\Shopware;

use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Shopware\Core\Kernel;

require_once __DIR__ . '/../sdk/autoload.php';

class KiboPricemotion extends Plugin {
    const CONFIG_API_KEY = 'KiboPricemotion.config.apiKey';

    public function uninstall(UninstallContext $uninstallContext): void {
        parent::uninstall($uninstallContext);

        if ($uninstallContext->keepUserData()) {
            return;
        }

        Kernel::getConnection()->executeStatement('DROP TABLE IF EXISTS kibo_pricemotion_product');
    }
}
