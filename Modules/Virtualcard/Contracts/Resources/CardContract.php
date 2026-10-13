<?php

namespace Modules\Virtualcard\Contracts\Resources;

use Modules\Virtualcard\Responses\CardResponse;

interface CardContract
{
    /**
     * Create a new virtual card for a cardholder.
     *
     * @param array $cardholder The cardholder for creating the virtual card.
     * @param array $data The details for creating the virtual card.
     * @return \Modules\Virtualcard\Responses\CardResponse The created virtual card details.
     */
    public function createVirtualcard(array $cardholder, array $data): CardResponse;

    /**
     * Get details of a specific virtual card.
     *
     * @param string $cardId The ID of the virtual card.
     * @return array The virtual card details.
     */
    public function getVirtualcard(string $cardId): CardResponse;

    /**
     * Update an existing virtual card.
     *
     * @param array $card The virtual card to update.
     * @param array $data The data to update the virtual card.
     * @return \Modules\Virtualcard\Responses\CardResponse The updated virtual card details.
     */
    public function updateVirtualcard(array $card, array $data): CardResponse;

    /**
     * Delete a virtual card.
     *
     * @param string $cardId The ID of the virtual card.
     * @return bool True if deletion is successful, otherwise false.
     */
    public function deleteVirtualcard(string $cardId): bool;

    /**
     * List all virtual cards for a specific cardholder.
     *
     * @param string $cardholderId The ID of the cardholder.
     * @return array A list of virtual cards.
     */
    public function listVirtualcards(string $cardholderId): array;
}
