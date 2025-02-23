<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelEvents;

class CorsListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 9999],
            KernelEvents::RESPONSE => ['onKernelResponse', 9999],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }
        $request = $event->getRequest();
        $method = $request->getRealMethod();

        // Handle OPTIONS preflight requests
        if ($method === 'OPTIONS') {
            $response = new Response();
            $response->setStatusCode(Response::HTTP_NO_CONTENT);

            $response->headers->set('Access-Control-Allow-Origin', 'http://localhost:3000'); // Single origin allowed
            $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization');
            $response->headers->set('Access-Control-Max-Age', '3600'); // Cache for 1 hour

            $event->setResponse($response);
        }
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        $response = $event->getResponse();
        $request = $event->getRequest();

        // Add CORS headers for all responses
        $response->headers->set('Access-Control-Allow-Origin', 'http://localhost:3000'); // Single origin allowed
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization');
        $response->headers->set('Access-Control-Max-Age', '3600'); // Cache for 1 hour

        // Allow credentials if needed
        $response->headers->set('Access-Control-Allow-Credentials', 'true');
    }
}