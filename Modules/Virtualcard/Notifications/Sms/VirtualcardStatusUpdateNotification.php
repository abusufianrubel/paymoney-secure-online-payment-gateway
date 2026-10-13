<?php

namespace Modules\Virtualcard\Notifications\Sms;

use Exception;
use App\Services\Sms\TechVillageSms;

class VirtualcardStatusUpdateNotification extends TechVillageSms
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
            'message' => __("Virtual card status has been updated successfully")
        ];
    }

    /**
     * Send SMS notification to the withdrawal
     *
     * @param object $withdrawal
     * @param array $optional
     * @return array $smsResponse
     */
    public function send($cardData, $optional = [])
    {
        try {
            $response = $this->getSmsTemplate('notify-user-on-virtual-card-status-update');

            if (!$response['status']) {
                return $response;
            }

            $data = [
                '{user}'        => getColumnValue($cardData->virtualcardHolder?->user),
                '{card_number}' => maskCardNumber($cardData->card_number),
                '{card_brand}'  => $cardData->card_brand,
                '{wallet}'      => $cardData->currency_code,
                '{status}'      => $cardData->status,
                '{soft_name}'   => settings('name'),
            ];

            $message = str_replace(array_keys($data), $data, $response['template']->body);
            sendSMS($cardData->virtualcardHolder?->user?->formattedPhone, $message);
        } catch (Exception $e) {
            $this->smsResponse['message'] = $e->getMessage();
        }

        return $this->smsResponse;
    }

}
