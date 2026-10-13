<?php

namespace App\libraries;

use Illuminate\Support\Collection;
use Modules\KycVerification\Contract\VerificationContract;

class VerificationProviderManager
{
    private $providers = [];

    /**
     * Add a provider to the collection.
     *
     * @param string $provider The fully qualified class name of the provider.
     * @param string|null $alias The alias for the provider.
     * @throws \Exception If the class does not exist.
     * @return void
     */
    public function add(string $provider, ?string $alias = null): void
    {
        if (! class_exists($provider)) {
            throw new \Exception("Class '$provider' does not exist.");
        }

        // Determine the alias for the provider
        $alias = $alias ? strtolower($alias) : strtolower(class_basename($provider));

        $providerInstance = new $provider($alias);

        if (! $providerInstance instanceof VerificationContract) {
            throw new \Exception("Class $provider must need to extends the \Modules\KycVerification\Contract\VerificationContract class.");
        }

        $this->providers[$alias] = $provider;
    }

    /**
     * Get all providers.
     *
     * @return array The array containing all providers.
     */
    public function get(): array
    {
        return $this->providers;
    }

    /**
     * Get all providers.
     *
     * Returns an array containing all the providers.
     *
     * @return array The array containing all providers.
     */
    public function all(): Collection
    {
        return collect($this->providers);
    }

    /**
     * Find a provider by its alias.
     *
     * @param string $alias The alias of the provider to find.
     * @return string|null The provider path if found, or null if not found.
     */
    public function find(string $alias): ?string
    {
        return $this->providers[strtolower($alias)] ?? null;
    }

    /**
     * Returns an array of class names with space-separated words.
     *
     * @return array The array of class names.
     */
    public function names(): array
    {
        return array_map(function ($aiProvider) {
            return \Str::replaceMatches('/(?<!\s)([A-Z])/', ' $1', class_basename($aiProvider));
        }, $this->providers);
    }
}
