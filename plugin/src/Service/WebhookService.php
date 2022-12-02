<?php declare(strict_types=1);

namespace Pricemotion\Shopware\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Nyholm\Psr7\Uri;
use Pricemotion\Sdk\Data\Ean;
use Pricemotion\Shopware\Exception\ConfigurationException;
use Pricemotion\Shopware\Util\Base64;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class WebhookService {
    private ConfigService $config;

    private UrlGeneratorInterface $urlGenerator;

    private LoggerInterface $logger;

    private Client $httpClient;

    public function __construct(ConfigService $config, UrlGeneratorInterface $urlGenerator, LoggerInterface $logger) {
        $this->config = $config;
        $this->urlGenerator = $urlGenerator;
        $this->logger = $logger;
    }

    public function registerWebhook(): void {
        $webhookUrl = $this->getWebhookUrl();
        $this->post('/webhooks', [
            'url' => (string) $webhookUrl,
        ]);
        $this->logger->info(sprintf('Registered Pricemotion webhook with URL %s', $webhookUrl));
    }

    private function getWebhookUrl(): Uri {
        $url = new Uri(
            $this->urlGenerator->generate(
                'pricemotion.webhook',
                [
                    'apiKeyDigest' => Base64::encode(hash('sha256', $this->config->getApiKey(), true)),
                ],
                UrlGeneratorInterface::ABSOLUTE_URL,
            ),
        );
        if ($baseUrl = $this->getDevelopmentBaseUrl()) {
            $url = $this->replaceAuthority($url, $baseUrl);
        }
        return $url;
    }

    private function getDevelopmentBaseUrl(): ?Uri {
        $metricsUrl = getenv('PRICEMOTION_CLOUDFLARED_METRICS_URL');
        if (!$metricsUrl) {
            return null;
        }
        $response = $this->getHttpClient()->get($metricsUrl . '/quicktunnel');
        $hostname = json_decode($response->getBody()->getContents(), false, JSON_THROW_ON_ERROR)->hostname;
        return new Uri("https://{$hostname}");
    }

    private function replaceAuthority(Uri $url, Uri $newAuthority): Uri {
        return $url
            ->withScheme($newAuthority->getScheme())
            ->withUserInfo($newAuthority->getUserInfo())
            ->withHost($newAuthority->getHost())
            ->withPort($newAuthority->getPort());
    }

    public function trigger(Ean $ean): void {
        $this->post('/webhooks/trigger', ['ean' => $ean]);
    }

    private function post(string $path, array $data): ResponseInterface {
        $requestOptions = [
            'auth' => [$this->config->getApiKey(), ''],
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
