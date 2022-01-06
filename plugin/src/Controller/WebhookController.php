<?php declare(strict_types=1);

namespace Pricemotion\Shopware\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"api"})
 */
class WebhookController extends AbstractController {
    /**
     * @Route("/api/pricemotion/webhook", methods={"POST"}, name="pricemotion.webhook")
     */
    public function webhook(): Response {
        // TODO -- Validate that the webhook is triggered for the right user (by API key)
        // eg, by hashing and including the API key in the webhook URL
        return new Response();
    }
}
