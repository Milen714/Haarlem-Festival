<?php

namespace App\Repositories;

use App\Repositories\Interfaces\IHistoryRepository;
use App\Models\HistoryTourPrice;
use App\Framework\Repository;

class HistoryRepository extends Repository implements IHistoryRepository
{

    public function __construct() {
        $this->pdo = $this->connect();
    }

    public function getAvailableTourOptions(): array
    {
        $sql = "SELECT DISTINCT
                    ttype.ticket_type_id AS ticket_type_id,
                    sched.date AS date,
                    sched.start_time AS time,
                    tschem.ticket_language AS language,
                    tschem.scheme_enum AS scheme_enum
                FROM TICKET_TYPE ttype
                INNER JOIN SCHEDULE sched ON ttype.schedule_id = sched.schedule_id
                INNER JOIN TICKET_SCHEME tschem ON ttype.scheme_id = tschem.ticket_scheme_id
                WHERE ttype.is_sold_out = 0
                  AND tschem.scheme_enum LIKE 'HISTORY_%'
                ORDER BY sched.date ASC, sched.start_time ASC, tschem.ticket_language ASC";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new \RuntimeException('Failed to fetch available tour options: ' . $e->getMessage(), 0, $e);
        }
    }

    public function getTourTicketPrices(): array
    {
        $sql = "SELECT scheme_enum, price
                FROM TICKET_SCHEME
                WHERE scheme_enum IN ('HISTORY_SINGLE_TICKET', 'HISTORY_FAMILY_TICKET')";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $prices = [
                'normal' => 0.0,
                'family' => 0.0
            ];

            foreach ($rows as $row) {
                if ($row['scheme_enum'] === 'HISTORY_SINGLE_TICKET') {
                    $prices['normal'] = (float)$row['price'];
                } elseif ($row['scheme_enum'] === 'HISTORY_FAMILY_TICKET') {
                    $prices['family'] = (float)$row['price'];
                }
            }

            return $prices;
        } catch (\PDOException $e) {
            throw new \RuntimeException('Failed to fetch tour ticket prices: ' . $e->getMessage(), 0, $e);
        }
    }
}