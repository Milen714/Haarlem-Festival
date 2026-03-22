<?php
namespace App\CmsModels\Enums;

enum PageType: string
{
    case homepage = 'homepage';
    case events = 'events';
    case event_schedule = 'event_schedule';
    case personal_schedule = 'personal_schedule';
    case shopping_cart = 'shopping_cart';
    case event_landing = 'event_landing';
    case performer_detail = 'performer_detail';
    case restaurants = 'restaurants';
    case restaurant_detail = 'restaurant_detail';
    case tour_detail = 'tour_detail';
    case venue_list = 'venue_list';
    case venue_detail = 'venue_detail';
}