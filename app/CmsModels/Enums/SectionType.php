<?php 
namespace App\CmsModels\Enums;
enum SectionType: string
{   
    case text = 'text';
    case image_left = 'image_left';
    case image_right = 'image_right';
    case image_top = 'image_top';
    case two_column = 'two_column';
    case divider = 'divider';
    case accordion_group = 'accordion_group';
    case hero_picture = 'hero_picture';
    case hero_gallery = 'hero_gallery';
    case event_left = 'event_left';
    case event_right = 'event_right';
    case image_bottom = 'image_bottom';
    case article = 'article';
    case landmark = 'landmark';
    case welcome = 'welcome';
    case book_tour = 'book_tour';  
    case tour_info = 'tour_info';
    case good_to_know = 'good_to_know';
    case tour_features = 'tour_features';
    case tour_tickets = 'tour_tickets';

}