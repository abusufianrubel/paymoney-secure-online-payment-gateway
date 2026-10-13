<?php 

namespace Modules\Virtualcard\Manager;

use Exception;
use InvalidArgumentException;
use Modules\Virtualcard\Contracts\VirtualcardProviderInterface;

class ProviderManager
{
    protected array $providers = [];

    /**
     * Register a provider with a unique name.
     */
    public function register(string $name, string $providerClass): void
    {
        if (!class_exists($providerClass)) {
            throw new InvalidArgumentException(__('Provider class :x does not exist.', ['x' => $providerClass]));
        }

        $providerInstance = new $providerClass();

        if (! $providerInstance instanceof VirtualcardProviderInterface) {
            throw new Exception(__('Class :x must implement the \Modules\Virtualcard\Contracts\VirtualcardProviderInterface.', ['x' => $providerClass]));
   
        }   

        $this->providers[$name] = $providerInstance;
    }

    /**
     * Get a registered provider.
     */
    public function get(?string $name = null)
    {
        if ($name) {
            return $this->providers[$name] ?? null;
        }
        return $this->providers;
    }
}
