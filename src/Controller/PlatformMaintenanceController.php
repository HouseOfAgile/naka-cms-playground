<?php
namespace HouseOfAgile\NakaCMSBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlatformMaintenanceController extends AbstractController
{
    #[Route('/maintenance', name: 'platform_maintenance')]
    public function showMaintenancePage(): Response
    {
        return $this->render('@NakaCMS/maintenance.html.twig', [
            'title' => 'Platform Under Maintenance',
            'message' => 'We’re currently performing updates. Please try again later.',
        ]);
    }
}
