<?php 

namespace Modules\Virtualcard\Responses;

use DateTime;
use InvalidArgumentException;

final class CardHolderResponse
{
    public function __construct(
        public ?string $apiHolderId,
        public string $city,
        public string $country,
        public string $address,
        public string $postalCode,
        public ?string $state,
        public string $status,
        public DateTime $createdAt,
        public ?string $apiResponse
    ) {}

    public static function fromArray(array $data): self
    {
        $requiredKeys = ['apiHolderId', 'city', 'country', 'address', 'state', 'postalCode', 'status', 'createdAt', 'apiResponse'];

        foreach ($requiredKeys as $key) {
            if (!array_key_exists($key, $data)) {
                throw new InvalidArgumentException(__("Missing required key: :x", ['x' => $key]));
            }
        }

        $requiredValues = ['city', 'country', 'address', 'postalCode', 'status', 'createdAt'];

        foreach ($requiredValues as $key) {
            if (!isset($data[$key])) {
                throw new InvalidArgumentException(__("Missing required value: :x", ['x' => $key]));
            }
        }

        return new static(
            $data['apiHolderId'],
            $data['city'],
            $data['country'],
            $data['address'],
            $data['postalCode'],
            $data['state'],
            $data['status'],
            is_numeric($data['createdAt'])
                ? (new DateTime())->setTimestamp((int) $data['createdAt'])
                : new DateTime($data['createdAt']),
            $data['apiResponse']
        );
    }      
}