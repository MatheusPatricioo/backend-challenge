<?php

namespace Contatoseguro\TesteBackend\Dto;

readonly class ProductFilters
{
    public function __construct(
        public ?int    $active = null,
        public ?int    $categoryId = null,
        public ?string $sort = null,
    ) {
    }

    public static function fromQueryParams(array $params): self
    {
        return new self(
            active: isset($params['active']) ? (int) filter_var($params['active'], FILTER_VALIDATE_BOOLEAN) : null,
            categoryId: isset($params['category_id']) ? (int) $params['category_id'] : null,
            sort: $params['sort'] ?? null,
        );
    }
}