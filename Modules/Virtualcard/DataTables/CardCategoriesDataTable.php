<?php

namespace Modules\Virtualcard\DataTables;

use App\Http\Helpers\Common;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Services\DataTable;
use Modules\Virtualcard\Entities\VirtualcardCategory;

class CardCategoriesDataTable extends DataTable
{
    public function ajax(): JsonResponse
    {
        return datatables()
            ->eloquent($this->query())
            ->editColumn('name', function ($cardCategories) {
                return $cardCategories->name;
            })
            ->editColumn('status', function ($cardCategories) {
                return ($cardCategories->status == 'Active') ? getStatusLabel('Active') : getStatusLabel('Inactive');
            })
            ->addColumn('action', function ($cardCategories) {
                $edit = (Common::has_permission(auth()->guard('admin')->user()->id, 'edit_card_category')) ? '<a href="' . route('admin.card_categories.edit', $cardCategories->id) . '" class="btn btn-xs btn-primary"><i class="fa fa-edit"></i></a>&nbsp;' : '';

                $delete = (Common::has_permission(auth()->guard('admin')->user()->id, 'delete_card_category')) ? '<a href="' . route('admin.card_categories.destroy', $cardCategories->id) . '" class="btn btn-xs btn-danger delete-warning"><i class="fa fa-trash"></i></a>' : '';

                return $edit . $delete;
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    public function query()
    {
        $query = VirtualcardCategory::select('*');
        return $this->applyScopes($query);
    }

    public function html()
    {
        return $this->builder()
            ->addColumn(['data' => 'id', 'name' => 'virtualcard_categories.id', 'title' => __('ID'), 'searchable' => false, 'visible' => false])
            ->addColumn(['data' => 'name', 'name' => 'virtualcard_categories.name', 'title' => __('Name')])
            ->addColumn(['data' => 'status', 'name' => 'virtualcard_categories.status', 'title' => __('Status')])
            ->addColumn(['data'  => 'action', 'name'  => 'action', 'title' => __('Action'), 'orderable' => false, 'searchable' => false])
            ->parameters(dataTableOptions());
    }
}
