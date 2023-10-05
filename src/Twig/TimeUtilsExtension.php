<?php

namespace HouseOfAgile\NakaCMSBundle\Twig;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TimeUtilsExtension extends AbstractExtension
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
            new TwigFilter('durationFromSeconds', [$this, 'durationFromSeconds']),
            new TwigFilter('durationIntoHuman', [$this, 'durationIntoHuman']),
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
            case 'getMonthPlusYear':
                return $dt->monthName . ' ' . $dt->year;
                break;
            case 'getMonthYear':
                $current = Carbon::now();
                return $dt->year != $current->year ? $dt->monthName . ' ' . $dt->year : $dt->monthName;
                break;
            case 'getMonthDayYear':
                $current = Carbon::now();
                $monthDay = substr($dt->monthName, 0, 3) . ' ' . $dt->day;
                return $dt->year != $current->year ? $monthDay  . ' ' . $dt->year : $monthDay;
                break;
            case 'getMonthDay':
                $current = Carbon::now();
                $monthDay = substr($dt->monthName, 0, 3) . ' ' . $dt->day;
                return $monthDay;
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

    public function durationIntoHuman($duration, $timeType = 'seconds', $short = true)
    {
        switch ($timeType) {
            case 'seconds':
                return CarbonInterval::seconds($duration)->cascade()->forHumans(null, $short);
                break;
            case 'milliseconds ' || 'ms':
                return CarbonInterval::milliseconds($duration)->cascade()->forHumans(null, $short);
                break;
            default:
                break;
        }
    }

    /**
     * @deprecated since NakaCMS 0.8, use durationIntoHuman instead.
     */
    public function durationFromSeconds($durationInSeconds)
    {
        //CarbonInterval::seconds(90060)->cascade()->forHumans();

        return $durationInSeconds > 3600 ? gmdate("g \h\o\u\\r\s i \m\i\\n s", $durationInSeconds) : gmdate("g \h\o\u\\r\s i \m\i\\n s", $durationInSeconds);
    }
}
