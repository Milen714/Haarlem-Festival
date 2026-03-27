<?php

namespace App\Repositories\Interfaces;

use App\Models\Log;

interface ILogRepository
{
    /**
     * Persists a new log entry to the database.
     *
     * @param Log $log  The log entry to store.
     *
     * @return bool  True on success.
     */
    public function create(Log $log): bool;

    /**
     * Returns log entries matching the given category, newest first.
     *
     * @param string $category  The category to filter by (e.g. 'Jazz', 'Venue').
     * @param int    $limit     Maximum number of rows to return.
     *
     * @return Log[]
     */
    public function getByCategory(string $category, int $limit = 100): array;

    /**
     * Returns log entries matching the given level, newest first.
     *
     * @param string $level  The severity level to filter by (e.g. 'ERROR', 'DEBUG').
     * @param int    $limit  Maximum number of rows to return.
     *
     * @return Log[]
     */
    public function getByLevel(string $level, int $limit = 100): array;

    /**
     * Returns the most recent log entries across all categories and levels.
     *
     * @param int $limit  Maximum number of rows to return.
     *
     * @return Log[]
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
