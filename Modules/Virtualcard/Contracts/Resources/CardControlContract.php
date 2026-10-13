<?php

namespace Modules\Virtualcard\Contracts\Resources;

use Modules\Virtualcard\Responses\CardResponse;

interface CardControlContract
{
    /**
     * Activate/Inactivate/Freeze a virtual card.
     *
     * @param string $cardId The unique identifier of the card.
     * @return bool Returns true if activation is successful, false otherwise.
     */
    public function action(array $card, array $data): CardResponse;

    /**
     * Activate a virtual card.
     *
     * @param string $cardId The unique identifier of the card.
     * @return bool Returns true if activation is successful, false otherwise.
     */
    public function activateCard(string $cardId): bool;

    /**
     * Deactivate a virtual card.
     *
     * @param string $cardId The unique identifier of the card.
     * @return bool Returns true if deactivation is successful, false otherwise.
     */
    public function deactivateCard(string $cardId): bool;

    /**
     * Freeze a virtual card, preventing any transactions.
     *
     * @param string $cardId The unique identifier of the card.
     * @return bool Returns true if freezing is successful, false otherwise.
     */
    public function freezeCard(string $cardId): bool;

    /**
     * Unfreeze a virtual card, allowing transactions again.
     *
     * @param string $cardId The unique identifier of the card.
     * @return bool Returns true if unfreezing is successful, false otherwise.
     */
    public function unfreezeCard(string $cardId): bool;
}
