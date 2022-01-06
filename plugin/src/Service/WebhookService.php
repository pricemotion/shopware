<?php declare(strict_types=1);

namespace Pricemotion\Shopware\Service;

use Pricemotion\Shopware\Exception\ConfigurationException;
use Pricemotion\Shopware\KiboPricemotion;
use Pricemotion\Shopware\Util\Base64;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class WebhookService {
    private SystemConfigService $config;

    private HttpClientInterface $httpClient;

    private UrlGeneratorInterface $urlGenerator;

    public function __construct(
        SystemConfigService $config,
        HttpClientInterface $httpClient,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->config = $config;
        $this->httpClient = $httpClient;
        $this->urlGenerator = $urlGenerator;
    }

    public function registerWebhook(): void {
        $this->httpClient->request('POST', 'https://www.pricemotion.nl/api/webhooks', [
            'auth_basic' => [$this->getApiKey(), ''],
            'json' => [
                'url' => $this->urlGenerator->generate('pricemotion.webhook'),
            ],
        ]);
    }

    private function getWebhookUrl(): string {
        return $this->urlGenerator->generate('pricemotion.webhook', [
            'apiKeyDigest' => Base64::encode(hash('sha256', $this->getApiKey(), true)),
        ]);
    }

    private function getApiKey(): string {
        $apiKey = trim($this->config->getString(KiboPricemotion::CONFIG_API_KEY));
        if (empty($apiKey)) {
            throw new ConfigurationException('No API key is configured');
        }
        return $apiKey;
    }
}
