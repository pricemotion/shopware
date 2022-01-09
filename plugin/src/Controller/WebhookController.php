<?php declare(strict_types=1);

namespace Pricemotion\Shopware\Controller;

use Pricemotion\Sdk\Api\WebhookRequestFactory;
use Pricemotion\Sdk\Crypto\SignatureVerifier;
use Pricemotion\Sdk\RuntimeException;
use Pricemotion\Shopware\KiboPricemotion;
use Pricemotion\Shopware\Util\Base64;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;

/**
 * @RouteScope(scopes={"api"})
 */
class WebhookController extends AbstractController {
    private SystemConfigService $config;

    private CacheInterface $cache;

    public function __construct(SystemConfigService $config, CacheInterface $cache) {
        $this->config = $config;
        $this->cache = $cache;
    }

    /**
     * @Route("/api/pricemotion/webhook", methods={"POST"}, name="pricemotion.webhook")
     */
    public function webhook(Request $request): Response {
        $apiKey = trim($this->config->getString(KiboPricemotion::CONFIG_API_KEY));
        $apiKeyDigest = Base64::encode(hash('sha256', $apiKey, true));

        if ($request->query->get('apiKeyDigest') !== $apiKeyDigest) {
            throw new NotFoundHttpException();
        }

        $signatureVerifier = new SignatureVerifier($this->cache);
        $webhookRequestFactory = new WebhookRequestFactory($signatureVerifier);

        try {
            $request = $webhookRequestFactory->createFromRequestBody($request->getContent());
        } catch (RuntimeException $e) {
            throw new BadRequestException();
        }

        $this->updateProduct($request->getProduct());

        return new Response();
    }
}
