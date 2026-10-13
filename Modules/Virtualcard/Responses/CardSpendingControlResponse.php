<?php

namespace Modules\Virtualcard\Responses;

use InvalidArgumentException;

final class CardSpendingControlResponse
{
    public function __construct(
        public array $limits // Holds an array of ['interval' => string, 'amount' => decimal] pairs
    ) {}

    // Factory method to create an instance from an array of limits
    public static function fromArray(array $spendingLimits): self
    {
        // Ensure that each element of the array contains 'interval' and 'amount'
        foreach ($spendingLimits as $limit) {
            if (!isset($limit['interval'], $limit['amount'])) {
                throw new InvalidArgumentException(__("Each spending limit must include 'interval' and 'amount'."));
            }

            if (!is_string($limit['interval']) || !is_numeric($limit['amount'])) {
                throw new InvalidArgumentException(__("Invalid type for 'interval' or 'amount'."));
            }
        }

        // Map each spending limit to a formatted array of strict types
        $limits = array_map(function ($limit) {
            return [
                'interval' => (string) $limit['interval'],
                'amount' => $limit['amount'],
            ];
        }, $spendingLimits);

        return new self($limits);
    }
}

