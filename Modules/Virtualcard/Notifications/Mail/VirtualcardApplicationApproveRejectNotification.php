<?php

namespace Modules\Virtualcard\Notifications\Mail;

use Exception;
use App\Services\Mail\TechVillageMail;

class VirtualcardApplicationApproveRejectNotification extends TechVillageMail
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
            'message' => __('Virtual card Application status has been updated successfully')
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

        try {
            $response = $this->getEmailTemplate('notify-user-on-virtual-card-application-approve-reject');

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

            $subject = str_replace("{Approve/Reject}", $applicationData->status, $response['template']->subject);
            $message = str_replace(array_keys($data), $data, $response['template']->body);

            $this->email->sendEmail(optional($applicationData->user)->email, $subject, $message);
        } catch (Exception $e) {
            $this->mailResponse = ['status' => false, 'message' => $e->getMessage()];
        }

        return $this->mailResponse;
    }
}
