<?php

namespace App\Models;

use DateTime;

class Log
{
    public ?int $log_id = null;
    public string $level = 'INFO';
    public string $category = '';
    public string $message = '';
    public ?array $context = null;
    public ?string $stack_trace = null;
    public ?DateTime $created_at = null;

    public function fromPDOData(array $data): Log
    {
        $this->log_id = isset($data['log_id']) ? (int)$data['log_id'] : null;
        $this->level = $data['level'] ?? 'INFO';
        $this->category = $data['category'] ?? '';
        $this->message = $data['message'] ?? '';
        
        if (!empty($data['context'])) {
            $this->context = json_decode($data['context'], true);
        }
        
        $this->stack_trace = $data['stack_trace'] ?? null;
        
        if (!empty($data['created_at'])) {
            $this->created_at = new DateTime($data['created_at']);
        }

        return $this;
    }
}
