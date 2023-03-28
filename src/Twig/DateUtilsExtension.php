<?php

namespace HouseOfAgile\NakaCMSBundle\Twig;

use Carbon\Carbon;
use DateTime;
use HouseOfAgile\NakaCMSBundle\Component\OpeningHours\OpeningHoursManager;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class DateUtilsExtension extends AbstractExtension
{
    /** @var RequestStack */
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('rangeAggregateMonth', [$this, 'rangeAggregateMonth'], ['beginDate', 'endDate']),
        ];
    }

    /**
     * rangeAggregateMonth function: from two datetime, return a string in the form:
     * * DEC 15 - DEC 20 2023 // same year
     * * DEC 15 2022 - JAN 23 2023 // different year
     *
     * @param DateTime $beginDate
     * @param DateTime $endDate
     * @return void
     */
    public function rangeAggregateMonth(DateTime $beginDate, DateTime $endDate)
    {
        $locale = $this->requestStack->getCurrentRequest()->getLocale();
        $carbonBeginDate = Carbon::parse($beginDate)->locale($locale);
        $carbonEndDate = Carbon::parse($endDate)->locale($locale);
        // $differentMonth =  $carbonBeginDate->month !=$carbonEndDate->month;
        $differentYear =  $carbonBeginDate->year != $carbonEndDate->year;
        return $differentYear ?
            $carbonBeginDate->format('M j Y') . ' / ' . $carbonEndDate->format('M j Y') :
            $carbonBeginDate->format('M j') . ' / ' . $carbonEndDate->format('M j Y');
    }

    public function openingStatusBool()
    {
        return $this->openingHoursManager->getOpeningStatusBool();
    }
}