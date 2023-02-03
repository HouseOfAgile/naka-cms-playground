<?php

declare(strict_types=1);

namespace HouseOfAgile\NakaCMSBundle\Form\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

final class OneFieldAtLeastListener implements EventSubscriberInterface
{
    /**
     * @var string[]
     */
    private $requiredFields;

    public function __construct(array $requiredFieldsArray = [])
    {

        $this->requiredFields = $requiredFieldsArray;
    }

    public static function getSubscribedEvents()
    {
        return [
            FormEvents::SUBMIT => 'onSubmit',
        ];
    }

    /**
     * @param FormEvent $event
     *
     * @throws TransformationFailedException
     */
    public function onSubmit(FormEvent $event)
    {
        $submittedData = $event->getData();

        $emptyFields = [];
        foreach ($this->requiredFields as $fieldToCheck) {
            if (!isset($submittedData[$fieldToCheck])) {
                $emptyFields[] = $fieldToCheck;
            }
        }

        if (count($emptyFields) === count($this->requiredFields)) {
            throw new TransformationFailedException(sprintf(
                'at least one of %s is required',
                implode(', ', $this->requiredFields)
            ));
        }
    }
}
