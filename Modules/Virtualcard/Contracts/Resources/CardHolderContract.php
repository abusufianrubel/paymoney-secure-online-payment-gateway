<?php

namespace Modules\Virtualcard\Contracts\Resources;

use Modules\Virtualcard\Responses\CardHolderResponse;

interface CardHolderContract
{
    /**
     * Create a new virtual cardholder.
     *
     * @param array $data The details for creating the cardholder.
     * @return \Modules\Virtualcard\Responses\CardHolderResponse The created cardholder details.
     */
    public function createCardHolder(array $data): CardHolderResponse;

    /**
     * Get details of a specific cardholder.
     *
     * @param string $cardHolderId The ID of the cardholder.
     * @return array The cardholder details.
     */
    public function getCardHolder(string $cardHolderId): array;

    /**
     * Update an existing cardholder.
     *
     * @param string $cardHolderId The ID of the cardholder.
     * @param array $data The data to update the cardholder.
     * @return array The updated cardholder details.
     */
    public function updateCardHolder(string $cardHolderId, array $data): array;

    /**
     * Delete a cardholder.
     *
     * @param string $cardHolderId The ID of the cardholder.
     * @return bool True if deletion is successful, otherwise false.
     */
    public function deleteCardHolder(string $cardHolderId): bool;

    /**
     * List all cardholders.
     *
     * @return array A list of cardholders.
     */
    public function listCardHolders(): array;
}
