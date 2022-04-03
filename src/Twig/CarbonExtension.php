<?php

namespace HouseOfAgile\NakaCMSBundle\Twig;

use Carbon\Carbon;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class CarbonExtension extends AbstractExtension
{
    /** @var RequestStack */
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function getFilters()
    {
        return [
            new TwigFilter('carbon', [$this, 'carbonDate']),
            new TwigFilter('carbonWeekDay', [$this, 'carbonWeekDay']),
        ];
    }

    public function carbonDate($date, $format = 'human')
    {
        $locale = $this->requestStack->getCurrentRequest()->getLocale();

        $dt = Carbon::parse($date)->locale($locale);

        switch ($format) {
            case 'human':
                return $dt->diffForHumans();
                break;
            case 'onlyWeekDay':
                return $dt->dayName;
                break;
                // get month name and year if not in the current year
            case 'getMonthYear':
                $current = Carbon::now();
                return $dt->year != $current->year ? $dt->monthName . ' ' . $dt->year : $dt->monthName;
                break;
            case 'normal':
            default:
                return $dt->toDayDateTimeString();
                break;
        }
    }

    public function carbonWeekDay($weekday)
    {
        $locale = $this->requestStack->getCurrentRequest()->getLocale();
        return Carbon::create($weekday)->locale($locale)->dayName;
    }
}
