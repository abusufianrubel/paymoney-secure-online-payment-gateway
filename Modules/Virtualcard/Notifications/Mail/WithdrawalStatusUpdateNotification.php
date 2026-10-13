<?php

namespace Modules\Virtualcard\Notifications\Mail;

use Exception;
use App\Services\Mail\TechVillageMail;

class WithdrawalStatusUpdateNotification extends TechVillageMail
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
            'message' => __('Withdrawal status has been updated successfully')
        ];
    }
    /**
     * Send registration details to the withdrawal via email
     * @param object $withdrawal
     * @param array $optional
     * @return array $response
     */
    public function send($withdrawalData, $optional = [])
    {

        try {
            $response = $this->getEmailTemplate('notify-user-on-withdrawal-status-update');

            if (!$response['status']) {
                return $response;
            }

            $data = [
                '{user}'        => getColumnValue($withdrawalData->user),
                '{amount}'      => moneyFormat($withdrawalData->virtualcard?->currency()?->symbol, formatNumber($withdrawalData->requested_fund)),
                '{card_number}' => maskCardNumber(optional($withdrawalData->virtualcard)->card_number),
                '{created_at}'  => dateFormat($withdrawalData->created_at, $withdrawalData->user_id),
                '{wallet}'      => optional($withdrawalData->virtualcard)->currency_code,
                '{status}'      => $withdrawalData->fund_approval_status,
                '{fee}'         => moneyFormat($withdrawalData->virtualcard?->currency()?->symbol, formatNumber($withdrawalData->fixed_fees + $withdrawalData->percentage_fees)),
                '{soft_name}'   => settings('name'),
            ];

            $message = str_replace(array_keys($data), $data, $response['template']->body);

            $this->email->sendEmail(optional($withdrawalData->user)->email, $response['template']->subject, $message);
        } catch (Exception $e) {
            $this->mailResponse = ['status' => false, 'message' => $e->getMessage()];
        }

        return $this->mailResponse;
    }
}
