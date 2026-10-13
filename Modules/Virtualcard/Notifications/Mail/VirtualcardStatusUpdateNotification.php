<?php

namespace Modules\Virtualcard\Notifications\Mail;

use Exception;
use App\Services\Mail\TechVillageMail;

class VirtualcardStatusUpdateNotification extends TechVillageMail
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
            'message' => __('Virtual card status has been updated successfully')
        ];
    }
    /**
     * Send registration details to the withdrawal via email
     * @param object $withdrawal
     * @param array $optional
     * @return array $response
     */
    public function send($cardData, $optional = [])
    {

        try {
            $response = $this->getEmailTemplate('notify-user-on-virtual-card-status-update');

            if (!$response['status']) {
                return $response;
            }

            $data = [
                '{user}'        => getColumnValue($cardData->virtualcardHolder?->user),
                '{card_brand}'  => $cardData->card_brand,
                '{wallet}'      => $cardData->currency_code,
                '{status}'      => $cardData->status,
                '{soft_name}'   => settings('name'),
            ];

            $message = str_replace(array_keys($data), $data, $response['template']->body);

            $this->email->sendEmail($cardData->virtualcardHolder?->user?->email, $response['template']->subject, $message);
        } catch (Exception $e) {
            $this->mailResponse = ['status' => false, 'message' => $e->getMessage()];
        }

        return $this->mailResponse;
    }
}
