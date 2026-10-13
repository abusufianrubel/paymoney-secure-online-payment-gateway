<?php

namespace Modules\Virtualcard\Http\Controllers\Admin;

use App\Models\{
    User,
    Wallet,
    Transaction
};
use Spatie\QueryBuilder\{
    QueryBuilder,
    AllowedFilter
};
use Modules\Virtualcard\Entities\{
    Virtualcard,
    VirtualcardWithdrawal
};
use Exception, DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Helpers\Common;
use Illuminate\Routing\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Virtualcard\Events\WithdrawalStatusUpdate;
use Modules\Virtualcard\Exports\VirtualcardWithdrawalsExport;
use Modules\Virtualcard\DataTables\VirtualcardWithdrawalsDataTable;

class VirtualcardWithdrawalController extends Controller
{
    private $helper;

    public function __construct()
    {
        $this->helper = new Common;
    }

    public function index(VirtualcardWithdrawalsDataTable $dataTable)
    {

        $statuses = VirtualcardWithdrawal::select('fund_approval_status')->distinct()->get();
        $currencies = Virtualcard::whereHas('virtualcardWithdrawals')->distinct()->pluck('currency_code');
        $users = User::whereHas('virtualcardWithdrawals')->select('id', 'first_name', 'last_name')->distinct()->get();

        return $dataTable->render('virtualcard::admin.virtualcard_withdrawals.index', [

            'menu' => 'virtualcard',
            'subMenu' => 'virtualcardWithdrawal',
            'virtualcardWithdrawalStatuses' => $statuses,
            'virtualcardWithdrawalCurrencies' => $currencies,
            'virtualcardWithdrawalUsers' => $users,
            'filter' => request()->input('filter')

        ]);
    }

    public function edit(VirtualcardWithdrawal $virtualcardWithdrawal)
    {
        return view('virtualcard::admin.virtualcard_withdrawals.edit', [

            'menu' => 'virtualcard',
            'subMenu' => 'virtualcardWithdrawal',
            'virtualcardWithdrawal' => $virtualcardWithdrawal->load('virtualcard', 'user', 'transaction')

        ]);
    }

