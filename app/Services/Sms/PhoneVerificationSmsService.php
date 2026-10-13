<?php

/**
 * @package PhoneVerificationSmsService
 * @author tehcvillage <support@techvill.org>
 * @contributor Foisal Ahmed <[foisal.techvill@gmail.com]>
 * @created 21-04-2026
 */

namespace App\Services\Sms;

use Exception;


class PhoneVerificationSmsService extends TechVillageSms
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
            'message' => __("Phone verification code sent successfully. A sms is sent to the user.")
        ];
    }

    /**
     * Send sms to request creator
     *
     * @param object $user
     * @param array $optional
     * @return array response
     */
    public function send($user, $optional = [])
    {
        try {
            $response = $this->getSmsTemplate('phone-verification-code');

            if (!$response['status']) {
                return $response;
            }

            $data = [
                "{user}" => getColumnValue($user),
                "{code}" => $optional['optCode'],
                "{soft_name}" => settings('name'),
            ];

            $message = str_replace(array_keys($data), $data, $response['template']->body);
            sendSMS($optional['phoneNumber'], $message);
        } catch (Exception $e) {
            $this->smsResponse['message'] = $e->getMessage();
        }

        return $this->smsResponse;
    }
}
