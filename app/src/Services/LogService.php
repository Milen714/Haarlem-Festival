<?php

namespace App\Services;

use App\Models\Log;
use App\Repositories\LogRepository;
use Exception;

class LogService
{
    private LogRepository $logRepository;

    public function __construct()
    {
        $this->logRepository = new LogRepository();
    }

    public function info(string $category, string $message, ?array $context = null): void
    {
        $this->log('INFO', $category, $message, $context);
    }

    public function error(string $category, string $message, ?array $context = null, ?string $stackTrace = null): void
    {
        $this->log('ERROR', $category, $message, $context, $stackTrace);
    }

    public function warning(string $category, string $message, ?array $context = null): void
    {
        $this->log('WARNING', $category, $message, $context);
    }

    public function debug(string $category, string $message, ?array $context = null): void
    {
        $this->log('DEBUG', $category, $message, $context);
    }

    public function exception(string $category, Exception $exception, ?array $context = null): void
    {
        $this->log('ERROR', $category, $exception->getMessage(), $context, $exception->getTraceAsString());
    }

    private function log(string $level, string $category, string $message, ?array $context = null, ?string $stackTrace = null): void
    {
        try {
            $log = new Log();
            $log->level = $level;
            $log->category = $category;
            $log->message = $message;
            $log->context = $context;
            $log->stack_trace = $stackTrace;

            $this->logRepository->create($log);
        } catch (Exception $e) {
            // Fallback to error_log if database logging fails
            error_log("[FALLBACK] $level - $category: $message");
        }
    }

    public function getByCategory(string $category, int $limit = 100): array
    {
        return $this->logRepository->getByCategory($category, $limit);
    }

    public function getByLevel(string $level, int $limit = 100): array
    {
        return $this->logRepository->getByLevel($level, $limit);
    }

    public function getRecent(int $limit = 50): array
    {
        return $this->logRepository->getRecent($limit);
    }

    public function deleteOlderThan(int $days): bool
    {
        return $this->logRepository->deleteOlderThan($days);
    }
}
