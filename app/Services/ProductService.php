<?php

namespace App\Services;

use App\Models\Product;
use App\Services\ProductService\ListFilter;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductService
{
    const DEFAULT_PER_PAGE = 40;
    const DEFAULT_PAGE = 1;

    public function getList(array $data): LengthAwarePaginator
    {
        $perPage = (int) ($data['per_page'] ?? null) ?: self::DEFAULT_PER_PAGE;
        $page = (int) ($data['page'] ?? null) ?: self::DEFAULT_PAGE;

        $query = Product::with(['propertyValues', 'propertyValues.property']);
        $query = (new ListFilter($query, $data))->apply();

        return $query->paginate($perPage, ['*'], 'page', $page);
    }
}
