<?php

namespace HouseOfAgile\NakaCMSBundle\EventSubscriber;

use Vich\UploaderBundle\Event\Event;
use App\Entity\Picture;
use App\Entity\PictureTranslation;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PictureUploadSubscriber implements EventSubscriberInterface
{
	public function __construct(
		private $defaultLocale
	) {}

	public static function getSubscribedEvents(): array
	{
		return [
			'vich_uploader.post_upload' => 'onPostUpload',
		];
	}

	public function onPostUpload(Event $event): void
	{
		$object = $event->getObject();
		if ($object instanceof Picture) {
			$originalName = $object->getImage()->getOriginalName();
			$translations = $object->getTranslations();

			if ($translations->isEmpty()) {
				// Create a default translation for the current locale
				$defaultTranslation = new PictureTranslation();
				$defaultTranslation->setLocale($this->defaultLocale);
				$defaultTranslation->setAltText($originalName);
				$object->addTranslation($defaultTranslation);
			} else {
				// Update existing translations where altText is empty
				foreach ($translations as $translation) {
					if (empty($translation->getAltText())) {
						$translation->setAltText($originalName);
					}
				}
			}
		}
	}
}
