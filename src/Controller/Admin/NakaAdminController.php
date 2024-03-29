<?php

namespace HouseOfAgile\NakaCMSBundle\Controller\Admin;

use App\Controller\Admin\AdminDashboardController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/admin/{_locale}')]
class NakaAdminController extends AdminDashboardController
{
    #[Route(path: '/dashboard', name: 'admin_dashboard')]
    public function adminDashboard(Request $request): Response
    {

        $viewParams = [
            'dummy' => true,
        ];
        return $this->render('@NakaCMS/backend/main-dashboard.html.twig', $viewParams);
    }    
}
