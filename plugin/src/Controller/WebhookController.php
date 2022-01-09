<?php declare(strict_types=1);

namespace Pricemotion\Shopware\Controller;

use Pricemotion\Sdk\Api\WebhookRequestFactory;
use Pricemotion\Sdk\Crypto\SignatureVerifier;
use Pricemotion\Sdk\RuntimeException;
use Pricemotion\Shopware\KiboPricemotion;
use Pricemotion\Shopware\Service\ProductUpdateService;
use Pricemotion\Shopware\Util\Base64;
use Psr\Log\LoggerInterface;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;

/**
 * @RouteScope(scopes={"api"})
 */
class WebhookController extends AbstractController {
    private SystemConfigService $config;

    private CacheInterface $cache;

    private ProductUpdateService $productUpdateService;

    private LoggerInterface $logger;

    public function __construct(
        SystemConfigService $config,
        CacheInterface $cache,
        ProductUpdateService $productUpdateService,
        LoggerInterface $logger
    ) {
        $this->config = $config;
        $this->cache = $cache;
        $this->productUpdateService = $productUpdateService;
        $this->logger = $logger;
    }

    /**
     * @Route("/api/pricemotion/webhook", name="pricemotion.webhook", defaults={"auth_required"=false}, methods={"POST"})
     */
    public function webhook(Request $request): Response {
        $apiKey = trim($this->config->getString(KiboPricemotion::CONFIG_API_KEY));
        $apiKeyDigest = Base64::encode(hash('sha256', $apiKey, true));

        if ($request->query->get('apiKeyDigest') !== $apiKeyDigest) {
            return new Response('API key mismatch', 404, ['Content-Type' => 'text/plain']);
        }

        $signatureVerifier = new SignatureVerifier($this->cache);
        $webhookRequestFactory = new WebhookRequestFactory($signatureVerifier);

        try {
            $request = $webhookRequestFactory->createFromRequestBody($request->getContent());
        } catch (RuntimeException $e) {
            $this->logger->warning(
                sprintf('Refused webhook request due to %s: (%s) %s', get_class($e), $e->getCode(), $e->getMessage()),
            );
            return new Response('Invalid request', 400, ['Content-Type' => 'text/plain']);
        }

        $this->productUpdateService->updateProducts($request->getProduct());

        return new Response();
    }
}
