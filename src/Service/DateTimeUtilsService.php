<?php

namespace HouseOfAgile\NakaCMSBundle\Service;

use Carbon\Carbon;
use DateTime;
use DateTimeImmutable;
use Psr\Log\LoggerInterface;

class DateTimeUtilsService
{
    /** @var LoggerInterface */
    protected $logger;

    public function __construct(
        LoggerInterface $generalLogger,

    ) {
        $this->logger = $generalLogger;
    }

    /**
     * rangeOverlap function: check if range starting on FirstDate and ending on EndDate overlap with range
     * starting on SecondDate and ending on SecondDate
     *
     * @param $startFirst
     * @param $endFirst
     * @param $startSecond
     * @param $endSecond
     * @return void
     */
    public function rangeOverlap($startFirst, $endFirst, $startSecond, $endSecond)
    {
        if ($startFirst <= $endSecond && $endFirst >= $startSecond) {
            return min($startFirst, $endSecond)->diff(max($startSecond, $startFirst))->days + 1;
        }
        return 0;
    }

    /**
     * dummy function: from two datetime, return a string in the form:
     * * Fri Dec 15 - 06-08 // same day
     * * Fri Dec 15 - 06-08 // same day
     * * DEC 15 2022 - JAN 23 2023 // different year
     * @param DateTime|DateTimeImmutable $startTime
     * @param DateTime|DateTimeImmutable $endTime
     * @param string $locale
     * @param boolean|null $withDate
     * @param boolean|null $doNotShowYear
     * @param string|null $separator
     * @return void
     */
    public function rangeAggregateTime(
        DateTime|DateTimeImmutable $startTime,
        DateTime|DateTimeImmutable $endTime,
        ?string $locale = 'de',
        ?bool $withDay = false,
        ?bool $withDate = false,
        ?string $separator = ' - ',
    ) {
        $carbonStartTime = Carbon::parse($startTime)->locale($locale);
        $carbonEndTime = Carbon::parse($endTime)->locale($locale);
        // dump($carbonStartTime);
        // dump($carbonEndTime);
        if ($carbonStartTime->isSameDay($carbonEndTime)) {
            return sprintf(
                '%s%s%s%s%s',
                $withDay ? $carbonStartTime->isoFormat('ddd') . ($withDate ? ', ' : ' ')  : '',
                $withDate ? $carbonStartTime->isoFormat('ll') . ' ' : '',
                $carbonStartTime->format('H:i'),
                $separator,
                $carbonEndTime->format('H:i')
            );
        } elseif (!$carbonStartTime->isSameMonth($carbonEndTime)) {
            return sprintf(
                '%s%s%s%s%s%s%s',
                $withDay ? $carbonStartTime->isoFormat('ddd') . ($withDate ? ', ' : ' ')  : '',
                $withDate ? $carbonStartTime->isoFormat('ll') . ' ' : '',
                $carbonStartTime->format('H:i'),
                $separator,
                $withDay ? $carbonEndTime->isoFormat('ddd') . ($withDate ? ', ' : ' ') : 'no',
                $withDate ? $carbonEndTime->isoFormat('ll') . ' ' : 'no',
                $carbonEndTime->format('H:i')
            );
        } else {
            // same month, not same day 
            return sprintf(
                '%s%s%s%s%s%s%s',
                $withDay ? $carbonStartTime->isoFormat('ddd') . ($withDate ? ', ' : ' ')  : '',
                $withDate ? $carbonStartTime->isoFormat('MMM D') . ' ' : '',
                $carbonStartTime->format('H:i'),
                $separator,
                $withDay ? $carbonEndTime->isoFormat('ddd') . ($withDate ? ', ' : ' ') : '',
                $withDate ? $carbonEndTime->isoFormat('ll') . ' ' : '',
                $carbonEndTime->format('H:i')
            );
        }
    }
}
