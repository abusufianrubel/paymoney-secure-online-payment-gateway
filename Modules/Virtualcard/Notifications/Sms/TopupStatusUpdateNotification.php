<?php

namespace Modules\Virtualcard\Notifications\Sms;

use Exception;
use App\Services\Sms\TechVillageSms;

class TopupStatusUpdateNotification extends TechVillageSms
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
            'message' => __("Topup status has been updated successfully")
        ];
    }

    /**
     * Send SMS notification to the topup
     *
     * @param object $topup
     * @param array $optional
     * @return array $smsResponse
     */
    public function send($topupData, $optional = [])
    {
        try {
            $response = $this->getSmsTemplate('notify-user-on-topup-status-update');

            if (!$response['status']) {
                return $response;
            }

            $data = [
                '{user}'        => getColumnValue($topupData->user),
                '{amount}'      => moneyFormat($topupData->transaction?->currency?->symbol, formatNumber($topupData->requested_fund)),
                '{card_number}' => maskCardNumber(optional($topupData->virtualcard)->card_number),
                '{status}'      => $topupData->fund_approval_status,
                '{created_at}'  => dateFormat($topupData->created_at, $topupData->user_id),
                '{uuid}'        => optional($topupData->transaction)->uuid,
                '{wallet}'      => optional($topupData->virtualcard)->currency_code,
                '{fee}'         => moneyFormat($topupData->transaction?->currency?->symbol, formatNumber($topupData->fixed_fees + $topupData->percentage_fees)),
                '{soft_name}'   => settings('name'),
            ];

            $message = str_replace(array_keys($data), $data, $response['template']->body);
            sendSMS(optional($topupData->user)->formattedPhone, $message);
        } catch (Exception $e) {
            $this->smsResponse['message'] = $e->getMessage();
        }

        return $this->smsResponse;
    }

}
