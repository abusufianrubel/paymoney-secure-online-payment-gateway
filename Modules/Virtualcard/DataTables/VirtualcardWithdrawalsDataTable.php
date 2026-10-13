<?php

namespace Modules\Virtualcard\DataTables;

use Spatie\QueryBuilder\{
    AllowedFilter,
    QueryBuilder
};
use Carbon\Carbon;
use App\Http\Helpers\Common;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Services\DataTable;
use Modules\Virtualcard\Entities\VirtualcardWithdrawal;

class VirtualcardWithdrawalsDataTable extends DataTable
{
    public function ajax(): JsonResponse
    {
        return datatables()
            ->eloquent($this->query())
            ->editColumn('user', function ($virtualcardWithdrawal) {
                return $virtualcardWithdrawal->user?->id ? '<a href="' . url(config('adminPrefix') . '/users/edit/' . $virtualcardWithdrawal->user->id) . '">' . getColumnValue($virtualcardWithdrawal->user) . '</a>' : '';
            })
            ->editColumn('card_number', function ($virtualcardWithdrawal) {
                return '<a href="' . route('admin.virtualcard.show', $virtualcardWithdrawal->virtualcard) . '">' . virtualcardSvgIcons(strtolower(str_replace(' ', '_', $virtualcardWithdrawal->virtualcard?->card_brand)) . '_icon') .  maskCardNumberForLogo($virtualcardWithdrawal->virtualcard?->card_number) . '</a>';
            })
            ->editColumn('amount', function ($virtualcardWithdrawal) {
                return formatNumber($virtualcardWithdrawal->requested_fund, $virtualcardWithdrawal->virtualcard?->currency()?->id);
            })
            ->addColumn('fees', function ($virtualcardWithdrawal) {
                return formatNumber($virtualcardWithdrawal->percentage_fees + $virtualcardWithdrawal->fixed_fees, $virtualcardWithdrawal->virtualcard?->currency()?->id);
            })
            ->addColumn('total', function ($virtualcardWithdrawal) {
                return formatNumber($virtualcardWithdrawal->requested_fund + $virtualcardWithdrawal->percentage_fees + $virtualcardWithdrawal->fixed_fees, $virtualcardWithdrawal->virtualcard?->currency()?->id);
            })
            ->editColumn('currency', function ($virtualcardWithdrawal) {
                return $virtualcardWithdrawal->virtualcard?->currency_code;
            })
            ->editColumn('request_at', function ($virtualcardWithdrawal) {
                return dateFormat($virtualcardWithdrawal->fund_request_time);
            })
            ->editColumn('release_at', function ($virtualcardWithdrawal) {
                return dateFormat($virtualcardWithdrawal->fund_release_time);
            })
            ->addColumn('status', function ($virtualcardWithdrawal) {
                return getStatusLabel($virtualcardWithdrawal->fund_approval_status);
            })
            ->addColumn('action', function ($virtualcardWithdrawal) {
                return (Common::has_permission(auth()->guard('admin')->user()->id, 'view_card_withdrawal')) ? '<a href="' . route('admin.virtualcard_withdrawal.edit', $virtualcardWithdrawal) . '" class="btn btn-xs btn-primary"><i class="fa fa-edit f-14"></i></a>&nbsp' : '';
            })
            ->rawColumns(['user', 'card_number', 'status', 'action'])
            ->make(true);
    }

    public function query()
    {
        $queryBuilder = QueryBuilder::for(VirtualcardWithdrawal::with('virtualcard', 'user'))
                    ->allowedFilters([
                        AllowedFilter::exact('status', 'fund_approval_status'),
                        AllowedFilter::exact('currency', 'virtualcard.currency_code'),
                        AllowedFilter::exact('first_name', ),
                        AllowedFilter::callback('from', function ($query, $value) {
                            $query->where('created_at', '>=', Carbon::parse(setDateForDb($value))->startOfDay());
                        }),
                        AllowedFilter::callback('to', function ($query, $value) {
                            $query->where('created_at', '<=', Carbon::parse(setDateForDb($value))->endOfDay());
                        }),
                        AllowedFilter::callback('user', function ($query, $value) {
                            $query->whereHas('user', function ($query) use ($value) {
                                $query->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'LIKE', "%$value%");
                            });
                        }),
                    ]);

        return $this->applyScopes($queryBuilder->getEloquentBuilder());
    }

    public function html()
    {
        return $this->builder()
            ->addColumn([
                'data' => 'id',
                'name' => 'virtualcard_withdrawals.id',
                'title' => __('ID'),
                'searchable' => false,
                'visible' => false
            ])
            ->addColumn([
                'data' => 'user',
                'name' => 'user.first_name',
                'title' => __('User')
            ])
            ->addColumn([
                'data' => 'user',
                'name' => 'user.last_name',
                'title' => __('User'),
                'visible' => false
            ])
            ->addColumn([
                'data' => 'card_number',
                'name' =>
                'virtualcard.card_number',
                'title' => __('Card')
            ])
            ->addColumn([
                'data' => 'amount',
                'name' => 'virtualcard_withdrawals.requested_fund',
                'title' => __('Amount')
            ])
            ->addColumn([
                'data' => 'fees',
                'name' => 'fees',
                'title' => __('Fees')
            ])
            ->addColumn([
                'data' => 'total',
                'title' => __('Total')
            ])
            ->addColumn([
                'data' => 'currency',
                'name' => 'virtualcard.currency_code',
                'title' => __('Currency')
            ])
            ->addColumn([
                'data' => 'request_at',
                'name' => 'virtualcard_withdrawals.fund_request_time',
                'title' => __('Requested')
            ])
            ->addColumn([
                'data' => 'release_at',
                'name' => 'virtualcard_withdrawals.fund_release_time',
                'title' => __('Completed')
            ])

            ->addColumn([
                'data' => 'status',
                'name' => 'virtualcard_withdrawals.fund_approval_status',
                'title' => __('Status')
            ])

            ->addColumn([
                'data' => 'action',
                'name' => 'action',
                'title' => __('Action'),
                'orderable' => false,
                'searchable' => false
            ])
            ->parameters(dataTableOptions());
    }
}
