<?php

namespace Modules\Virtualcard\Contracts\Resources;


interface CardTransactionContract
{
    /**
     * Retrieve a specific transaction for a virtual card.
     *
     * @param string $transactionId The ID of the transaction.
     * @return array The transaction details.
     */
    public function getTransaction(string $transactionId): array;

    /**
     * List all transactions for a specific virtual card.
     *
     * @param string $cardId The ID of the virtual card.
     * @param array $filters Optional filters (e.g., date range, status).
     * @return array A list of transactions for the virtual card.
     */
    public function listTransactions(string $cardId, array $filters = []): array;

    /**
     * Refund a specific transaction.
     *
     * @param string $transactionId The ID of the transaction to refund.
     * @param int|null $amount The amount to refund in the smallest currency unit (e.g., cents). Defaults to full refund.
     * @return array The details of the refunded transaction.
     */
    public function refundTransaction(string $transactionId, ?int $amount = null): array;

    /**
     * Retrieve the balance of a virtual card.
     *
     * @param string $cardId The ID of the virtual card.
     * @return array The current balance of the virtual card.
     */
    public function getCardBalance(string $cardId): array;

    /**
     * Fund a virtual card.
     *
     * @param string $cardId The ID of the virtual card.
     * @param int $amount The amount to fund in the smallest currency unit (e.g., cents).
     * @param string $currency The currency in which the card is funded.
     * @return array The updated card details or funding transaction.
     */
    public function fundCard(string $cardId, int $amount, string $currency): array;

    /**
     * Retrieve the list of all transactions across virtual cards.
     *
     * @param array $filters Optional filters (e.g., cardholder ID, date range, or status).
     * @return array A list of all transactions across cards.
     */
    public function listAllTransactions(array $filters = []): array;

    /**
     * Reverse a pending transaction on a virtual card.
     *
     * @param string $transactionId The ID of the pending transaction.
     * @return array The updated transaction details after reversal.
     */
    public function reversePendingTransaction(string $transactionId): array;
}
