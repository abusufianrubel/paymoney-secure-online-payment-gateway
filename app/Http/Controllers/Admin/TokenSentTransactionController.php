<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\TokenSentTransactionsDataTable;
use App\Exports\TokenSendsExport;
use App\Models\Currency;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

class TokenSentTransactionController extends Controller
{
    protected $transaction;

    public function __construct()
    {
        $this->transaction = new \App\Models\Transaction();
    }

    public function index(TokenSentTransactionsDataTable $dataTable)
    {
        $data['menu'] = 'transaction';
        $data['sub_menu'] = 'token-sent-transactions';

        $cryptoSentTransactions = $this->transaction->where('transaction_type_id', Token_Sent);
        $data['cryptoSentTransactionsCurrencies'] = $cryptoSentTransactions->with('currency:id,code')->groupBy('currency_id')->get(['currency_id']);
        $data['cryptoSentTransactionsStatus'] = $cryptoSentTransactions->groupBy('status')->get(['status']);

        $data['from']     = isset(request()->from) ? setDateForDb(request()->from) : null;
        $data['to']       = isset(request()->to ) ? setDateForDb(request()->to) : null;
        $data['status']   = isset(request()->status) ? request()->status : 'all';
        $data['currency'] = isset(request()->currency) ? request()->currency : 'all';
        $data['user']     = $user = isset(request()->user_id) ? request()->user_id : null;
        $data['getName']  = $this->transaction->getTransactionsUsersEndUsersName($user, Token_Sent);
        return $dataTable->render('admin.token_transactions.sent.index', $data);
    }

    public function tokenSentTransactionsSearchUser(Request $request)
    {
        $search = $request->search;
        $user   = $this->transaction->getTransactionsUsersResponse($search, Token_Sent);
        $res    = [
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

    public function view($id)
    {
        $data['menu']     = 'transaction';
        $data['sub_menu'] = 'token-sent-transactions';

        $data['transaction'] = $transaction = $this->transaction->with([
            'user:id,first_name,last_name',
            'end_user:id,first_name,last_name',
            'currency:id,code,symbol',
            'payment_method:id,name',
            'cryptoAssetApiLog:id,object_id,payload,confirmations',
        ])
        ->where('transaction_type_id', Token_Sent)
        ->exclude(['merchant_id', 'bank_id', 'file_id', 'refund_reference', 'transaction_reference_id', 'email', 'phone', 'note'])
        ->find($id);

        // Get crypto api log details for Crypto_Sent
        if (!empty($transaction->cryptoAssetApiLog)) {
            $getCryptoDetails = getCryptoPayloadConfirmationsDetails($transaction->transaction_type_id, $transaction->cryptoAssetApiLog->payload, $transaction->cryptoAssetApiLog->confirmations);
            if (count($getCryptoDetails) > 0) {
                if (isset($getCryptoDetails['senderAddress'])) {
                    $data['senderAddress'] = $getCryptoDetails['senderAddress'];
                }
                if (isset($getCryptoDetails['receiverAddress'])) {
                    $data['receiverAddress'] = $getCryptoDetails['receiverAddress'];
                }
                if (isset($getCryptoDetails['network_fee'])) {
                    $data['network_fee'] = $getCryptoDetails['network_fee'];
                }

                if (isset($getCryptoDetails['network'])) {
                    $data['network'] = Currency::where('code', $getCryptoDetails['network'])->first();
                }

                $data['txId'] = $getCryptoDetails['txId'];
                $data['confirmations'] = $getCryptoDetails['confirmations'];
            }
        }
        return view('admin.token_transactions.sent.view', $data);
    }

    public function tokenSentTransactionsCsv()
    {
        return Excel::download(new TokenSendsExport(), 'token_sent_transactions_list_' . time() . '.csv');
    }

    public function tokenSentTransactionsPdf()
    {
        $from = !empty(request()->startfrom) ? setDateForDb(request()->startfrom) : null;
        $to = !empty(request()->endto) ? setDateForDb(request()->endto) : null;
        $status = isset(request()->status) ? request()->status : null;
        $currency = isset(request()->currency) ? request()->currency : null;
        $user = isset(request()->user_id) ? request()->user_id : null;

        $data['getCryptoSentTransactions'] = $this->transaction->getCryptoSentTransactions($from, $to, $status, $currency, $user, Token_Sent)->orderBy('transactions.id', 'desc')->get();

        if (isset($from) && isset($to)) {
            $data['date_range'] = $from . ' To ' . $to;
        } else {
            $data['date_range'] = 'N/A';
        }

        // Input parameters (view, filename, printdata)
        generatePDF('admin.token_transactions.sent.token_sent_transactions_report_pdf', 'token_sent_transactions_report_', $data);
    }
}