<?php

namespace Modules\Virtualcard\Enums;


enum VerificationDocumentTypes: string
{
    case PASSPORT = 'Passport';
    case NID = 'NID';
    case DRIVING_LICENSE = 'Driving License';
    case VOTER_ID = 'VoterID';
    case HEALTH_INSURANCE_CARD = 'Health Insurance Card';
    case EMPLOYEE_ID = 'EmployeeID';
    case TIN = 'TIN';
}
