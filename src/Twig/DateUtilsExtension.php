<?php

namespace HouseOfAgile\NakaCMSBundle\Twig;

use Carbon\Carbon;
use DateTime;
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
            new TwigFunction('rangeAggregateDay', [$this, 'rangeAggregateDay'], ['beginDate', 'endDate']),
        ];
    }

    /**
     * rangeAggregateDay function: from two datetime, return a string in the form:
     * * DEC 15 - DEC 20 2023 // same year
     * * DEC 15 2022 - JAN 23 2023 // different year
     * @param DateTime $beginDate
     * @param DateTime $endDate
     * @param boolean $doNotShowYear : set to true to not show year if not different from current year
     * @param string $separator
     * @return void
     * @todo improve the donotshowyear feature
     */
    public function rangeAggregateDay(DateTime $beginDate, DateTime $endDate, bool $doNotShowYear = false, $separator = ' / ')
    {
        $locale = $this->requestStack->getCurrentRequest()->getLocale();
        $carbonBeginDate = Carbon::parse($beginDate)->locale($locale);
        $carbonEndDate = Carbon::parse($endDate)->locale($locale);
        // $differentMonth =  $carbonBeginDate->month !=$carbonEndDate->month;
        $differentYear =  $carbonBeginDate->year != $carbonEndDate->year;
        $yearFormat = ($doNotShowYear && !$differentYear) ? '' : ' Y';
        if ($carbonBeginDate->isSameDay($carbonEndDate)) {
            return $carbonEndDate->format('M j' . $yearFormat);
        } else {
            return $differentYear ?
                $carbonBeginDate->format('M j' . $yearFormat) . $separator . $carbonEndDate->format('M j' . $yearFormat) :
                $carbonBeginDate->format('M j') . $separator . $carbonEndDate->format('M j' . $yearFormat);
        }
    }
}
