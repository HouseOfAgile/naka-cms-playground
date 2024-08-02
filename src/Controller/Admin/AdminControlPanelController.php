<?php

namespace HouseOfAgile\NakaCMSBundle\Controller\Admin;

use App\Controller\Admin\AdminDashboardController;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/control-panel/{_locale<%app.supported_locales%>}', requirements: ['_locale' => 'en|de|fr'], name: 'admin_control_panel_')]
class AdminControlPanelController extends AdminDashboardController
{
    public const CONTROL_PANEL_OPERATIONS = [
        'translation' => '@NakaCMS/backend/control_panel/_panel_translation.html.twig'
    ];

    #[Route(path: '/dashboard', name: 'dashboard')]
    public function controlPanelDashboard(KernelInterface $kernel): Response
    {
        $viewParams = [
            'controlPanelOperations' => self::CONTROL_PANEL_OPERATIONS
        ];
        return $this->render('@NakaCMS/backend/control_panel/control_panel_dashboard.html.twig', $viewParams);
    }

    #[Route(path: '/export-translation', name: 'export_translation')]
    public function exportTranslation(KernelInterface $kernel): Response
    {
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput([
            'command' => 'lexik:translations:export',
            '--domains' => 'messages',
        ]);

        // You can use NullOutput() if you don't need the output
        $output = new BufferedOutput();
        $application->run($input, $output);

        // return the output, don't use if you used NullOutput()
        $exportTranslationContent = $output->fetch();

        // return new Response($content);
        $viewParams = [
            'exportTranslationContent' => $exportTranslationContent,
        ];
        return $this->render('@NakaCMS/backend/control_panel/export_translation.html.twig', $viewParams);
    }
}
