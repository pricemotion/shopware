<?php declare(strict_types=1);

namespace Pricemotion\Shopware\Controller;

use Pricemotion\Shopware\KiboPricemotion;
use Pricemotion\Shopware\Util\Base64;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"api"})
 */
class ApiController extends AbstractController {
    private $systemConfigService;

    public function __construct(SystemConfigService $systemConfigService) {
        $this->systemConfigService = $systemConfigService;
    }

    /**
     * @Route("/api/pricemotion/get-widget-url", methods={"POST"})
     */
    public function getWidgetUrl(): JsonResponse {
        return new JsonResponse([
            'url' => 'https://www.pricemotion.nl/app/widget',
            'token' => $this->getApiToken(),
        ]);
    }

    public function getApiToken(): ?string {
        $apiKey = $this->getApiKey();

        if ($apiKey === null) {
            return null;
        }

        $expiresAt = time() + 3600;

        return Base64::encode(
            implode('', [
                hash('sha256', $apiKey, true),
                hash_hmac('sha256', (string) $expiresAt, $apiKey, true),
                pack('P', $expiresAt),
            ]),
        );
    }

    public function getApiKey(): ?string {
        return trim($this->systemConfigService->getString(KiboPricemotion::CONFIG_API_KEY)) ?: null;
    }
}
