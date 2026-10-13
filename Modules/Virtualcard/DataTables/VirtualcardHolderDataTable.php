<?php

namespace Modules\Virtualcard\DataTables;

use App\Http\Helpers\Common;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Services\DataTable;
use Modules\Virtualcard\Entities\VirtualcardHolder;

class VirtualcardHolderDataTable extends DataTable
{
    public function ajax(): JsonResponse
    {
        return datatables()
            ->eloquent($this->query())
            ->addColumn('card_holder_type', function ($cardHolder) {
                return ucwords($cardHolder->card_holder_type);
            })->addColumn('user_id', function ($cardHolder) {
                $sender = getColumnValue($cardHolder->user);
                $url = url(config('adminPrefix') . '/users/edit/' . $cardHolder->user_id);
                return '<a href="' . $url . '">' . $sender . '</a>';
            })->addColumn('name', function ($cardHolder) {
                
                $name = getColumnValue($cardHolder);
                if (\Modules\Virtualcard\Enums\CardHolderTypes::BUSINESS->value == $cardHolder->card_holder_type) {
                   $name = $cardHolder->business_name . ' (' . getColumnValue($cardHolder) . ')';
                }
                return $name;

            })->addColumn('country', function ($cardHolder) {
                return $cardHolder->country;
            })->editColumn('created_at', function ($cardHolder) {
                return dateFormat($cardHolder->created_at);
            })->editColumn('status', function ($cardHolder) {
                return getStatusLabel($cardHolder->status);
            })->addColumn('action', function ($cardHolder) {
                return (Common::has_permission(auth()->guard('admin')->user()->id, 'view_card_holder')) ? '<a href="' . route('admin.virtualcard_holder.show', $cardHolder->id ) . '" class="btn btn-xs btn-primary"><i class="fa fa-eye"></i></a>&nbsp;' : '';
            })
            ->rawColumns(['user_id', 'name', 'status', 'action'])
            ->make(true);
    }

    public function query()
    {
        $status   = isset(request()->status) ? request()->status : 'all';
        $country  = isset(request()->country) ? request()->country : 'all';
        $holderType = isset(request()->card_holder_type) ? request()->card_holder_type : 'all';
        $user     = isset(request()->user_id) ? request()->user_id : null;
        $from     = isset(request()->from) ? setDateForDb(request()->from) : null;
        $to       = isset(request()->to) ? setDateForDb(request()->to) : null;
        $query    = (new VirtualcardHolder())->getCardHoldersList($from, $to, $status, $country, $holderType, $user);

        return $this->applyScopes($query);
    }

    public function html()
    {
        return $this->builder()
            ->addColumn(['data' => 'id', 'name' => 'virtualcard_holders.id', 'title' => __('ID'), 'searchable' => false, 'visible' => false])
            ->addColumn(['data' => 'card_holder_type', 'name' => 'virtualcard_holders.card_holder_type', 'title' => __('Account Type')])
            ->addColumn(['data' => 'user_id', 'name' => 'user.first_name', 'title' => __('User'), 'visible' => false])
            ->addColumn(['data' => 'user_id', 'name' => 'user.last_name', 'title' => __('User')])

            ->addColumn(['data' => 'name', 'name' => 'virtualcard_holders.first_name', 'title' => __('Name'), 'visible' => false])
            ->addColumn(['data' => 'name', 'name' => 'virtualcard_holders.last_name', 'title' => __('Name'), 'visible' => false])
            ->addColumn(['data' => 'name', 'name' => 'virtualcard_holders.business_name', 'title' => __('Name')])
            
            ->addColumn(['data' => 'country', 'name' => 'virtualcard_holders.country', 'title' => __('Country')])
            ->addColumn(['data' => 'created_at', 'name' => 'virtualcard_holders.created_at', 'title' => __('Date')])
            ->addColumn(['data' => 'status', 'name' => 'virtualcard_holders.status', 'title' => __('Status')])
            ->addColumn(['data' => 'action', 'name' => 'action', 'title' => __('Action'), 'orderable' => false, 'searchable' => false])
            ->parameters(dataTableOptions());
    }
}
