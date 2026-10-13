<?php  

namespace Modules\Virtualcard\Http\Controllers;

use Stripe\Exception\SignatureVerificationException;
use Modules\Virtualcard\Facades\VirtualcardProviderManager;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Webhook;

class WebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $provider = ucfirst($request->route('provider'));
        $providerHandler = VirtualcardProviderManager::get($provider);
        $cardWebhookService = $providerHandler->getWebhookService();
        $cardSpendingResponse = $cardWebhookService->handleTransactionCreated($request);  
    }

    public function development(Request $request)
    {
        $provider = ucfirst($request->route('provider'));
        $providerHandler = VirtualcardProviderManager::get($provider);
        $cardWebhookService = $providerHandler->getWebhookService();
        $cardSpendingResponse = $cardWebhookService->handleTransactionCreated($request);   
    }
}