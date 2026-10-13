<?php

namespace Modules\Virtualcard\Http\Controllers\Admin;

use Spatie\QueryBuilder\{
    QueryBuilder,
    AllowedFilter
};
use Modules\Virtualcard\Entities\{
    Virtualcard,
    VirtualcardHolder,
    VirtualcardProvider,
    VirtualcardCategory,
    VirtualcardSpendingControl,
};
use Modules\Virtualcard\{
    Responses\CardResponse,
    Exports\VirtualcardsExport,
    Events\VirtualcardStatusUpdate,
    Actions\UpsertVirtualcardAction,
    DataTables\VirtualcardsDataTable,
    DataTransferObject\VirtualcardData,
    Facades\VirtualcardProviderManager,
    Http\Requests\Admin\IssueVirtualcardRequest
};
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use Carbon\Carbon,Exception, Common, DB;

class VirtualcardController extends Controller
{
    public function __construct(

        private readonly UpsertVirtualcardAction $virtualCardAction

    ) {}

    public function index(VirtualcardsDataTable $dataTable)
    {
        return $dataTable->render('virtualcard::admin.virtualcards.index', [

            'menu' => 'virtualcard',
            'subMenu' => 'virtualcard',
            'virtualcardCurrencies' => $this->getDistinctValues('currency_code'),
            'virtualcardStatuses'   => $this->getDistinctValues('status'),
            'virtualcardBrands'     => $this->getDistinctValues('card_brand'),
            'filter' => request()->input('filter')

        ]);
    }

    public function show(Virtualcard $virtualcard)
    {
        $providers = VirtualcardProvider::whereHas('feesLimit', function ($query) {
                            $query->where('status', 'Active');
                        })
                        ->where('status', 'Active')
                        ->get(['id', 'name', 'type', 'status'])
                        ->filter(function ($provider) {
                            return ($provider->type === 'Manual' && isActive('ManualVirtualcard'))
                                    || ($provider->type === 'Automated' && isActive($provider->name . 'Virtualcard'));
                        })->values();

        return view('virtualcard::admin.virtualcards.show', [

            'menu' => 'virtualcard',
            'subMenu' => 'virtualcard',
            'virtualcard' => $virtualcard->load('virtualcardProvider', 'virtualcardCategory', 'virtualcardHolder'),
            'providers' => $providers,
            'cardHolder' => VirtualcardHolder::with('user:id,first_name,last_name')->find($virtualcard->virtualcardHolder?->id),
            'spendingControls' => VirtualcardSpendingControl::where('virtualcard_id', $virtualcard->id)->get(['id', 'virtualcard_id', 'amount', 'interval', 'spent']),
            'currency' => $virtualcard->currency(),

        ]);
    }

    public function edit(Virtualcard $virtualcard)
    {
        if ($virtualcard->virtualcardProvider->type == 'Automated') {
            Common::one_time_message('error', __('You cannot edit the automated card.'));
            return redirect()->route('admin.virtualcard.index');
        }

        $virtualcardProviders = VirtualcardProvider::whereHas('feesLimit', function($q) {
            return $q->whereNotNull('virtualcard_provider_id');
        })->whereStatus('Active')->get();

        return view('virtualcard::admin.virtualcards.edit', [

            'menu' => 'virtualcard',
            'subMenu' => 'virtualcard',
            'virtualcard' => $virtualcard,
            'virtualcardProviders' => $virtualcardProviders,
            'virtualcardCategories' => VirtualcardCategory::whereStatus('Active')->get(),

        ]);
    }

    public function update(Virtualcard $virtualcard, IssueVirtualcardRequest $request)
    {
        try {

            ($virtualcard->status == 'Freezed' && $request->status == 'Active') ? $status = 'UnFreeze' : $status = $request->status;
            $virtualcardData = VirtualcardData::fromRequest($request);
            $cardData = $this->virtualCardAction->execute($virtualcard, $virtualcardData);
            $cardData->status = $status;
            event(new VirtualcardStatusUpdate($cardData));
            Common::one_time_message('success', __('The :x has been successfully updated.', ['x' => __('card')]));
            return redirect()->route('admin.virtualcard.index');

        } catch (Exception $e) {

            Common::one_time_message('error', $e->getMessage());
            return redirect()->route('admin.virtualcard.edit', $virtualcard);

        }
    }

    protected function getDistinctValues(string $column)
    {
        return Virtualcard::select($column)->distinct()->get();
    }

    public function virtualcardCsv()
    {
        return Excel::download(new VirtualcardsExport(), 'virtualcards_' . time() . '.csv');
    }

    public function virtualcardPdf()
    {
        $data['virtualcards'] =  QueryBuilder::for(Virtualcard::with([
            'virtualcardProvider',
            'virtualcardCategory',
            'virtualcardHolder'
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
        ])
        ->orderBy('id', 'desc')
        ->whereNotIn('status', ['Pending', 'Declined'])
        ->take(1100)->get();

        $filter = request()->input('filter');

        $data['date_range'] = (isset($filter['from']) && isset($filter['to'])) ? $filter['from'] . ' To ' . $filter['to'] : 'N/A';

        generatePDF('virtualcard::admin.virtualcards.pdf', 'virtualcards_', $data);
    }

    public function action(Request $request) 
    {
        try {
            DB::beginTransaction();

            $virtualcard = Virtualcard::find($request->virtuacardId);

            if ($virtualcard->virtualcardProvider?->type == 'Automated' && checkDemoEnvironment() == true) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => __('Automated virtualcard status update is not allowed on the demo site.')
                ]);
            }

            if (! $virtualcard) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => __('Virtualcard not found.')
                ]);
            } 

            if ($request->action == 'active' && $virtualcard->status == 'Active') {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => __('The card is already active.')
                ]);
            }
            
            if ($request->action == 'inactive' && $virtualcard->status == 'Inactive') {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => __('The card is already inactive.')
                ]);
            }
       
            if ($virtualcard->virtualcardProvider?->type == 'Automated') {
                $providerHandler = VirtualcardProviderManager::get($virtualcard->virtualcardProvider?->name);
                $cardService = $providerHandler->getCardControlService();
                $cardResponse = $cardService->action($virtualcard->toArray(), ['status' => $request->action]);   
            } else {
                $providerHandler = VirtualcardProviderManager::get('manualvirtualcard');
                $cardService = $providerHandler->getCardControlService();
                $cardResponse = $cardService->action($virtualcard->toArray(), ['status' => $request->action]);   
            }

            if (! $cardResponse instanceof CardResponse) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => __('Invalid card response: Expected an instance of \Modules\Virtualcard\Responses\CardResponse, but received :x. Please verify that the card service is returning the correct data structure.', ['x' => get_class($cardResponse)])
                ]);
            }

            $virtualcard->status = ucfirst($cardResponse->status);
            $virtualcard->save();
            
            DB::commit();

            return response()->json([
                'status' => true,
                'title' => $request->action == 'active' ? __('Activated') : __('Deactivated'),
                'message' => __('Virtualcard :x successfully.', ['x' => $request->action == 'active' ? __('activated') : __('deactivated')])
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
