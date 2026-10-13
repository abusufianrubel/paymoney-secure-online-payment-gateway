<?php

namespace Modules\Virtualcard\Contracts\Resources;

use Illuminate\Http\Request;

interface WebhookContract
{
    /**
     * Create webhook for the virtual card provider.
     *
     * @param array $data The webhook information to create
     * @return void
     */
    public function webhookSetup(array $data): void;

    /**
     * Handle the event when a virtual card is created.
     *
     * @param array $payload The webhook payload containing card details.
     * @return void
     */
    public function handleCardCreated(array $payload): void;

    /**
     * Handle the event when a virtual card is updated.
     *
     * @param array $payload The webhook payload containing updated card details.
     * @return void
     */
    public function handleCardUpdated(array $payload): void;

    /**
     * Handle the event when a virtual card is deleted or closed.
     *
     * @param array $payload The webhook payload containing card details.
     * @return void
     */
    public function handleCardDeleted(array $payload): void;

    /**
     * Handle the event when a transaction is created for a virtual card.
     *
     * @param array $payload The webhook payload containing transaction details.
     * @return void
     */
    public function handleTransactionCreated(Request $request): void;

    /**
     * Handle the event when a transaction status is updated (e.g., pending, completed, refunded).
     *
     * @param array $payload The webhook payload containing transaction status updates.
     * @return void
     */
    public function handleTransactionUpdated(array $payload): void;

    /**
     * Handle the event when spending limits or controls are breached.
     *
     * @param array $payload The webhook payload containing the violation details.
     * @return void
     */
    public function handleSpendingControlViolation(array $payload): void;

    /**
     * Handle generic or unrecognized webhook events for logging or debugging.
     *
     * @param string $eventType The type of the webhook event.
     * @param array $payload The full webhook payload.
     * @return void
     */
    public function handleUnknownEvent(string $eventType, array $payload): void;
}
