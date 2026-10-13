<?php

namespace Modules\Virtualcard\Notifications\Sms;

use Exception;
use App\Services\Sms\TechVillageSms;

class VirtualcardApplicationApproveRejectNotification extends TechVillageSms
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
            'message' => __("Virtual card Application status has been updated successfully")
        ];
    }

    /**
     * Send SMS notification to the topup
     *
     * @param object $topup
     * @param array $optional
     * @return array $smsResponse
     */
    public function send($applicationData, $optional = [])
    {
        try {
            $response = $this->getSmsTemplate('notify-user-on-virtual-card-application-approve-reject');

            if (!$response['status']) {
                return $response;
            }

            $data = [
                '{user}'      => getColumnValue(optional($applicationData->user)),
                '{status}'    => $applicationData->status,
                '{type}'      => ucfirst(optional($applicationData)->card_holder_type),
                '{currency}'  => $applicationData->preferred_currency,
                '{soft_name}' => settings('name'),
            ];

            $message = str_replace(array_keys($data), $data, $response['template']->body);
            sendSMS(optional($applicationData->user)->formattedPhone, $message);
        } catch (Exception $e) {
            $this->smsResponse['message'] = $e->getMessage();
        }

        return $this->smsResponse;
    }

}
