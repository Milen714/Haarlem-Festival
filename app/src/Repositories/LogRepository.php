<?php

namespace App\Repositories;

use App\Framework\Repository;
use App\Models\Log;
use PDO;
use PDOException;

class LogRepository extends Repository
{
    public function create(Log $log): bool
    {
        try {
            $pdo = $this->connect();

            $query = "
                INSERT INTO LOGS (
                    level,
                    category,
                    message,
                    context,
                    stack_trace,
                    created_at
                ) VALUES (
                    :level,
                    :category,
                    :message,
                    :context,
                    :stack_trace,
                    :created_at
                )
            ";

            $stmt = $pdo->prepare($query);
            $stmt->bindValue(':level', $log->level, PDO::PARAM_STR);
            $stmt->bindValue(':category', $log->category, PDO::PARAM_STR);
            $stmt->bindValue(':message', $log->message, PDO::PARAM_STR);
            $stmt->bindValue(':context', $log->context ? json_encode($log->context) : null);
            $stmt->bindValue(':stack_trace', $log->stack_trace, PDO::PARAM_STR);
            $stmt->bindValue(':created_at', $log->created_at ?? date('Y-m-d H:i:s'));

            return $stmt->execute();
        } catch (PDOException $e) {
            throw new \RuntimeException("Failed to create log: " . $e->getMessage());
        }
    }

    public function getByCategory(string $category, int $limit = 100): array
    {
        try {
            $pdo = $this->connect();

            $query = "
                SELECT * FROM LOGS
                WHERE category = :category
                ORDER BY created_at DESC
                LIMIT :limit
            ";

            $stmt = $pdo->prepare($query);
            $stmt->bindValue(':category', $category, PDO::PARAM_STR);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();

            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return array_map(function (array $row) {
                $log = new Log();
                return $log->fromPDOData($row);
            }, $rows);
        } catch (PDOException $e) {
            throw new \RuntimeException("Failed to get logs by category: " . $e->getMessage());
        }
    }

    public function getByLevel(string $level, int $limit = 100): array
    {
        try {
            $pdo = $this->connect();

            $query = "
                SELECT * FROM LOGS
                WHERE level = :level
                ORDER BY created_at DESC
                LIMIT :limit
            ";

            $stmt = $pdo->prepare($query);
            $stmt->bindValue(':level', $level, PDO::PARAM_STR);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();

            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return array_map(function (array $row) {
                $log = new Log();
                return $log->fromPDOData($row);
            }, $rows);
        } catch (PDOException $e) {
            throw new \RuntimeException("Failed to get logs by level: " . $e->getMessage());
        }
    }

    public function getRecent(int $limit = 50): array
    {
        try {
            $pdo = $this->connect();

            $query = "
                SELECT * FROM LOGS
                ORDER BY created_at DESC
                LIMIT :limit
            ";

            $stmt = $pdo->prepare($query);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();

            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return array_map(function (array $row) {
                $log = new Log();
                return $log->fromPDOData($row);
            }, $rows);
        } catch (PDOException $e) {
            throw new \RuntimeException("Failed to get recent logs: " . $e->getMessage());
        }
    }

    public function deleteOlderThan(int $days): bool
    {
        try {
            $pdo = $this->connect();

            $query = "
                DELETE FROM LOGS
                WHERE created_at < DATE_SUB(NOW(), INTERVAL :days DAY)
            ";

            $stmt = $pdo->prepare($query);
            $stmt->bindValue(':days', $days, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            throw new \RuntimeException("Failed to delete old logs: " . $e->getMessage());
        }
    }
}