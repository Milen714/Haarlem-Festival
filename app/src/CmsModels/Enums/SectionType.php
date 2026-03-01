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
}