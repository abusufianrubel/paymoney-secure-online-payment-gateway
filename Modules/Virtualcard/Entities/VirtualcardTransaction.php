<?php

namespace Modules\Virtualcard\Entities;

class VirtualcardTransaction
{
    public function getTransactionDetails($id)
    {
        return [
            'menu'      => 'transaction',
            'sub_menu'  => 'transactions',
            'transaction' => \App\Models\Transaction::with('virtualcardWithdrawal')->where('id', $id)->first()
        ];

    }

}
