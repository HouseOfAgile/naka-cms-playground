<?php
namespace HouseOfAgile\NakaCMSBundle\EventListener;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

class PlatformAccessControlSubscriber
{
    public function __construct(
        #[Autowire('%hoa_naka_cms.maintenance_mode%')]
        private bool $isGlobalMaintenanceEnabled,
        #[Autowire('%hoa_naka_cms.access_control%')]
        private array $platformAccessControlConfig,
        private Environment $twig,
        private RouterInterface $router,
        private RequestStack $requestStack
    ) {
    }

	#[AsEventListener(event: KernelEvents::REQUEST, priority: -10)]
public function onKernelRequest(RequestEvent $event): void
{
    if ($event->getRequestType() !== HttpKernelInterface::MAIN_REQUEST) {
        return;
    }

    $request = $event->getRequest();
    $currentRoute = $request->attributes->get('_route');

    $allowedRoutes = ['platform_maintenance', 'health_check'];
    if (in_array($currentRoute, $allowedRoutes, true)) {
        return;
    }

    // Global maintenance mode
    if ($this->isGlobalMaintenanceEnabled) {
        $event->setResponse(new Response(
            $this->twig->render('@NakaCMS/maintenance.html.twig'),
            Response::HTTP_SERVICE_UNAVAILABLE
        ));
        $event->stopPropagation();
        return;
    }

    // Route-specific restrictions
    $restrictedRoutes = $this->platformAccessControlConfig['restricted_routes'] ?? [];
    if (isset($restrictedRoutes[$currentRoute]) && $restrictedRoutes[$currentRoute] === false) {
        // Add a flash message
        $session = $request->getSession();
        $session->getFlashBag()->add('warning', 'This page is currently not accessible.');

        // Redirect the user either to the homepage or to the referring page
        $referer = $request->headers->get('referer');
        if ($referer && $referer !== $request->getUri()) {
            $event->setResponse(new RedirectResponse($referer));
        } else {
            $event->setResponse(new RedirectResponse($this->router->generate('app_homepage')));
        }

        $event->stopPropagation();
        return;
    }
}

	
}

