<?php

namespace Modules\Virtualcard\Contracts\Resources;

use Modules\Virtualcard\Responses\CardSpendingControlResponse;

interface SpendingControlContract
{
    /**
     * Get the list of spending controls for a virtual card.
     *
     * @param string $cardId The ID of the virtual card.
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSpendingControls(string $cardId);

    /**
     * Create a new spending control for a virtual card.
     *
     * @param string $cardId The ID of the virtual card.
     * @param array $data The data for the spending control (e.g., spending limits, categories).
     * @return \Illuminate\Http\JsonResponse
     */
    public function createSpendingControl(string $cardId, array $data);

    /**
     * Update an existing spending control for a virtual card.
     *
     * @param array $virtualcard The virtual card details.
     * @param array $spendingLimits The data to update the spending control.
     * @return \Modules\Virtualcard\Responses\CardSpendingControlResponse
     */
    public function updateSpendingControl(array $virtualcard, ?array $spendingLimits): CardSpendingControlResponse;

    /**
     * Delete a spending control for a virtual card.
     *
     * @param string $controlId The ID of the spending control.
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteSpendingControl(string $controlId);
}
