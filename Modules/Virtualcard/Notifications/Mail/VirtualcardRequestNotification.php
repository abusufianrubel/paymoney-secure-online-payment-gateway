<?php

namespace Modules\Virtualcard\Notifications\Mail;

use Exception;
use App\Services\Mail\TechVillageMail;

class VirtualcardRequestNotification extends TechVillageMail
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
            'message' => __('Virtual card Application has been send successfully')
        ];
    }
    /**
     * Send registration details to the topup via email
     * @param object $topup
     * @param array $optional
     * @return array $response
     */
    public function send($applicationData, $optional = [])
    {
        $recipient = getRecipientFromNotificationSetting($optional);
        try {
            $response = $this->getEmailTemplate('notify-admin-on-new-virtual-card-request');

            if (!$response['status']) {
                return $response;
            }

            $data = [
                '{admin}'     => $recipient['name'] ?? $recipient['email'],
                '{user}'      => getColumnValue(optional($applicationData->virtualcardHolder)->user),
                '{type}'      => ucfirst(optional($applicationData->virtualcardHolder)->card_holder_type),
                '{currency}'  => $applicationData->currency_code,
                '{soft_name}' => settings('name'),
            ];
            $message = str_replace(array_keys($data), $data, $response['template']->body);

            $this->email->sendEmail($recipient['email'], $response['template']->subject, $message);
        } catch (Exception $e) {
            $this->mailResponse = ['status' => false, 'message' => $e->getMessage()];
        }

        return $this->mailResponse;
    }
}
