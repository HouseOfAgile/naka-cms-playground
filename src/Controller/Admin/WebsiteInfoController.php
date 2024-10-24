<?php

namespace HouseOfAgile\NakaCMSBundle\Controller\Admin;

use App\Controller\Admin\AdminDashboardController;
use App\Repository\WebsiteInfoRepository;
use HouseOfAgile\NakaCMSBundle\Component\OpeningHours\OpeningHoursManager;
use HouseOfAgile\NakaCMSBundle\Form\OpeningHoursType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/website-info')]
class WebsiteInfoController extends AdminDashboardController
{
    #[Route(path: '/view', name: 'website_info_dashboard')]
    public function viewWebsiteInfo(Request $request, WebsiteInfoRepository $websiteInfoRepository): Response
    {
        $viewParams = [
            'websiteInfo' => $websiteInfoRepository->find(1),
        ];

        return $this->render('@NakaCMS/backend/topic/website-info/show_website_info.html.twig', $viewParams);
    }

    #[Route(path: '/opening-hours', name: 'website_info_view_opening_hours')]
    public function viewOpeningHours(
        Request $request,
        OpeningHoursManager $openingHoursManager
    ): Response {
        $viewParams = [
            'openingHours' => $openingHoursManager->getOpeningHoursData(),
        ];

        return $this->render('@NakaCMS/backend/topic/website-info/show_opening_hours.html.twig', $viewParams);
    }

    #[Route(path: '/opening-hours/edit', name: 'website_info_edit_opening_hours')]
    public function editOpeningHours(
        Request $request,
        OpeningHoursManager $openingHoursManager
    ): Response {
        // dd($openingHoursManager->getOpeningHoursData());

        $form = $this->createForm(OpeningHoursType::class, null, [
            'openinHoursData' => json_encode($openingHoursManager->getOpeningHoursData() ?? '')
            // 'add_submit' => true,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $data = $form->getData();
                $openingHoursArray = json_decode($data['openingHours'], true);
                $openingHoursManager->setOpeningHoursData($openingHoursArray);
                $this->addFlash('success', sprintf('flash.openingHours.openingHoursUpdated'));
                return $this->redirectToRoute('website_info_view_opening_hours', []);
            } else {
                $this->addFlash('error', sprintf('There was an error during updating of the opening hours'));
            }
        }
        $viewParams = [
            'form' => $form->createView(),
        ];
        return $this->render('@NakaCMS/backend/topic/website-info/edit_opening_hours.html.twig', $viewParams);
    }
}
