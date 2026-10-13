<?php

namespace Modules\Virtualcard\DataTables;

use Spatie\QueryBuilder\{
    AllowedFilter,
    QueryBuilder
};
use Carbon\Carbon;
use App\Http\Helpers\Common;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Services\DataTable;
use Modules\Virtualcard\Entities\Virtualcard;

class VirtualcardsDataTable extends DataTable
{
    public function ajax(): JsonResponse
    {
        return datatables()
            ->eloquent($this->query())
            ->editColumn('user', function ($virtualcard) {
                return getColumnValue($virtualcard->virtualcardHolder?->user);
            })
            ->editColumn('holder', function ($virtualcard) {
                return cardTitle($virtualcard->virtualcardHolder);
            })
            ->editColumn('number', function ($virtualcard) {
                return $virtualcard->card_number ? virtualcardSvgIcons(strtolower(str_replace(' ', '_', $virtualcard->card_brand)) . '_icon') .  maskCardNumberForLogo($virtualcard->card_number) : '-';
            })
            ->addColumn('currency', function ($virtualcard) {
                return $virtualcard->currency_code;
            })
            ->addColumn('provider', function ($virtualcard) {
                $providerName = $virtualcard->virtualcardProvider?->name ?? '-';
                $providerType = $virtualcard->virtualcardProvider?->type ?? '';

                return "<div class='d-flex align-items-center gap-2'>
                            <span class='fw-semibold text-dark'>". $providerName ."</span>
                            <span class='badge bg-secondary'>". $providerType ."</span>
                        </div>";
            })

            ->addColumn('expired', function ($virtualcard) {
                return $virtualcard->expiry_month && $virtualcard->expiry_year ? formatCardExpiryDate($virtualcard->expiry_month, $virtualcard->expiry_year) : '-';
            })
            ->addColumn('assigned', function ($virtualcard) {
                return dateFormat($virtualcard->created_at);
            })
            ->addColumn('status', function ($virtualcard) {
                return getStatusLabel($virtualcard->status == 'Canceled' ? 'cancelled' : $virtualcard->status) ? getStatusLabel($virtualcard->status == 'Canceled' ? 'cancelled' : $virtualcard->status) : '';
            })
            ->addColumn('action', function ($virtualcard) {
                $edit = $show = '';

                if (in_array($virtualcard->status, ['Active', 'Inactive', 'Freezed', 'Expired']) && $virtualcard->virtualcardProvider?->type == 'Manual') {
                    $edit = (Common::has_permission(auth()->guard('admin')->user()->id, 'edit_virtual_card')) ? '<a href="' . route('admin.virtualcard.edit', $virtualcard) . '" class="btn btn-xs btn-primary"><i class="fa fa-edit f-14"></i></a>&nbsp' : '';
                }

                $show = (Common::has_permission(auth()->guard('admin')->user()->id, 'view_virtual_card')) ? '<a href="' . route('admin.virtualcard.show', $virtualcard) . '" class="btn btn-xs btn-primary"><i class="fa fa-eye f-14"></i></a>&nbsp' : '';

                return $edit . $show;
            })
            ->rawColumns(['number', 'provider', 'status', 'action'])
            ->make(true);
    }

    public function query()
    {
        $queryBuilder = QueryBuilder::for(Virtualcard::with([
                            'virtualcardProvider',
                            'virtualcardCategory',
                            'virtualcardHolder',
                            'virtualcardHolder.user'
                        ]))
                        ->allowedFilters([
                            'card_type',
                            'status',
                            AllowedFilter::exact('brand', 'card_brand'),
                            AllowedFilter::exact('currency', 'currency_code'),
                            AllowedFilter::callback('from', function ($query, $value) {
                                $query->where('created_at', '>=', Carbon::parse(setDateForDb($value))->startOfDay() );
                            }),
                            AllowedFilter::callback('to', function ($query, $value) {
                                $query->where('created_at', '<=', Carbon::parse(setDateForDb($value))->endOfDay() );
                            }),
                        ]);

        return $this->applyScopes($queryBuilder->getEloquentBuilder());
    }

    public function html()
    {
        return $this->builder()
            ->addColumn(['data' => 'id', 'name' => 'virtualcards.id', 'title' => __('ID'), 'searchable' => false, 'visible' => false])

            ->addColumn(['data' => 'user', 'name' => 'virtualcardHolder.user.first_name', 'title' => __('User'), 'visible' => false])

            ->addColumn(['data' => 'user', 'name' => 'virtualcardHolder.user.last_name', 'title' => __('User')])

            ->addColumn(['data' => 'holder', 'name' => 'virtualcardHolder', 'title' => __('Card Holder')])
            ->addColumn(['data' => 'number', 'name' => 'virtualcards.card_number', 'title' => __('Number')])
            ->addColumn(['data' => 'currency', 'name' => 'virtualcards.currency_code', 'title' => __('Currency')])
            ->addColumn(['data' => 'provider', 'name' => 'virtualcardProvider.name', 'title' => __('Providers')])
            ->addColumn(['data' => 'expired', 'name' => 'virtualcards.expiry_year', 'title' => __('Expired')])
            ->addColumn(['data' => 'assigned', 'name' => 'virtualcards.created_at', 'title' => __('Assigned')])
            ->addColumn(['data' => 'status', 'name' => 'virtualcards.status', 'title' => __('Status')])

            ->addColumn(['data' => 'action', 'name' => 'action', 'title' => __('Action'), 'orderable' => false, 'searchable' => false])
            ->parameters(dataTableOptions());
    }
}
