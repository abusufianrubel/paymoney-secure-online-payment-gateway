<?php

namespace Modules\KycVerification\Datatable;

use App\Http\Helpers\Common;
use Illuminate\Http\JsonResponse;
use App\Models\DocumentVerification;
use Yajra\DataTables\Services\DataTable;

class VerificationsDatatable extends DataTable
{
    public function ajax(): JsonResponse
    {
        return datatables()
            ->eloquent($this->query())
            ->editColumn('created_at', function ($verification) {
                return dateFormat($verification->created_at);
            })
            ->addColumn('user_id', function ($verification) {
                return getColumnValue($verification->user);
            })
            ->addColumn('provider_id', function ($verification) {
                return getColumnValue($verification->provider, 'name', '');
            })
            ->editColumn('verification_type', function ($verification) {
                return $verification->verification_type;
            })
            ->editColumn('status', function ($verification) {
                return getStatusLabel($verification->status);
            })
            ->addColumn('action', function ($verification) {
                return (Common::has_permission(auth('admin')->id(), 'edit_kyc_verification')) ? '<a href="' . route('admin.kyc.verifications.edit', $verification->id) . '" class="btn btn-xs btn-primary"><i class="fa fa-edit"></i></a>&nbsp;' : '';
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    public function query()
    {
        $query = (new DocumentVerification())->getVerifications();
        return $this->applyScopes($query);
    }


    public function html()
    {
        return $this->builder()
            ->addColumn([
                'data' => 'id',
                'name' => 'document_verifications.id',
                'title' => __('ID'),
                'searchable' => false,
                'visible' => false
            ])
            ->addColumn([
                'data' => 'created_at',
                'name' => 'document_verifications.created_at',
                'title' => __('Date')
            ])
            ->addColumn([
                'data' => 'user_id',
                'name' => 'user.last_name',
                'title' => __('User'),
                'visible' => false
            ])
            ->addColumn([
                'data' => 'user_id',
                'name' => 'user.first_name',
                'title' => __('User')
            ])
            ->addColumn([
                'data' => 'provider_id',
                'name' => 'provider.name',
                'title' => __('Provider')
            ])
            ->addColumn([
                'data' => 'verification_type',
                'name' => 'document_verifications.verification_type',
                'title' => __('Type')
            ])
            ->addColumn([
                'data' => 'status',
                'name' => 'document_verifications.status',
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
