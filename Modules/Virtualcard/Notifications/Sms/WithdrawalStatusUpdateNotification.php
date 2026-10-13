<?php

namespace Modules\Virtualcard\Notifications\Sms;

use Exception;
use App\Services\Sms\TechVillageSms;

class WithdrawalStatusUpdateNotification extends TechVillageSms
{
    /**
     * The array of status and message whether SMS sent or not.
     *
     * @var array
     */
    protected $smsResponse = [];

    public function __construct()
    {
        parent::__construct();
        $this->smsResponse = [
            'status'  => true,
            'message' => __("Withdrawal status has been updated successfully")
        ];
    }

    /**
     * Send SMS notification to the withdrawal
     *
     * @param object $withdrawal
     * @param array $optional
     * @return array $smsResponse
     */
    public function send($withdrawalData, $optional = [])
    {
        try {
            $response = $this->getSmsTemplate('notify-user-on-withdrawal-status-update');

            if (!$response['status']) {
                return $response;
            }

            $data = [
                '{user}'        => getColumnValue($withdrawalData->user),
                '{amount}'      => moneyFormat($withdrawalData->virtualcard?->currency()?->symbol, formatNumber($withdrawalData->requested_fund)),
                '{card_number}' => maskCardNumber(optional($withdrawalData->virtualcard)->card_number),
                '{created_at}'  => dateFormat($withdrawalData->created_at, $withdrawalData->user_id),
                '{wallet}'      => optional($withdrawalData->virtualcard)->currency_code,
                '{status}'      => $withdrawalData->fund_approval_status,
                '{fee}'         => moneyFormat($withdrawalData->virtualcard?->currency()?->symbol, formatNumber($withdrawalData->fixed_fees + $withdrawalData->percentage_fees)),
                '{soft_name}'   => settings('name'),
            ];

            $message = str_replace(array_keys($data), $data, $response['template']->body);
            sendSMS(optional($withdrawalData->user)->formattedPhone, $message);
        } catch (Exception $e) {
            $this->smsResponse['message'] = $e->getMessage();
        }

        return $this->smsResponse;
    }

}
