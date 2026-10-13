<?php

namespace App\Services\Sms;

use Exception;

class RequestMoneyCancelSmsService extends TechVillageSms
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
            'message' => __('Request Money Cancellation Notification')
        ];
    }
    /**
     * Send request money cancellation notification to user SMS
     * @param object $requestPayment
     * @param array $optional
     * @return array $response
     */
    public function send($requestPayment, $optional = [])
    {
        try {
            $response = $this->getSmsTemplate('cancellation-of-request-payment');

            if (!$response['status']) {
                return $response;
            }

            $phoneNumber = $requestPayment->user_id == $optional['auth_id'] ? optional($requestPayment->receiver)->formattedPhone ?? $requestPayment->phone : optional($requestPayment->user)->formattedPhone;

            $userFullName = $requestPayment->user_id != $optional['auth_id'] ? getColumnValue($requestPayment->user) : (getColumnValue($requestPayment->receiver) != '-'  ? getColumnValue($requestPayment->receiver) : $requestPayment->phone);

            $data = [
                "{user}" => $userFullName,
                "{amount}" => moneyFormat(optional($requestPayment->currency)->symbol, formatNumber($requestPayment->amount, $requestPayment->currency_id)),
                "{uuid}" => $requestPayment->uuid,
                "{cancelled_by}" => $optional['cancelled_by'],
                "{soft_name}" => settings('name'),
            ];

            $message = str_replace(array_keys($data), $data, $response['template']->body);

            sendSMS($phoneNumber, $message);
        } catch (Exception $e) {
            $this->smsResponse = ['status' => false, 'message' => $e->getMessage()];
        }
        return $this->smsResponse;
    }
}
