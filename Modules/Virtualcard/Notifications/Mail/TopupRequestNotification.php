<?php

namespace Modules\Virtualcard\Notifications\Mail;

use Exception;
use App\Services\Mail\TechVillageMail;

class TopupRequestNotification extends TechVillageMail
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
            'message' => __('Topup request has been send successfully')
        ];
    }
    /**
     * Send registration details to the topup via email
     * @param object $topup
     * @param array $optional
     * @return array $response
     */
    public function send($topupData, $optional = [])
    {
        $recipient = getRecipientFromNotificationSetting($optional);

        try {
            $response = $this->getEmailTemplate('notify-admin-on-topup-request');

            if (!$response['status']) {
                return $response;
            }

            $data = [
                '{admin}'       => $recipient['name'] ?? $recipient['email'],
                '{user}'        => getColumnValue($topupData->user),
                '{amount}'      => moneyFormat($topupData->virtualcard?->currency()?->symbol, formatNumber($topupData->requested_fund)),
                '{card_number}' => maskCardNumber(optional($topupData->virtualcard)->card_number),
                '{created_at}'  => dateFormat($topupData->created_at, $topupData->user_id),
                '{uuid}'        => optional($topupData->transaction)->uuid,
                '{wallet}'      => optional($topupData->virtualcard)->currency_code,
                '{fee}'         => moneyFormat($topupData->virtualcard?->currency()?->symbol, formatNumber($topupData->fixed_fees + $topupData->percentage_fees)),
                '{soft_name}'   => settings('name'),
            ];

            $message = str_replace(array_keys($data), $data, $response['template']->body);

            $this->email->sendEmail($recipient['email'], $response['template']->subject, $message);
        } catch (Exception $e) {
            $this->mailResponse = ['status' => false, 'message' => $e->getMessage()];
        }

        return $this->mailResponse;
    }
}
