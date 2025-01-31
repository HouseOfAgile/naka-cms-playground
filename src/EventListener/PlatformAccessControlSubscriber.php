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
        #[Autowire('%kernel.environment%')]
        private string $kernelEnvironment,
        #[Autowire('%hoa_naka_cms.maintenance_mode%')]
        private bool $isGlobalMaintenanceEnabled,
        #[Autowire('%hoa_naka_cms.future_maintenance_warning%')]
        private bool $isFutureMaintenanceWarningEnabled,
        #[Autowire('%hoa_naka_cms.maintenance_start%')]
        private ?string $maintenanceStartTime,
        #[Autowire('%hoa_naka_cms.maintenance_duration%')]
        private ?int $maintenanceDuration,
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

        // Some routes allowed e.g. 'health_check', 'platform_maintenance' etc.
        $allowedRoutes = ['platform_maintenance', 'health_check'];
        if (\in_array($currentRoute, $allowedRoutes, true)) {
            return;
        }

        // 1) FULL MAINTENANCE MODE
        if ($this->isGlobalMaintenanceEnabled) {
            $response = new Response(
                $this->twig->render('@NakaCMS/maintenance.html.twig', [
                    // Pass raw translation keys to Twig
                    'title' => 'maintenance.title',
                    'message' => 'maintenance.message',
                    'maintenance_start' => $this->maintenanceStartTime,
                    'maintenance_duration' => $this->maintenanceDuration,
                ]),
                Response::HTTP_SERVICE_UNAVAILABLE
            );
            $event->setResponse($response);
            $event->stopPropagation();
            return;
        }

        // 2) FUTURE / SCHEDULED MAINTENANCE WARNING (only if environment != 'prod')
        if (
            $this->isFutureMaintenanceWarningEnabled &&
            $this->kernelEnvironment !== 'prod' &&
            $this->maintenanceStartTime &&
            $this->maintenanceDuration
        ) {
            $session = $request->getSession();
            if ($session && $session->isStarted()) {
                $maintenanceStart = new \DateTime($this->maintenanceStartTime);
                $maintenanceEnd   = (clone $maintenanceStart)->modify("+{$this->maintenanceDuration} minutes");
                $now             = new \DateTime();

                if ($now < $maintenanceEnd) {
                    if ($now < $maintenanceStart) {
                        // Maintenance is in the future
                        $session->getFlashBag()->add('warning', sprintf(
                            '⚠️ %s: %s (Duration: %d min)',
                            // The next two can be plain text or translator calls. 
                            // If you prefer translation in Twig, pass a string with a key. 
                            'Maintenance Scheduled', 
                            $maintenanceStart->format('Y-m-d H:i'),
                            $this->maintenanceDuration
                        ));
                    } else {
                        // Maintenance is happening now, but not in full mode
                        $session->getFlashBag()->add('warning', sprintf(
                            '⚠️ %s: %s (Ends approx: %s)',
                            'Maintenance in Progress',
                            $maintenanceStart->format('Y-m-d H:i'),
                            $maintenanceEnd->format('Y-m-d H:i')
                        ));
                    }
                }
            }
        }

        // 3) ROUTE-SPECIFIC RESTRICTIONS
        $restrictedRoutes = $this->platformAccessControlConfig['restricted_routes'] ?? [];
        if (isset($restrictedRoutes[$currentRoute]) && $restrictedRoutes[$currentRoute] === false) {
            $session = $request->getSession();
            $session->getFlashBag()->add('warning', 'This page is currently not accessible.');

            $referer = $request->headers->get('referer');
            $event->setResponse(new RedirectResponse($referer ?: $this->router->generate('app_homepage')));
            $event->stopPropagation();
        }
    }
}
