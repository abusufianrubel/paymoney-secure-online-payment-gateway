<?php

namespace Modules\Virtualcard\Notifications\Mail;

use Exception;
use App\Services\Mail\TechVillageMail;

class VirtualcardApplicationIssueNotification extends TechVillageMail
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
            $response = $this->getEmailTemplate('notify-user-on-virtual-card-issue');

            if (!$response['status']) {
                return $response;
            }

            $data = [
                '{user}'      => getColumnValue(optional($applicationData->virtualcardHolder)->user),
                '{status}'    => $applicationData->status,
                '{type}'      => ucfirst(optional($applicationData->virtualcardHolder)->card_holder_type),
                '{currency}'  => $applicationData->currency_code,
                '{soft_name}' => settings('name'),
            ];

            $message = str_replace(array_keys($data), $data, $response['template']->body);

            $this->email->sendEmail($applicationData->virtualcardHolder?->user?->email, $response['template']->subject, $message);
        } catch (Exception $e) {
            $this->mailResponse = ['status' => false, 'message' => $e->getMessage()];
        }

        return $this->mailResponse;
    }
}
