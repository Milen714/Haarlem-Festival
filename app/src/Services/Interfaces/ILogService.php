<?php

namespace App\Services\Interfaces;

use Exception;

interface ILogService
{
    /**
     * Logs an informational message.
     *
     * @param string     $category  Logical grouping (e.g. 'Jazz', 'Venue', 'Auth').
     * @param string     $message   Human-readable description of the event.
     * @param array|null $context   Optional key-value pairs providing additional detail.
     */
    public function info(string $category, string $message, ?array $context = null): void;

    /**
     * Logs an error message, optionally including a stack trace.
     *
     * @param string      $category    Logical grouping (e.g. 'Media', 'FileUpload').
     * @param string      $message     Human-readable description of the error.
     * @param array|null  $context     Optional key-value pairs providing additional detail.
     * @param string|null $stackTrace  Raw stack trace string if available.
     */
    public function error(string $category, string $message, ?array $context = null, ?string $stackTrace = null): void;

    /**
     * Logs a warning — something unexpected happened but execution can continue.
     *
     * @param string     $category  Logical grouping.
     * @param string     $message   Human-readable description of the warning.
     * @param array|null $context   Optional key-value pairs providing additional detail.
     */
    public function warning(string $category, string $message, ?array $context = null): void;

    /**
     * Logs a debug message for development and troubleshooting purposes.
     *
     * @param string     $category  Logical grouping.
     * @param string     $message   Human-readable debug information.
     * @param array|null $context   Optional key-value pairs providing additional detail.
     */
    public function debug(string $category, string $message, ?array $context = null): void;

    /**
     * Logs an exception as an ERROR entry, extracting its message and stack trace automatically.
     *
     * @param string     $category  Logical grouping.
     * @param Exception  $exception The caught exception to log.
     * @param array|null $context   Optional key-value pairs providing additional detail.
     */
    public function exception(string $category, Exception $exception, ?array $context = null): void;

    /**
     * Returns log entries matching the given category, newest first.
     *
     * @param string $category  The category to filter by.
     * @param int    $limit     Maximum number of entries to return.
     *
     * @return \App\Models\Log[]
     */
    public function getByCategory(string $category, int $limit = 100): array;

    /**
     * Returns log entries matching the given severity level, newest first.
     *
     * @param string $level  The level to filter by (e.g. 'ERROR', 'WARNING').
     * @param int    $limit  Maximum number of entries to return.
     *
     * @return \App\Models\Log[]
     */
    public function getByLevel(string $level, int $limit = 100): array;

    /**
     * Returns the most recent log entries across all categories and levels.
     *
     * @param int $limit  Maximum number of entries to return.
     *
     * @return \App\Models\Log[]
     */
    public function getRecent(int $limit = 50): array;

    /**
     * Deletes log entries older than the given number of days.
     *
     * @param int $days  Entries older than this many days are removed.
     *
     * @return bool  True on success.
     */
    public function deleteOlderThan(int $days): bool;
}
