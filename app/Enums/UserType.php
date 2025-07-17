<?php

namespace App\Enums;

enum UserType: string
{
    case ADMIN = 'admin';
    case EMPLOYEE = 'employee';
    case CLIENT = 'client';
}