    public function update(Request $request, VirtualcardWithdrawal $virtualcardWithdrawal)
    {
        $request->validate([
            'fund_approval_status' => 'sometimes|required|in:Approved,Pending,Cancelled',
        ]);
            try {
            if ($request->fund_approval_status == 'Approved') {

                if ($virtualcardWithdrawal->fund_approval_status == 'Cancelled') {

                    # Virtualcard
                    $virtualcard = Virtualcard::where('id', $virtualcardWithdrawal->virtualcard_id)->first();
                    $virtualcard->amount -= ($virtualcardWithdrawal->requested_fund + $virtualcardWithdrawal->fixed_fees + $virtualcardWithdrawal->percentage_fees);
                    $virtualcard->save();

                    # Wallet
                    $userWallet = Wallet::where('user_id', $virtualcardWithdrawal->user_id)->first();
                    $userWallet->balance = $userWallet->balance + $virtualcardWithdrawal->requested_fund;
                    $userWallet->save();

                    # Withdrawal
                    $virtualcardWithdrawal->fund_approval_status = $request->fund_approval_status;
                    $virtualcardWithdrawal->fund_release_time = now();
                    $virtualcardWithdrawal->cancellation_reason = null;
                    $virtualcardWithdrawal->save();

                    # Transaction
                    Transaction::where(['transaction_reference_id' => $virtualcardWithdrawal->id, 'transaction_type_id' => Virtualcard_Withdrawal])->update(['status' => 'Success']);
                    event(new WithdrawalStatusUpdate($virtualcardWithdrawal));
                    return redirect()->back()->with('success', __('Virtualcard withdrawal approved successfully'));
                }

                if ($virtualcardWithdrawal->fund_approval_status == 'Pending') {

                    $userWallet = Wallet::where('user_id', $virtualcardWithdrawal->user_id)->first();
                    $userWallet->balance = $userWallet->balance + $virtualcardWithdrawal->requested_fund;
                    $userWallet->save();

                    # Withdrawal
                    $virtualcardWithdrawal->fund_approval_status = $request->fund_approval_status;
                    $virtualcardWithdrawal->fund_release_time = now();
                    $virtualcardWithdrawal->cancellation_reason = null;
                    $virtualcardWithdrawal->save();

                    # Transaction
                    Transaction::where(['transaction_reference_id' => $virtualcardWithdrawal->id, 'transaction_type_id' => Virtualcard_Withdrawal])->update(['status' => 'Success']);

                    event(new WithdrawalStatusUpdate($virtualcardWithdrawal));
                    return redirect()->back()->with('success', __('Virtualcard withdrawal approved successfully'));

                }

            }

            if ($request->fund_approval_status == 'Cancelled') {

                if ($virtualcardWithdrawal->fund_approval_status == 'Cancelled') {

                    $virtualcardWithdrawal->cancellation_reason = $request->cancellation_reason;
                    $virtualcardWithdrawal->save();
                    event(new WithdrawalStatusUpdate($virtualcardWithdrawal));
                    return redirect()->back()->with('success', __('Virtualcard withdrawal already cancelled.'));
                }

                if ($virtualcardWithdrawal->fund_approval_status == 'Pending') {

                    # Virtualcard
                    $virtualcard = Virtualcard::where('id', $virtualcardWithdrawal->virtualcard_id)->first();
                    $virtualcard->amount = $virtualcard->amount + ($virtualcardWithdrawal->requested_fund + $virtualcardWithdrawal->fixed_fees + $virtualcardWithdrawal->percentage_fees);
                    $virtualcard->save();

                    # Withdrawal
                    $virtualcardWithdrawal->fund_approval_status = $request->fund_approval_status;
                    $virtualcardWithdrawal->cancellation_reason = $request->cancellation_reason;
                    $virtualcardWithdrawal->save();

                    # Transaction
                    Transaction::where(['transaction_reference_id' => $virtualcardWithdrawal->id, 'transaction_type_id' => Virtualcard_Withdrawal])->update(['status' => 'Blocked', 'note' => $virtualcardWithdrawal->cancellation_reason]);
                    event(new WithdrawalStatusUpdate($virtualcardWithdrawal));
                    return redirect()->back()->with('success', __('Virtualcard withdrawal approved successfully'));
                }

            }

            if ($request->fund_approval_status == 'Pending') {


                if ($virtualcardWithdrawal->fund_approval_status == 'Cancelled') {

                    # Virtualcard
                    $virtualcard = Virtualcard::where('id', $virtualcardWithdrawal->virtualcard_id)->first();
                    $virtualcard->amount -= ($virtualcardWithdrawal->requested_fund + $virtualcardWithdrawal->fixed_fees + $virtualcardWithdrawal->percentage_fees);
                    $virtualcard->save();

                    # Withdrawal
                    $virtualcardWithdrawal->fund_approval_status = $request->fund_approval_status;
                    $virtualcardWithdrawal->fund_release_time = null;
                    $virtualcardWithdrawal->cancellation_reason = null;
                    $virtualcardWithdrawal->save();

                    # Transaction
                    Transaction::where(['transaction_reference_id' => $virtualcardWithdrawal->id, 'transaction_type_id' => Virtualcard_Withdrawal])->update(['status' => 'Pending']);
                    event(new WithdrawalStatusUpdate($virtualcardWithdrawal));
                    return redirect()->back()->with('success', __('The :x status has been successfully changed.', ['x' => __('withdrawal')]));
                }

                if ($virtualcardWithdrawal->fund_approval_status == 'Pending') {
                    (new Common)->one_time_message('success', __('Virtualcard withdrawal already pending.'));
                    return back();
                }

            }
        } catch (Exception $e) {
            (new Common)->one_time_message('error', $e->getMessage());
            return redirect()->route('admin.virtualcard_withdrawal.index');
        }

        return redirect()->route('admin.virtualcard_withdrawal.index')->with('success', __('Virtualcard withdrawal updated successfully'));
    }

    public function csv()
    {
        return Excel::download(new VirtualcardWithdrawalsExport(), 'virtualcard_withdrawals_' . time() . '.xlsx');
    }

    public function pdf()
    {
        $data['virtualcardWithdrawals'] = QueryBuilder::for(VirtualcardWithdrawal::with('virtualcard', 'user'))
                                    ->allowedFilters([
                                        AllowedFilter::exact('status', 'fund_approval_status'),
                                        AllowedFilter::exact('currency', 'virtualcard.currency_code'),
                                        AllowedFilter::exact('first_name', ),
                                        AllowedFilter::callback('from', function ($query, $value) {
                                            $query->where('created_at', '>=', Carbon::parse(setDateForDb($value))->startOfDay());
                                        }),
                                        AllowedFilter::callback('to', function ($query, $value) {
                                            $query->where('created_at', '<=', Carbon::parse(setDateForDb($value))->endOfDay());
                                        }),
                                        AllowedFilter::callback('user', function ($query, $value) {
                                            $query->whereHas('user', function ($query) use ($value) {
                                                $query->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'LIKE', "%$value%");
                                            });
                                        }),
                                    ])
                                    ->orderBy('id', 'desc')
                                    ->take(1100)
                                    ->get();

        $filter = request()->input('filter');

        $data['date_range'] = (isset($filter['from']) && isset($filter['to'])) ? $filter['from'] . ' To ' . $filter['to'] : 'N/A';

        generatePDF('virtualcard::admin.virtualcard_withdrawals.pdf', 'virtualcard_withdrawals_', $data);
    }
}
