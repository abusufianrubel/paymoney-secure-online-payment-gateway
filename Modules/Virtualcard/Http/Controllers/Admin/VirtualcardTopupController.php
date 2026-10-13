<?php

namespace Modules\Virtualcard\Http\Controllers\Admin;

use Exception;
use App\Http\Helpers\Common;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Virtualcard\Events\TopupStatusUpdate;
use Modules\Virtualcard\Entities\VirtualcardTopup;
use Modules\Virtualcard\Exports\VirtualcardTopupExport;
use Modules\Virtualcard\Services\VirtualcardTopupService;
use Modules\Virtualcard\DataTables\VirtualcardTopupDataTable;
use Modules\Virtualcard\Http\Requests\Admin\UpdateTopupStatusRequest;

class VirtualcardTopupController extends Controller
{
    protected $virtualcardTopup;

    public function __construct()
    {
        $this->virtualcardTopup = new VirtualcardTopup();
    }

    public function index(VirtualcardTopupDataTable $dataTable, VirtualcardTopupService $service)
    {
        $data['menu']     = 'virtualcard';
        $data['sub_menu'] = 'virtualcard_topup';

        $data['virtualcardBrands']        = $service->getDistinctCardValues('card_brand');
        $data['virtualcardTopupStatuses'] = $service->getDistinctValues('fund_approval_status');
        $data['virtualcardCurrencies']    = $service->getDistinctCardValues('currency_code');

        $data['from']     = isset(request()->from) ? setDateForDb(request()->from) : null;
        $data['to']       = isset(request()->to ) ? setDateForDb(request()->to) : null;
        $data['cardStatus']   = isset(request()->status) ? request()->status : 'all';
        $data['cardCurrency'] = isset(request()->currency) ? request()->currency : 'all';
        $data['user']     = $user = isset(request()->user_id) ? request()->user_id : null;
        $data['getName']  = $this->virtualcardTopup->virtualcardTopupsUsersName($user);
        $data['cardBrand'] = isset(request()->brand) ? request()->brand : 'all';

        return $dataTable->render('virtualcard::admin.virtualcard_topups.index', $data);
    }

    public function csv()
    {
        return Excel::download(new VirtualcardTopupExport(), 'card_topup_list_'. time() .'.csv');
    }

    public function pdf()
    {
        $from   = !empty(request()->from) ? setDateForDb(request()->from) : null;
        $to     = !empty(request()->to) ? setDateForDb(request()->to) : null;
        $status = isset(request()->status) ? request()->status : null;
        $brand  = isset(request()->brand) ? request()->brand : null;
        $user   = isset(request()->user_id) ? request()->user_id : null;
        $currency = isset(request()->currency) ? request()->currency : null;

        $data['virtualcardtopups'] = $this->virtualcardTopup->getVirtualcardTopupsList($from, $to, $status, $currency, $brand, $user)->orderBy('id', 'desc')->get();
        $data['date_range'] = (isset($from) && isset($to)) ? $from . ' To ' . $to : 'N/A';

        generatePDF('virtualcard::admin.virtualcard_topups.card_topup_report_pdf', 'card_topup_report_', $data);
    }

    public function show($id)
    {
        return view('virtualcard::admin.virtualcard_topups.show', [
            'menu' => 'virtualcard',
            'sub_menu' => 'virtualcard_topup',
            'cardTopup' => $this->virtualcardTopup->with([
                'user:id,first_name,last_name',
                'transaction:id,transaction_reference_id,transaction_type_id,uuid,total,percentage,charge_fixed',
                'virtualcard:id,card_brand,card_number,currency_code',
            ])->find($id),
        ]);
    }

    public function topupUserSearch(Request $request)
    {
        $user  = $this->virtualcardTopup->getTopupUsersResponse($request->search);
        $res   = [
            'status' => 'fail',
        ];
        if (count($user) > 0) {
            $res = [
                'status' => 'success',
                'data'   => $user,
            ];
        }
        return json_encode($res);
    }

    public function statusChange(UpdateTopupStatusRequest $request, VirtualcardTopupService $service, $id)
    {
        $redirectUrl = isset($request->topupStatusUpdate) && $request->topupStatusUpdate == 'Yes' ? redirect()->route('admin.virtualcard_topup.index') : redirect(config('adminPrefix').'/transactions');
        try {
            $topupData =  $service->topupStatusChange($request, $id);
            event(new TopupStatusUpdate($topupData));
            (new Common)->one_time_message('success', __('The :x status has been successfully changed.', ['x' => __('Topup')]));
            return $redirectUrl;

        } catch (Exception $e) {
            (new Common)->one_time_message('error', $e->getMessage());
            return $redirectUrl;

        }
    }

}
