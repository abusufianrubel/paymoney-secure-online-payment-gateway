<?php

/**
 * @package DepositViaAdminSMSService
 * @author tehcvillage <support@techvill.org>
 * @contributor Foisal Ahmed <[foisal.techvill@gmail.com]>
 * @created 11-04-2026
 */

namespace App\Services\Sms;

use Exception;
use Illuminate\Support\Str;
use App\Services\Sms\TechVillageSms;

class DepositViaAdminSMSService extends TechVillageSms
{
    /**
     * The array of status and message whether sms sent or not.
     *
     * @var array
     */
    protected $smsResponse = [];

    public function __construct()
    {
        parent::__construct();
        $this->smsResponse = [
            'status'  => true,
            'message' => __('We have sent you the deposit details. Please check your sms.')
        ];
    }
    /**
     * Send deposit sms to user via admin
     * @param object $deposit
     * @return array $response
     */
    public function send($deposit, $optional = [])
    {
        try {
            $response = $this->getSmsTemplate('notify-user-on-deposit-via-admin');

            if (!$response['status']) {
                return $response;
            }

            $data = [
                "{user}" => getColumnValue($deposit->user),
                "{amount}" => moneyFormat(optional($deposit->currency)->symbol, formatNumber($deposit->amount)),
                "{soft_name}" => settings('name'),
            ];

            $message = str_replace(array_keys($data), $data, $response['template']->body);
            if (!empty($deposit->user?->formattedPhone)) {
                sendSMS($deposit->user?->formattedPhone, $message);
            }
        } catch (Exception $e) {
            $this->smsResponse = ['status' => false, 'message' => $e->getMessage()];
        }
        return $this->smsResponse;
    }
}
