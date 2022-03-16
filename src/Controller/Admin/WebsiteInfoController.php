<?php

namespace HouseOfAgile\NakaCMSBundle\Controller\Admin;

use App\Controller\Admin\AdminDashboardController;
use App\Repository\WebsiteInfoRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/website-info")
 */
class WebsiteInfoController extends AdminDashboardController
{
    /**
     * @Route("/view", name="website_info_dashboard")
     */
    public function viewWebsiteInfo(Request $request, WebsiteInfoRepository $websiteInfoRepository): Response
    {
        $viewParams = [
            'websiteInfo' => $websiteInfoRepository->find(1),
        ];

        return $this->render('@NakaCMS/backend/website-info/show-website-info.html.twig', $viewParams);
    }
}
