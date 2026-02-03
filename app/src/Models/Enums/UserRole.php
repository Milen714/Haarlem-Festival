<?php
namespace App\Models;
enum UserRole: string{
    case CUSTOMER = 'CUSTOMER';
    case EMPLOYEE = 'EMPLOYEE';
    case ADMIN = 'ADMIN';

    
}