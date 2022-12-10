<?php

namespace HouseOfAgile\NakaCMSBundle\Controller;

use HouseOfAgile\NakaCMSBundle\Component\ContentManagement\StaffManager;
use HouseOfAgile\NakaCMSBundle\Component\OpeningHours\OpeningHoursManager;
use HouseOfAgile\NakaCMSBundle\Form\ContactType;
use HouseOfAgile\NakaCMSBundle\Service\Mailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TeamController extends AbstractController
{
    #[Route(path: '/{_locale<%app.supported_locales%>}/team', name: 'app_team')]
    public function showTeam(
        Request $request,
        StaffManager $staffManager
    ): Response {

        $viewParams = [
            'dentists' => $staffManager->getStaffByBusinessRole('dentist'),
            'assistants' => $staffManager->getStaffByBusinessRole('assistant'),
        ];
        return $this->render('@NakaCMS/team.html.twig', $viewParams);
    }
}
