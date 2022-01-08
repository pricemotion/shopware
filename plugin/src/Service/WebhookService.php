<?php declare(strict_types=1);

namespace Pricemotion\Shopware\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Pricemotion\Shopware\Exception\ConfigurationException;
use Pricemotion\Shopware\KiboPricemotion;
use Pricemotion\Shopware\Util\Base64;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class WebhookService {
    private SystemConfigService $config;

    private UrlGeneratorInterface $urlGenerator;

    private LoggerInterface $logger;

    private Client $httpClient;

    public function __construct(
        SystemConfigService $config,
        UrlGeneratorInterface $urlGenerator,
        LoggerInterface $logger
    ) {
        $this->config = $config;
        $this->urlGenerator = $urlGenerator;
        $this->logger = $logger;
    }

    public function registerWebhook(): void {
        $webhookUrl = $this->getWebhookUrl();
        $this->post('/webhooks', [
            'url' => $webhookUrl,
        ]);
        $this->logger->info(sprintf('Registered Pricemotion webhook with URL %s', $webhookUrl));
    }

    private function getWebhookUrl(): string {
        return $this->urlGenerator->generate(
            'pricemotion.webhook',
            [
                'apiKeyDigest' => Base64::encode(hash('sha256', $this->getApiKey(), true)),
            ],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );
    }

    private function getApiKey(): string {
        $apiKey = trim($this->config->getString(KiboPricemotion::CONFIG_API_KEY));
        if (empty($apiKey)) {
            throw new ConfigurationException('No API key is configured');
        }
        return $apiKey;
    }

    public function trigger(string $ean): void {
        $this->post('/webhooks/trigger', ['ean' => $ean]);
    }

    private function post(string $path, array $data): ResponseInterface {
        $requestOptions = [
            'auth' => [$this->getApiKey(), ''],
            'json' => $data,
            'connect_timeout' => 10,
            'read_timeout' => 10,
        ];
        try {
            return $this->getHttpClient()->request('POST', 'https://www.pricemotion.nl/api' . $path, $requestOptions);
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 401) {
                throw new ConfigurationException('API key is invalid');
            }
            throw $e;
        }
    }

    private function getHttpClient(): Client {
        return $this->httpClient ??= new Client();
    }
}
