<?php
// src/Controller/Admin/AdminUserGuideController.php
namespace HouseOfAgile\NakaCMSBundle\Controller\Admin;

use League\CommonMark\CommonMarkConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\MimeTypes;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\EA;
use App\Controller\Admin\AdminDashboardController;

#[Route(
	'/admin/{_locale<%app.supported_locales%>}/tech-guide',
	name: 'admin_tech_guide_',
	defaults: [EA::DASHBOARD_CONTROLLER_FQCN => AdminDashboardController::class],
)]
#[IsGranted('ROLE_ADMIN')]
class AdminTechGuideController extends AbstractController
{
	private string $guideDir;

	public function __construct(
		private string $applicationName,
		string $projectDir,
		private UrlGeneratorInterface $url,
		private CommonMarkConverter   $md = new CommonMarkConverter(),
	) {
		$this->guideDir = $projectDir . '/docs/tech-guide/';
	}

	#[Route('/', name: 'index')]
	#[Route('/{page<[^/]+>}', name: 'page')]
	public function page(Request $req, string $page = 'index'): Response
	{
		$file = $this->guideDir . $page . '.md';
		if (!is_file($file)) {
			throw $this->createNotFoundException("Page $page not found");
		}

		$pageName = ucwords(str_replace('_', ' ', $page));
		$html      = $this->md->convert(file_get_contents($file))->getContent();
		$html      = $this->replaceMarkdownLinks($html, $req->getLocale());
		$html      = $this->replaceImageSources($html, $req->getLocale());

		return $this->render(
			'@NakaCMS/backend/topic/tech-guide/markdown_page.html.twig',
			[
				'title'        => $this->applicationName . ' Tech Guide - ' . $pageName,
				'content'      => $html,
				'current_page' => $page,
			],
		);
	}

	#[Route('/images/{path}', name: 'image', requirements: ['path' => '.+'])]
	public function image(string $path): BinaryFileResponse
	{
		// block directory‑traversal
		$clean = preg_replace('#\.+/#', '', ltrim($path, '/'));
		$file  = $this->guideDir . 'images/' . $clean;

		if (!is_file($file)) {
			throw $this->createNotFoundException('Image not found');
		}

		$mime = MimeTypes::getDefault()->guessMimeType($file) ?? 'application/octet-stream';
		return new BinaryFileResponse($file, Response::HTTP_OK, ['Content-Type' => $mime]);
	}

	private function replaceMarkdownLinks(string $html, string $locale): string
	{
		return preg_replace_callback(
			'~href="(?!https?://|/|#)([^"]+?)\.md(?:#([^"]+))?"~i',
			fn($m) => 'href="' .
				$this->url->generate('admin_tech_guide_page', [
					'_locale' => $locale,
					'page'    => ltrim($m[1], './'),
				]) .
				// add anchor only when present
				(($m[2] ?? '') !== '' ? '#' . $m[2] : '') .
				'"',
			$html,
		);
	}


	/** Turn `<img src="images/pic.png">` into the dedicated image route */
	private function replaceImageSources(string $html, string $locale): string
	{
		return preg_replace_callback(
			'~src="(?!https?://|/)(images/[^"]+)"~i',
			fn($m) =>
			'src="' .
				$this->url->generate('admin_tech_guide_image', [
					'_locale' => $locale,
					'path'    => ltrim(substr($m[1], 7), '/'),
				]) . '"',
			$html,
		);
	}
}
