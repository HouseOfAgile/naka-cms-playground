<?php

namespace HouseOfAgile\NakaCMSBundle\Component\OpeningHours;

use DateTime;
use HouseOfAgile\NakaCMSBundle\Component\WebsiteInfo\WebsiteInfoService;
use Spatie\OpeningHours\OpeningHours;

class OpeningHoursManager
{
    /** @var WebsiteInfoService */
    protected $websiteInfoService;

    protected $openingHoursData;
    protected $openingHours;

    public function __construct(WebsiteInfoService $websiteInfoService)
    {
        $this->websiteInfoService = $websiteInfoService;

        if ($this->websiteInfoService->isOpeningHoursSet()) {
            // if ($this->websiteInfoService->getWebsiteInfo()) {
            $this->openingHoursData = $this->websiteInfoService->getWebsiteInfo()->getOpeningHours();
        } else {
            $this->openingHoursData = [
                'monday'     => ['09:00-12:00', '13:00-18:00'],
                'tuesday'    => ['09:00-12:00', '13:00-18:00'],
                'wednesday'  => ['09:00-12:00'],
                'thursday'   => ['09:00-12:00', '13:00-18:00'],
                'friday'     => ['09:00-12:00', '13:00-16:00'],
                'saturday'   => ['09:00-12:00', '13:00-16:00'],
                'sunday'     => [],
                'exceptions' => [
                    '2024-11-11' => ['09:00-12:00'],
                    '2024-12-25' => [],
                    '01-01'      => [],                // Recurring on each 1st of January
                    '12-25'      => ['09:00-12:00'],   // Recurring on each 25th of December
                ],
            ];
        }
        $this->openingHours = OpeningHours::create($this->openingHoursData);
    }

    public function getOpeningHoursData()
    {
        return $this->openingHoursData;
    }

    public function setOpeningHoursData($openingHoursData)
    {
        // check openinhoursData here
        $this->websiteInfoService->setOpeningHours($openingHoursData);
    }

    public function getOpeningHours()
    {
        return $this->openingHours;
    }

    public function getOpeningStatus($completeStatus = false)
    {
        $now = new DateTime('now');

        $inRange = $this->openingHours->currentOpenRange($now);

        if ($inRange) {
            if ($completeStatus) {
                return sprintf(
                    'We are open since %s, we will be close on %s',
                    $inRange->start(),
                    $inRange->end()
                );
            } else {
                return sprintf('We are open');
            }
        } else {
            if ($completeStatus) {
                return sprintf(
                    'We are close since %s, we will be re-open on %s',
                    $this->openingHours->previousClose($now)->format('l H:i'),
                    $this->openingHours->nextOpen($now)->format('l H:i'),
                );
            } else {
                return sprintf('We are close');
            }
        }
    }
}
