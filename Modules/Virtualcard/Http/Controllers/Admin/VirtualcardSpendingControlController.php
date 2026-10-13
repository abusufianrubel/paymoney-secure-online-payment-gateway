<?php

namespace Modules\Virtualcard\Http\Controllers\Admin;

use Modules\Virtualcard\Entities\{
    Virtualcard,
    VirtualcardSpending,
    VirtualcardSpendingControl
};
use Exception, DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Virtualcard\Facades\VirtualcardProviderManager;
use Modules\Virtualcard\Responses\CardSpendingControlResponse;

class VirtualcardSpendingControlController extends Controller
{
    public function index()
    {
        $spendingControls = VirtualcardSpendingControl::where('virtualcard_id', request('virtualcardId'))->get(['amount', 'interval'])->toArray();
        return response()->json($spendingControls);
    }

    public function upsert()
    {
        try {
            DB::beginTransaction();

            $virtualcard = Virtualcard::with('virtualcardProvider')->where('id', request('virtualcardId'))->first();

            if ($virtualcard->virtualcardProvider?->type == 'Automated' && checkDemoEnvironment()) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => __('Automated virtualcard spending control update is not allowed on the demo site.')
                ]);
            }
            
            if (! $virtualcard) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => __('Virtualcard not found.')
                ]);
            }

            if ($virtualcard->virtualcardProvider?->type == 'Automated') {
                $providerHandler = VirtualcardProviderManager::get($virtualcard->virtualcardProvider?->name);
                $cardSpendingService = $providerHandler->getSpendingControlService();
                $cardSpendingResponse = $cardSpendingService->updateSpendingControl($virtualcard->toArray(), request('cardSpendingLimit'));   
            } else {
                $providerHandler = VirtualcardProviderManager::get('manualvirtualcard');
                $cardSpendingService = $providerHandler->getSpendingControlService();
                $cardSpendingResponse = $cardSpendingService->updateSpendingControl($virtualcard->toArray(), request('cardSpendingLimit'));   
            }

            if (! $cardSpendingResponse instanceof CardSpendingControlResponse) {
                throw new Exception(__('Invalid spending control response: Expected an instance of \Modules\Virtualcard\Responses\CardSpendingControlResponse, but received :x. Please verify that the spending control service is returning the correct data structure.', ['x' => get_class($cardSpendingResponse)]));
            }
            // Clearing old spending control data
            VirtualcardSpendingControl::where('virtualcard_id', request('virtualcardId'))->delete();

            // Taking the latest spending data
            $virtualcardSpending = VirtualcardSpending::where('virtualcard_id', request('virtualcardId'))->first(['id', 'amount']);

            // Update the new spending control data
            if (!empty ($cardSpendingResponse->limits)) {
                foreach ($cardSpendingResponse->limits as $spendingLimit) {
                    $responseData[] =[
                        'virtualcard_id' => request('virtualcardId'),
                        'amount' => $spendingLimit['amount'],
                        'interval' => $spendingLimit['interval'],
                        'spent' => $virtualcardSpending?->amount,
                    ]; 
                }
            
                VirtualcardSpendingControl::insert($responseData);
            }
            DB::commit();

            return response()->json([
                'status' => true,
                'title' => __('Success'),
                'message' => __('Spending control updated successfully.')
            ]);
            
           
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function limitExist(Request $request)
    {
        $formattedAmount = number_format($request->amountValue, 8, ".", ",");
    
        // Checking with newly added limit items
        if (!empty($request->cardSpendingLimit)) {
            foreach ($request->cardSpendingLimit as $cardSpendingLimit ) {
                if ($cardSpendingLimit['amount'] == $formattedAmount && $cardSpendingLimit['interval'] == $request->intervalValue) {
                    return response()->json([
                        'status' => true,
                        'message' => __('Limit already exists.')
                    ]);
                }
            }
        }

        // Checking with old database limit items
        $virtualcardSpendingControl = VirtualcardSpendingControl::where('amount', $formattedAmount)
                                        ->where('virtualcard_id', $request->virtualcardId)
                                        ->where('interval', $request->intervalValue)
                                        ->first();

       if ($virtualcardSpendingControl) {
            return response()->json([
                'status' => true,
                'message' => __('Limit already exists.')
            ]);
       } else {
            return response()->json([
                'status' => false,
                'message' => __('Limit does not exist.')
            ]);
       }
    }
}