<?php

namespace destvil\cbr\ObjectValues;

class CurrencyNavigationFilter
{
    private array $order = [];
    private int $limit = 0;
    private int $offset = 0;

    public function order(string $field, string $order = 'ASC'): CurrencyNavigationFilter
    {
        $this->order[$field] = $order;

        return $this;
    }

    public function getOrder(): array
    {
        return $this->order;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function setLimit(int $limit): CurrencyNavigationFilter
    {
        $this->limit = $limit;

        return $this;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function setOffset(int $offset): CurrencyNavigationFilter
    {
        $this->offset = $offset;

        return $this;
    }
}