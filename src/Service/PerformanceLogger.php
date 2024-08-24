<?php

namespace HouseOfAgile\NakaCMSBundle\Service;

use Psr\Log\LoggerInterface;

class PerformanceLogger
{
    private LoggerInterface $logger;
    private bool $isPerformanceLoggingEnabled;

    public function __construct(LoggerInterface $logger, bool $isPerformanceLoggingEnabled)
    {
        $this->logger = $logger;
        $this->isPerformanceLoggingEnabled = $isPerformanceLoggingEnabled;
    }

    /**
     * Measures and logs the performance of a given callable.
     *
     * @param callable $callable The function to be measured.
     * @param string $operationName A name for the operation being measured.
     * @param array $context Additional context to include in the logs.
     * @return mixed The result of the callable.
     */
    public function measureAndLogPerformance(callable $callable, string $operationName, array $context = [])
    {
        if (!$this->isPerformanceLoggingEnabled) {
            return $callable();
        }

        // Start time and CPU usage tracking
        $startTime = microtime(true);
        $startCpu = getrusage();

        $result = $callable();

        $endTime = microtime(true);
        $endCpu = getrusage();

        // Calculate performance metrics
        $timeTakenMs = ($endTime - $startTime) * 1000;  // Convert to milliseconds
        $cpuUserTime = ($endCpu['ru_utime.tv_sec'] - $startCpu['ru_utime.tv_sec']) +
                       ($endCpu['ru_utime.tv_usec'] - $startCpu['ru_utime.tv_usec']) / 1e6;
        $cpuSystemTime = ($endCpu['ru_stime.tv_sec'] - $startCpu['ru_stime.tv_sec']) +
                         ($endCpu['ru_stime.tv_usec'] - $startCpu['ru_stime.tv_usec']) / 1e6;

        // Calculate total CPU time and average CPU usage
        $totalCpuTime = $cpuUserTime + $cpuSystemTime;
        $averageCpuUsage = ($totalCpuTime / ($endTime - $startTime)) * 100;  // As a percentage

        // Log the performance data
        $this->logger->info('PerformanceLogger: ' . $operationName, array_merge($context, [
            'time_taken_ms' => round($timeTakenMs, 2),
            'cpu_user_time_ms' => round($cpuUserTime * 1000, 2),
            'cpu_system_time_ms' => round($cpuSystemTime * 1000, 2),
            'average_cpu_usage_percentage' => round($averageCpuUsage, 2) . '%'
        ]));

        return $result;
    }
}
