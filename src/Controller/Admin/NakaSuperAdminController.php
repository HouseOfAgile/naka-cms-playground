<?php

namespace HouseOfAgile\NakaCMSBundle\Controller\Admin;

use App\Controller\Admin\SuperAdminDashboardController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/superadmin')]
class NakaSuperAdminController extends SuperAdminDashboardController
{
    #[Route(path: '/dashboard', name: 'superadmin_dashboard')]
    public function superAdminDashboard(Request $request): Response
    {
        $viewParams = [
            'dummy' => true,
        ];
        return $this->render('@NakaCMS/backend/main-dashboard.html.twig', $viewParams);
    }
}
