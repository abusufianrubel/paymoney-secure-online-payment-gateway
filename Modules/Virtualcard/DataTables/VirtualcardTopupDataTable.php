<?php

namespace Modules\Virtualcard\DataTables;

use App\Http\Helpers\Common;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Services\DataTable;
use Modules\Virtualcard\Entities\VirtualcardTopup;

class VirtualcardTopupDataTable extends DataTable
{
    public function ajax(): JsonResponse
    {
        return datatables()
            ->eloquent($this->query())
            ->addColumn('user_id', function ($cardTopup) {
                $sender = getColumnValue($cardTopup->user);
                $url = url(config('adminPrefix') . '/users/edit/' . $cardTopup->user_id);
                return '<a href="' . $url . '">' . $sender . '</a>';

            })
            ->editColumn('card_number', function ($cardTopup) {
                return '<a href="' . route('admin.virtualcard.show', $cardTopup->virtualcard) . '">' . virtualcardSvgIcons(strtolower(str_replace(' ', '_', $cardTopup->virtualcard?->card_brand)) . '_icon') .  maskCardNumberForLogo($cardTopup->virtualcard?->card_number) . '</a>';

            })->addColumn('currency_code', function ($cardTopup) {
                return optional($cardTopup->virtualcard)->currency_code;
            })->addColumn('requested_fund', function ($cardTopup) {
                return moneyFormat(currencyDetails(optional($cardTopup->virtualcard)->currency_code)->symbol, formatNumber($cardTopup->requested_fund, currencyDetails(optional($cardTopup->virtualcard)->currency_code)->id));
            })->addColumn('fees', function ($cardTopup) {
                $fees  = $cardTopup->percentage_fees + $cardTopup->fixed_fees;
                return moneyFormat(currencyDetails(optional($cardTopup->virtualcard)->currency_code)->symbol, formatNumber($fees, currencyDetails(optional($cardTopup->virtualcard)->currency_code)->id));
            })->addColumn('total', function ($cardTopup) {
                $fees  = $cardTopup->percentage_fees + $cardTopup->fixed_fees;
                $total = $fees + $cardTopup->requested_fund;
                return moneyFormat(currencyDetails(optional($cardTopup->virtualcard)->currency_code)->symbol, formatNumber($total, currencyDetails(optional($cardTopup->virtualcard)->currency_code)->id));
            })->addColumn('created_at', function ($cardTopup) {
                return dateFormat($cardTopup->created_at);
            })->editColumn('fund_approval_status', function ($cardTopup) {
                return getStatusLabel($cardTopup->fund_approval_status);
            })->addColumn('action', function ($cardTopup) {
                return (Common::has_permission(auth()->guard('admin')->user()->id, 'view_card_topup')) ? '<a href="' . route('admin.virtualcard_topup.show', $cardTopup->id ) . '" class="btn btn-xs btn-primary"><i class="fa fa-eye"></i></a>&nbsp;' : '';
            })
            ->rawColumns(['user_id', 'card_number', 'fund_approval_status', 'action'])
            ->make(true);
    }

    public function query()
    {
        $status   = isset(request()->status) ? request()->status : 'all';
        $currency = isset(request()->currency) ? request()->currency : 'all';
        $brand    = isset(request()->brand) ? request()->brand : 'all';
        $user     = isset(request()->user_id) ? request()->user_id : null;
        $from     = isset(request()->from) ? setDateForDb(request()->from) : null;
        $to       = isset(request()->to) ? setDateForDb(request()->to) : null;
        $query    = (new VirtualcardTopup())->getVirtualcardTopupsList($from, $to, $status, $currency, $brand, $user);

        return $this->applyScopes($query);
    }

    public function html()
    {
        return $this->builder()
            ->addColumn(['data' => 'id', 'name' => 'virtualcard_topups.id', 'title' => __('ID'), 'searchable' => false, 'visible' => false])
            ->addColumn(['data' => 'user_id', 'name' => 'user.first_name', 'title' => __('User')])
            ->addColumn(['data' => 'card_number', 'name' => 'virtualcard.card_number', 'title' => __('Card Number')])
            ->addColumn(['data' => 'currency_code', 'name' => 'virtualcard.currency_code', 'title' => __('Currency Code')])
            ->addColumn(['data' => 'requested_fund', 'name' => 'virtualcard_topups.requested_fund', 'title' => __('Amount')])
            ->addColumn(['data' => 'fees', 'name' => 'fees', 'title' => __('Fees')])
            ->addColumn(['data' => 'total', 'name' => 'total', 'title' => __('Total')])
            ->addColumn(['data' => 'created_at', 'name' => 'virtualcard_topups.created_at', 'title' => __('Date')])
            ->addColumn(['data' => 'fund_approval_status', 'name' => 'virtualcard_topups.fund_approval_status', 'title' => __('Status')])
            ->addColumn(['data' => 'action', 'name' => 'action', 'title' => __('Action'), 'orderable' => false, 'searchable' => false])
            ->parameters(dataTableOptions());
    }
}
