<?php

/**
 * @package TowFaSmsService
 * @author tehcvillage <support@techvill.org>
 * @contributor Foisal Ahmed <[foisal.techvill@gmail.com]>
 * @created 20-08-2025
 */

namespace App\Services\Sms;

use Exception;


class TowFaSmsService extends TechVillageSms
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
            'message' => __("Tow factor code sent successfully. A sms is sent to the user.")
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
            $response = $this->getSmsTemplate('two-fa-authentication');

            if (!$response['status']) {
                return $response;
            }

            $data = [
                "{user}" => getColumnValue($user),
                "{code}" => $optional['optCode'],
                "{soft_name}" => settings('name'),
            ];

            $message = str_replace(array_keys($data), $data, $response['template']->body);
            sendSMS($user->formattedPhone, $message);
        } catch (Exception $e) {
            $this->smsResponse['message'] = $e->getMessage();
        }

        return $this->smsResponse;
    }
}
