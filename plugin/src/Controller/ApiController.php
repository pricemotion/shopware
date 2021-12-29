<?php declare(strict_types=1);

namespace Pricemotion\Shopware\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"api"})
 */
class ApiController extends AbstractController {
    /**
     * @Route("/api/pricemotion/get-widget-url", methods={"POST"})
     */
    public function getWidgetUrl(Request $request): JsonResponse {
        $data = $request->toArray();

        return new JsonResponse([
            'url' => 'https://www.pricemotion.nl/app/widget',
            'token' => $this->getApiToken()
        ]);
    }

    private function getApiToken(): string {
        return 'TODO';
    }
}
