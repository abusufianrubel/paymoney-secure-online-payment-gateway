<?php

/**
 * @package WithdrawViaAdminSMSService
 * @author tehcvillage <support@techvill.org>
 * @contributor Foisal Ahmed <[foisal.techvill@gmail.com]>
 * @created 11-04-2026
 */

namespace App\Services\Sms;

use Exception;
use Illuminate\Support\Str;
use App\Services\Sms\TechVillageSms;

class WithdrawViaAdminSMSService extends TechVillageSms
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
            'message' => __('We have sent you the withdrawal details. Please check your sms.')
        ];
    }
    /**
     * Send withdrawal sms to user via admin
     * @param object $withdrawal
     * @return array $response
     */
    public function send($withdrawal, $optional = [])
    {
        try {
            $response = $this->getSmsTemplate('notify-user-on-withdrawal-via-admin');

            if (!$response['status']) {
                return $response;
            }

            $data = [
                "{user}" => getColumnValue($withdrawal->user),
                "{amount}" => moneyFormat(optional($withdrawal->currency)->symbol, formatNumber($withdrawal->amount)),
                "{soft_name}" => settings('name'),
            ];

            $message = str_replace(array_keys($data), $data, $response['template']->body);
            if (!empty($withdrawal->user?->formattedPhone)) {
                sendSMS($withdrawal->user?->formattedPhone, $message);
            }
        } catch (Exception $e) {
            $this->smsResponse = ['status' => false, 'message' => $e->getMessage()];
        }
        return $this->smsResponse;
    }
}
