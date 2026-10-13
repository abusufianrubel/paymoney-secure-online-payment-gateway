<?php

namespace Modules\Virtualcard\Http\Controllers\Admin;

use Common, Exception, DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Virtualcard\Entities\VirtualcardHolder;
use Modules\Virtualcard\Responses\CardHolderResponse;
use Modules\Virtualcard\Entities\VirtualcardProvider;
use Modules\Virtualcard\Exports\VirtualcardHolderExport;
use Modules\Virtualcard\Facades\VirtualcardProviderManager;
use Modules\Virtualcard\DataTables\VirtualcardHolderDataTable;
use Modules\Virtualcard\Events\VirtualcardApplicationApproveReject;

class VirtualcardHolderController extends Controller
{
    protected $helper;
    protected $virtualcardHolder;

    public function __construct()
    {
        $this->helper  = new Common();
        $this->virtualcardHolder = new VirtualcardHolder();
    }

    public function index(VirtualcardHolderDataTable $dataTable)
    {
        $data['menu']     = 'virtualcard';
        $data['sub_menu'] = 'cardHolder';

        $data['holderType'] = $this->getCardHolderDistinctValues('card_holder_type');
        $data['statuses']   = $this->getCardHolderDistinctValues('status');
        $data['countries']  = $this->getCardHolderDistinctValues('country');
        $data['from']     = isset(request()->from) ? setDateForDb(request()->from) : null;
        $data['to']       = isset(request()->to ) ? setDateForDb(request()->to) : null;
        $data['status']   = isset(request()->status) ? request()->status : 'all';
        $data['country']  = isset(request()->country) ? request()->country : 'all';
        $data['user']     = $user = isset(request()->user_id) ? request()->user_id : null;
        $data['getName']  = $this->virtualcardHolder->virtualcardHoldersUsersName($user);
        $data['cardHolderType'] = isset(request()->card_holder_type) ? request()->card_holder_type : 'all';

        return $dataTable->render('virtualcard::admin.virtualcard_holders.index', $data);
    }

    public function show($id)
    {

        $virtualcardHolder = VirtualcardHolder::with('user:id,first_name,last_name')->find($id);

        if ($virtualcardHolder) {

            // Only those providers will be shown which have fees limit and addon is active
            $virtualcardProviders = VirtualcardProvider::whereHas('feesLimit', function ($query) {
                                           $query->where('status', 'Active');
                                        })
                                        ->where('status', 'Active')
                                        ->get(['id', 'name', 'type', 'status'])
                                        ->filter(function ($provider) {
                                            return ($provider->type === 'Manual' && isActive('ManualVirtualcard'))
                                                    || ($provider->type === 'Automated' && isActive($provider->name . 'Virtualcard'));
                                        })->values();

            return view('virtualcard::admin.virtualcard_holders.show', [

                'menu' => 'virtualcard',
                'sub_menu' => 'cardHolder',
                'cardHolder' => $virtualcardHolder,
                'providers' => $virtualcardProviders,

            ]);
        }

        (new Common)->one_time_message('error', __('The :x does not exist.', ['x' => __('holder')]));
        return redirect()->route('admin.virtualcard_holder.index');
    }

    public function approve(VirtualcardHolder $virtualcardHolder, VirtualcardProvider $provider)
    {
        try {
            DB::beginTransaction();

            if ($provider->type == 'Manual') {
                if (!m_g_c_v('TUFOVUFMVklSVFVBTENBUkRfU0VDUkVU') && m_aic_c_v('TUFOVUFMVklSVFVBTENBUkRfU0VDUkVU')) {
                    return view('addons::install', ['module' => 'TUFOVUFMVklSVFVBTENBUkRfU0VDUkVU']);
                }
                
                $providerHandler = VirtualcardProviderManager::get('manualvirtualcard');
                $cardService = $providerHandler->getCardHolderService();
                $cardholder = $cardService->createCardHolder($virtualcardHolder->toArray());
            } else {
                if (checkDemoEnvironment()) {
                    $this->helper->one_time_message('warning', __('Automated cardholder application approval is not allowed on the demo site.'));
                    return redirect()->back();
                }
                
                if (!m_g_c_v('U1RSSVBFVklSVFVBTENBUkRfU0VDUkVU') && m_aic_c_v('U1RSSVBFVklSVFVBTENBUkRfU0VDUkVU')) {
                    return view('addons::install', ['module' => 'U1RSSVBFVklSVFVBTENBUkRfU0VDUkVU']);
                }
                $providerHandler = VirtualcardProviderManager::get($provider->name);
                $cardService = $providerHandler->getCardHolderService();
                if (!isset($cardService->stripe)) {
                    throw new Exception(__('Please check your provider keys'));
                }
                $cardholder = $cardService->createCardHolder($virtualcardHolder->toArray());
            }

            if (! $cardholder instanceof CardHolderResponse) {
                throw new Exception(__('Invalid cardholder response: Expected an instance of Modules\Virtualcard\Responses\CardHolderResponse, but received :x. Please verify that the cardholder service is returning the correct data structure.', ['x' => get_class($cardholder)]));
            }

            $virtualcardHolder->virtualcard_provider_id  = $provider->id;
            $virtualcardHolder->api_holder_id = $cardholder->apiHolderId;
            $virtualcardHolder->api_holder_response = $cardholder->apiResponse;
            $virtualcardHolder->status = 'Approved';
            $virtualcardHolder->updated_at = $cardholder->createdAt;
            $virtualcardHolder->save();

            DB::commit();

            event(new VirtualcardApplicationApproveReject($virtualcardHolder));

            (new Common)->one_time_message('success', __('The card holder application has been successfully :x.', ['x' => __('approved')]));
            return redirect()->route('admin.virtualcard_holder.index');

        } catch (Exception $e) {
            (new Common)->one_time_message('error', $e->getMessage());
            return redirect()->route('admin.virtualcard_holder.show', $virtualcardHolder->id );
        }
    }

    public function reject(VirtualcardHolder $virtualcardHolder)
    {
        $virtualcardHolder->status = 'Rejected';
        $virtualcardHolder->save();
        event(new VirtualcardApplicationApproveReject($virtualcardHolder));

        (new Common)->one_time_message('success', __('The card holder application has been successfully :x.', ['x' => __('rejected')]));
        return redirect()->route('admin.virtualcard_holder.index');
    }

    public function cardUserSearch(Request $request)
    {
        $user  = $this->virtualcardHolder->getCardUsersResponse($request->search);
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

    protected function getCardHolderDistinctValues(string $column)
    {
        return $this->virtualcardHolder->select($column)->distinct()->get();
    }

    public function csv()
    {
        return Excel::download(new VirtualcardHolderExport(), 'card_holder_report_'. time() .'.csv');
    }

    public function pdf()
    {
        $from   = !empty(request()->from) ? setDateForDb(request()->from) : null;
        $to     = !empty(request()->to) ? setDateForDb(request()->to) : null;
        $status = isset(request()->status) ? request()->status : 'all';
        $holderType = isset(request()->holderType) ? request()->holderType : 'all';
        $country = isset(request()->country) ? request()->country : 'all';
        $user    = isset(request()->user_id) ? request()->user_id : null;
        $data['virtualcardHolders'] = $this->virtualcardHolder->getCardHoldersList($from, $to, $status, $country, $holderType, $user)->orderBy('id', 'desc')->get();

        $data['date_range'] = (isset($from) && isset($to)) ? $from . ' To ' . $to : 'N/A';

        generatePDF('virtualcard::admin.virtualcard_holders.card_holder_report_pdf', 'card_holder_report_', $data);
    }
}
