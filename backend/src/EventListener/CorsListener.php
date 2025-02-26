<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelEvents;

class CorsListener implements EventSubscriberInterface
{
    private array $allowedOrigins = [
        'http://localhost:3000',
        'http://192.168.64.8:3000',
    ];

    private array $allowedRoutes = [
        '/api/',
        '/public/',
    ];

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
        $origin = $request->headers->get('Origin');
        $path = $request->getPathInfo();

        if ($method === 'OPTIONS' && $this->isAllowedOrigin($origin) && $this->isAllowedRoute($path)) {
            $response = new Response();
            $response->setStatusCode(Response::HTTP_NO_CONTENT);

            $response->headers->set('Access-Control-Allow-Origin', $origin);
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
        $origin = $request->headers->get('Origin');
        $path = $request->getPathInfo();

        if ($this->isAllowedOrigin($origin) && $this->isAllowedRoute($path)) {
            $response->headers->set('Access-Control-Allow-Origin', $origin);
            $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization');
            $response->headers->set('Access-Control-Max-Age', '3600');

            $response->headers->set('Access-Control-Allow-Credentials', 'true');
        }
    }

    private function isAllowedOrigin(?string $origin): bool
    {
        return $origin !== null && in_array($origin, $this->allowedOrigins, true);
    }

    private function isAllowedRoute(string $path): bool
    {
        foreach ($this->allowedRoutes as $route) {
            if (strpos($path, $route) === 0) {
                return true;
            }
        }

        return false;
    }
}