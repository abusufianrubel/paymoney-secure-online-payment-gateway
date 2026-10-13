<?php

namespace Modules\Virtualcard\DataTables;

use App\Http\Helpers\Common;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Services\DataTable;
use Modules\Virtualcard\Entities\VirtualcardProvider;

class VirtualcardProviderDataTable extends DataTable
{
    public function ajax(): JsonResponse
    {
        return datatables()
            ->eloquent($this->query())
            ->editColumn('name', function ($virtualcardProvider) {
                return $virtualcardProvider->name;
            })
            ->editColumn('type', function ($virtualcardProvider) {
                return "<div class='d-flex align-items-center gap-2'>
                            <span class='badge bg-secondary'>". $virtualcardProvider->type ."</span>
                        </div>";
            })
            ->editColumn('status', function ($virtualcardProvider) {
                return  getStatusLabel($virtualcardProvider->status);
            })
            ->addColumn('action', function ($virtualcardProvider) {
    
                $delete = '';
                
                $edit = (Common::has_permission(auth()->guard('admin')->user()->id, 'edit_card_provider')) ? '<a href="' . route('admin.virtualcard_provider.edit', $virtualcardProvider->id) . '" class="btn btn-xs btn-primary"><i class="fa fa-edit"></i></a>&nbsp;' : '';
                
                if ($virtualcardProvider->type == 'Manual') {
                    $delete = (Common::has_permission(auth()->guard('admin')->user()->id, 'delete_card_provider')) ? '<a href="' . route('admin.virtualcard_provider.destroy', $virtualcardProvider->id) . '" class="btn btn-xs btn-danger delete-warning"><i class="fa fa-trash"></i></a>' : '';
                }
                

                return $edit . $delete;
            })
            ->rawColumns(['type', 'status', 'action'])
            ->make(true);
    }

    public function query()
    {
        $query = VirtualcardProvider::select('*');
        return $this->applyScopes($query);
    }

    public function html()
    {
        return $this->builder()
            ->addColumn(['data' => 'id', 'name' => 'virtualcard_providers.id', 'title' => __('ID'), 'searchable' => false, 'visible' => false])
            ->addColumn(['data' => 'name', 'name' => 'virtualcard_providers.name', 'title' => __('Name')])
            ->addColumn(['data' => 'type', 'name' => 'virtualcard_providers.type', 'title' => __('Type')])
            ->addColumn(['data' => 'status', 'name' => 'virtualcard_providers.status', 'title' => __('Status')])
            ->addColumn(['data'  => 'action', 'name'  => 'action', 'title' => __('Action'), 'orderable' => false, 'searchable' => false])
            ->parameters(dataTableOptions());
    }
}
