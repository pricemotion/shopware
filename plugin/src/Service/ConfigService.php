<?php declare(strict_types=1);

namespace Pricemotion\Shopware\Service;

use Pricemotion\Shopware\Exception\ConfigurationException;
use Pricemotion\Shopware\KiboPricemotion;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class ConfigService {
    private SystemConfigService $systemConfig;

    public function __construct(SystemConfigService $systemConfig) {
        $this->systemConfig = $systemConfig;
    }

    public function getApiKey(): string {
        $apiKey = trim($this->systemConfig->getString(KiboPricemotion::CONFIG_API_KEY));
        if (empty($apiKey)) {
            throw new ConfigurationException('No API key is configured');
        }
        return $apiKey;
    }
}
