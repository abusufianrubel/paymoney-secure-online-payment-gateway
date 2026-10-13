<?php 

namespace Modules\Virtualcard\Responses;

use DateTime;
use InvalidArgumentException;
use Modules\Virtualcard\Enums\CardBrands;

final class CardResponse
{
    public function __construct(
        public CardBrands $cardBrand,
        public string $cardNumber,
        public ?string $cardCvc,
        public string $currencyCode,
        public int $expiryMonth,
        public int $expiryYear,
        public string $status,
        public DateTime $createdAt,
        public ?string $apiCardId,
        public ?string $apiCardResponse
    ) {}

    public static function fromArray(array $data): self
    {
        $requiredKeys = ['cardBrand', 'cardNumber', 'cardCvc', 'currencyCode', 'expiryMonth', 'expiryYear', 'status', 'createdAt', 'apiCardId', 'apiCardResponse'];

        foreach ($requiredKeys as $key) {
            if (!array_key_exists($key, $data)) {
                throw new InvalidArgumentException(__("Missing required key: :x", ['x' => $key]));
            }
        }

        $requiredValues = ['cardBrand', 'cardNumber', 'currencyCode', 'expiryMonth', 'expiryYear', 'status', 'createdAt'];

        foreach ($requiredValues as $key) {
            if (!isset($data[$key])) {
                throw new InvalidArgumentException(__("Missing required value: :x", ['x' => $key]));
            }
        }

        return new static(
            $data['cardBrand'],
            $data['cardNumber'],
            $data['cardCvc'],
            $data['currencyCode'],
            $data['expiryMonth'],
            $data['expiryYear'],
            $data['status'],
            is_numeric($data['createdAt'])
                ? (new DateTime())->setTimestamp((int) $data['createdAt'])
                : new DateTime($data['createdAt']),
            $data['apiCardId'],
            $data['apiCardResponse'],
        );
    }      
}