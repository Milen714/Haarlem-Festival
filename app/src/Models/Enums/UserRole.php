<?php
namespace App\Models\Enums;
enum UserRole: string{
    case CUSTOMER = 'CUSTOMER';
    case EMPLOYEE = 'EMPLOYEE';
    case ADMIN = 'ADMIN';

    
}