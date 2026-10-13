<?php 

namespace Modules\Virtualcard\Contracts;

use Modules\Virtualcard\Contracts\Resources\{
    CardContract,
    WebhookContract,
    CardHolderContract,
    CardControlContract,
    SpendingControlContract
};

interface VirtualcardProviderInterface
{    
    /**
     * Method getCardHolderService
     *
     * @return \Modules\Virtualcard\Contracts\Resources\CardHolderContract
     */
    public function getCardHolderService(): CardHolderContract;

    /**
     * Method getCardService
     *
     * @return \Modules\Virtualcard\Contracts\Resources\CardContract
     */
    public function getCardService(): CardContract;

    /**
     * Method getCardControlService
     *
     * @return \Modules\Virtualcard\Contracts\Resources\CardContract
     */
    public function getCardControlService(): CardControlContract;

    /**
     * Method getSpendingControlService
     *
     * @return \Modules\Virtualcard\Contracts\Resources\SpendingControlContract
     */
    public function getSpendingControlService(): SpendingControlContract;

    /**
     * Method getWebhookService
     *
     * @return \Modules\Virtualcard\Contracts\Resources\CardContract
     */
    public function getWebhookService(): WebhookContract;
}