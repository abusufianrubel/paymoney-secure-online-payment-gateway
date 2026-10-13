<?php

namespace Modules\Virtualcard\DataTables;

use App\Http\Helpers\Common;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Services\DataTable;
use Modules\Virtualcard\Entities\VirtualcardFeeslimit;

class VirtualcardFeesLimitsDataTable extends DataTable
{
    public function ajax(): JsonResponse
    {
        return datatables()
            ->eloquent($this->query())
            ->editColumn('virtualcard_provider_id', function ($feesLimits) {
                return "<div class='d-flex align-items-center gap-2'>
                            <span class='fw-semibold text-dark'>". optional($feesLimits->cardProvider)->name ."</span> 
                            <span class='badge bg-secondary'>". optional($feesLimits->cardProvider)->type ."</span>
                        </div>";

            })
            ->editColumn('topup_percentage_fee', function ($feesLimits) {
                return formatNumber($feesLimits->topup_percentage_fee);
            })
            ->editColumn('topup_fixed_fee', function ($feesLimits) {
                return formatNumber($feesLimits->topup_fixed_fee);
            })
            ->editColumn('topup_min_limit', function ($feesLimits) {
                return formatNumber($feesLimits->topup_min_limit);
            })
            ->editColumn('topup_max_limit', function ($feesLimits) {
                return formatNumber($feesLimits->topup_max_limit);
            })
            ->editColumn('withdrawal_percentage_fee', function ($feesLimits) {
                return formatNumber($feesLimits->withdrawal_percentage_fee);
            })
            ->editColumn('withdrawal_fixed_fee', function ($feesLimits) {
                return formatNumber($feesLimits->withdrawal_fixed_fee);
            })
            ->editColumn('withdrawal_min_limit', function ($feesLimits) {
                return formatNumber($feesLimits->withdrawal_min_limit);
            })
            ->editColumn('withdrawal_max_limit', function ($feesLimits) {
                return formatNumber($feesLimits->withdrawal_max_limit);
            })
            ->editColumn('status', function ($cardHolder) {
                return getStatusLabel($cardHolder->status);
            })
            ->addColumn('action', function ($feesLimits) {
                $edit = (Common::has_permission(auth()->guard('admin')->user()->id, 'edit_card_fees_limit')) ? '<a href="' . route('admin.card_fees.edit', $feesLimits->id) . '" class="btn btn-xs btn-primary"><i class="fa fa-edit"></i></a>&nbsp;' : '';

                $delete = (Common::has_permission(auth()->guard('admin')->user()->id, 'delete_card_fees_limit')) ? '<a href="' . route('admin.card_fees.destroy', $feesLimits->id) . '" class="btn btn-xs btn-danger delete-warning"><i class="fa fa-trash"></i></a>' : '';

                return $edit . $delete;
            })
            ->rawColumns(['virtualcard_provider_id', 'status', 'action'])
            ->make(true);
    }

    public function query()
    {
        $query = VirtualcardFeeslimit::with(['cardProvider:id,name,type'])->select('*');
        return $this->applyScopes($query);
    }

    public function html()
    {
        return $this->builder()
            ->addColumn(['data' => 'id', 'name' => 'virtualcard_feeslimits.id', 'title' => __('ID'), 'searchable' => false, 'visible' => false])
            ->addColumn(['data' => 'virtualcard_provider_id', 'name' => 'cardProvider.name', 'title' => __('Provider')])
            ->addColumn(['data' => 'virtualcard_currency_code', 'name' => 'virtualcard_feeslimits.virtualcard_currency_code', 'title' => __('Currency')])
            ->addColumn(['data' => 'topup_percentage_fee', 'name' => 'virtualcard_feeslimits.topup_percentage_fee', 'title' => __('Topup Percentage Fee')])
            ->addColumn(['data' => 'topup_fixed_fee', 'name' => 'virtualcard_feeslimits.topup_fixed_fee', 'title' => __('Topup Fixed Fee')])
            ->addColumn(['data' => 'topup_min_limit', 'name' => 'virtualcard_feeslimits.topup_min_limit', 'title' => __('Topup Min Limit')])
            ->addColumn(['data' => 'topup_max_limit', 'name' => 'virtualcard_feeslimits.topup_max_limit', 'title' => __('Topup Max Limit')])
            ->addColumn(['data' => 'withdrawal_percentage_fee', 'name' => 'virtualcard_feeslimits.withdrawal_percentage_fee', 'title' => __('Withdrawal Percentage Fee')])
            ->addColumn(['data' => 'withdrawal_fixed_fee', 'name' => 'virtualcard_feeslimits.withdrawal_fixed_fee', 'title' => __('Withdrawal Fixed Fee')])
            ->addColumn(['data' => 'withdrawal_min_limit', 'name' => 'virtualcard_feeslimits.withdrawal_min_limit', 'title' => __('Withdrawal Min Limit')])
            ->addColumn(['data' => 'withdrawal_max_limit', 'name' => 'virtualcard_feeslimits.withdrawal_max_limit', 'title' => __('Withdrawal Max Limit')])
            ->addColumn(['data' => 'status', 'name' => 'virtualcard_holders.status', 'title' => __('Status')])
            ->addColumn(['data'  => 'action', 'name'  => 'action', 'title' => __('Action'), 'orderable' => false, 'searchable' => false])
            ->parameters(dataTableOptions());
    }
}
