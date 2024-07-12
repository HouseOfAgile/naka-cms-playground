<?php

namespace HouseOfAgile\NakaCMSBundle\Twig;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use DateTime;
use DateTimeImmutable;
use HouseOfAgile\NakaCMSBundle\Service\DateTimeUtilsService;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class TimeUtilsExtension extends AbstractExtension
{
    /** @var DateTimeUtilsService */
    private $dateTimeUtilsService;

    /** @var RequestStack */
    private $requestStack;

    public function __construct(
        RequestStack $requestStack,
        DateTimeUtilsService $dateTimeUtilsService,
    ) {
        $this->requestStack = $requestStack;
        $this->dateTimeUtilsService = $dateTimeUtilsService;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('carbon', [$this, 'carbonDate']),
            new TwigFilter('carbonWeekDay', [$this, 'carbonWeekDay']),
            new TwigFilter('durationFromSeconds', [$this, 'durationFromSeconds']),
            new TwigFilter('durationIntoHuman', [$this, 'durationIntoHuman']),
        ];
    }


    public function getFunctions(): array
    {
        return [
            new TwigFunction('rangeAggregateTime', [$this, 'rangeAggregateTime']),
            new TwigFunction('isOlderThan', [$this, 'isOlderThan']),
        ];
    }

    public function carbonDate($date, $format = 'human')
    {
        // in case the requestStack is not set (command line) we use default locale
        $locale = $this->requestStack->getCurrentRequest() ? $this->requestStack->getCurrentRequest()->getLocale() : 'en';

        $dt = Carbon::parse($date)->locale($locale);

        switch ($format) {
            case 'human':
                return $dt->diffForHumans();
                break;
            case 'onlyHourMinute':
                // return $dt->hour . ':' . $dt->minute;
                return $dt->isoFormat('LT');
                break;
            case 'dayHourMinute':
                return $dt->shortLocaleDayOfWeek . ' ' . $dt->isoFormat('LT');
                break;
            case 'dayHourSlot':
                $dtnh = $dt->copy()->addHour();
                return $dt->shortLocaleDayOfWeek . ' ' . $dt->isoFormat('LT') . ' - ' . $dtnh->isoFormat('LT');
                break;
            case 'shortDay':
                return $dt->shortLocaleDayOfWeek;
                break;
            case 'hourSlot':
                $dtnh = $dt->copy()->addHour();
                return $dt->isoFormat('LT') . ' - ' . $dtnh->isoFormat('LT');
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

    public function rangeAggregateTime(
        DateTime|DateTimeImmutable $startTime,
        DateTime|DateTimeImmutable $endTime,
        ?bool $withDay = false,
        ?bool $withDate = false,
        ?string $separator = ' - ',
    ) {
        $locale = $this->requestStack->getCurrentRequest()->getLocale();

        $timeString = $this->dateTimeUtilsService->rangeAggregateTime(
            $startTime,
            $endTime,
            $locale,
            $withDay,
            $withDate,
            $separator
        );
        return $timeString;
    }

    public function isOlderThan($date, $timeRange = '-1 week'): bool
    {
        if (!$date instanceof DateTime) {
            // Optionally handle invalid argument or convert from another format
            return false;
        }

        $timeAgo = new DateTime($timeRange);

        return $date < $timeAgo;
    }
}
