<?php

namespace Contatoseguro\TesteBackend\Dto;

use Contatoseguro\TesteBackend\Exception\ValidationException;

readonly class ProductFilters
{
    public function __construct(
        public ?int    $active = null,
        public ?int    $categoryId = null,
        public ?string $sort = null,
        public ?int    $minStock = null,
    )
    {
    }

    public static function fromQueryParams(array $params): self
    {
        return new self(
            active: self::parse($params, 'active', FILTER_VALIDATE_BOOLEAN),
            categoryId: self::parse($params, 'category_id', FILTER_VALIDATE_INT),
            sort: $params['sort'] ?? null,
            minStock: self::parse($params, 'min_stock', FILTER_VALIDATE_INT),
        );
    }

    private static function parse(array $params, string $key, int $filter): ?int
    {
        if (!isset($params[$key])) {
            return null;
        }

        $value = filter_var($params[$key], $filter, FILTER_NULL_ON_FAILURE);

        if ($value === null) {
            throw new ValidationException("Invalid value for {$key}.");
        }

        return (int)$value;
    }
}