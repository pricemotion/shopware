<?php declare(strict_types=1);

namespace Pricemotion\Shopware\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"api"})
 */
class ApiController extends AbstractController {
    /**
     * @Route("/api/pricemotion/get-widget-url", methods={"POST"})
     */
    public function getWidgetUrl(): JsonResponse {
        return new JsonResponse([
            'url' => 'https://www.pricemotion.nl/app/widget',
        ]);
    }
}
