<?php

namespace Modules\Virtualcard\Enums;

enum CardHolderTypes: string
{
    case INDIVIDUAL = 'Individual';
    case BUSINESS = 'Business';
}