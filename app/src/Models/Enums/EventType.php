<?php
namespace App\Models\Enums;

enum EventType: string
{
    case Yummy = 'Yummy';
    case History = 'History';
    case Magic = 'Magic';
    case Dance = 'Dance';
    case Jazz = 'Jazz';
}