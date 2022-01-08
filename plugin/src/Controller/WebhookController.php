<?php declare(strict_types=1);

namespace Pricemotion\Shopware\Controller;

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

/**
 * @RouteScope(scopes={"api"})
 */
class WebhookController extends AbstractController {
    private SystemConfigService $config;

    public function __construct(SystemConfigService $config) {
        $this->config = $config;
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

        $data = $request->toArray();

        if (empty($data['xml'])) {
            throw new BadRequestException();
        }

        // TODO -- Validate that the webhook is triggered for the right user (by API key)
        // eg, by hashing and including the API key in the webhook URL
        return new Response();
    }
}
