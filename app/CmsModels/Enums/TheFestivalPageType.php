<?php
namespace App\CmsModels\Enums;

enum TheFestivalPageType: string
{
    case homepage = 'homepage';
    case events = 'events';
    case event_schedule = 'event_schedule';
    case personal_schedule = 'personal_schedule';
    case shopping_cart = 'shopping_cart';
}