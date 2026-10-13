<?php

namespace Modules\KycVerification\Datatable;

use App\Http\Helpers\Common;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Services\DataTable;
use Modules\KycVerification\Entities\KycProvider;

class ProvidersDatatable extends DataTable
{
    public function ajax(): JsonResponse
    {
        return datatables()
            ->eloquent($this->query())
            ->editColumn('name', function ($provider) {
                return $provider->name;
            })
            ->editColumn('is_default', function ($provider) {
                return isDefault($provider->is_default);
            })
            ->addColumn('action', function ($provider) {
                
                $edit = (Common::has_permission(auth('admin')->id(), 'edit_kyc_provider')) ? '<a href="' . route('admin.kyc.providers.edit', $provider->id) . '" class="btn btn-xs btn-primary"><i class="fa fa-edit"></i></a>&nbsp;' : '';

                $credential = Common::has_permission(auth('admin')->user()->id, 'view_kyc_credential_setting') && verifyModulesCredentialRequirement($provider->alias) && $provider->is_default == 'Yes' ? '<a href="' . route('admin.kyc.credentials.active-provider-setting') . '" class="btn btn-xs btn-success" data-bs-toggle="tooltip" data-bs-placement="top" title="' . __('Credential Settings') . '"><i class="fa fa-cog"></i></a>&nbsp;' : '';

                return $edit . $credential;
            })
            ->rawColumns(['is_default', 'action'])
            ->make(true);
    }

    public function query()
    {
        $query = KycProvider::select('id', 'name', 'is_default', 'alias');
        return $this->applyScopes($query);
    }

    public function html()
    {
        return $this->builder()
            ->addColumn([
                'data' => 'id',
                'name' => 'kyc_providers.id',
                'title' => __('ID'),
                'searchable' => false,
                'visible' => false
            ])
            ->addColumn([
                'data' => 'name',
                'name' => 'kyc_providers.name',
                'title' => __('Provider')
            ])
            ->addColumn([
                'data' => 'is_default',
                'name' => 'kyc_providers.is_default',
                'title' => __('Default')
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
