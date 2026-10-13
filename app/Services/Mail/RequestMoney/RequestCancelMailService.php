<?php

namespace App\Services\Mail\RequestMoney;

use Exception;
use App\Services\Mail\TechVillageMail;

class RequestCancelMailService extends TechVillageMail
{
    /**
     * The array of status and message whether email sent or not.
     *
     * @var array
     */
    protected $mailResponse = [];

    public function __construct()
    {
        parent::__construct();
        $this->mailResponse = [
            'status'  => true,
            'message' => __('Request Money Cancellation Notification')
        ];
    }
    /**
     * Send request money cancellation notification to user email
     * @param object $requestPayment
     * @param array $optional
     * @return array $response
     */
    public function send($requestPayment, $optional = [])
    {
        try {
            $response = $this->getEmailTemplate('cancellation-of-request-payment');

            if (!$response['status']) {
                return $response;
            }

            $userEmail = $requestPayment->user_id == $optional['auth_id'] ? optional($requestPayment->receiver)->email ?? $requestPayment->email : optional($requestPayment->user)->email;

            $userFullName = $requestPayment->user_id != $optional['auth_id'] ? getColumnValue($requestPayment->user) : (getColumnValue($requestPayment->receiver) != '-'  ? getColumnValue($requestPayment->receiver) : $requestPayment->email);

            $data = [
                "{user}" => $userFullName,
                "{amount}" => moneyFormat(optional($requestPayment->currency)->symbol, formatNumber($requestPayment->amount, $requestPayment->currency_id)),
                "{uuid}" => $requestPayment->uuid,
                "{cancelled_by}" => $optional['cancelled_by'],
                "{soft_name}" => settings('name'),
            ];

            $message = str_replace(array_keys($data), $data, $response['template']->body);

            $this->email->sendEmail($userEmail, $response['template']->subject, $message);
        } catch (Exception $e) {
            $this->mailResponse = ['status' => false, 'message' => $e->getMessage()];
        }
        return $this->mailResponse;
    }
}