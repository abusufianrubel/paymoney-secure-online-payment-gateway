<?php

namespace Modules\Virtualcard\Enums;

enum SpendingIntervals: string
{
    case EVERY_AUTHORIZATION = 'per_authorization';
    case EVERY_DAY = 'daily';
    case EVERY_WEEK = 'weekly';
    case EVERY_MONTH = 'monthly';
    case EVERY_YEAR = 'yearly';
    case ALL_TIME = 'all_time';
}
