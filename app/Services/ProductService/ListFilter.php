<?php

namespace App\Services\ProductService;

use App\Models\ProductProperty;
use App\Models\ProductPropertyValue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;

class ListFilter
{
    private Builder $query;
    private array $data;
    private string $table;
    private string $joinTable;

    public function __construct(Builder $query, array $data)
    {
        $this->query = $query;
        $this->data = $data;
        $this->table = $query->getModel()->getTable();
        $this->joinTable = (new ProductPropertyValue())->getTable();
    }

    public function apply(): Builder
    {
        $this->search("$this->table.name", $this->data['name'] ?? '');
        $this->range( "$this->table.quantity", [
            $this->data['quantity_from'] ?? null,
            $this->data['quantity_to'] ?? null,
        ]);
        $this->range("$this->table.price", [
            $this->data['price_from'] ?? null,
            $this->data['price_to'] ?? null,
        ]);
        $this->filterProperties($this->data['properties'] ?? []);

        return $this->query;
    }

    private function range(string $field, array $range): void
    {
        $from = $range[0] ?? null;
        $to = $range[1] ?? null;

        if ($from && $to) {
            $this->query->whereBetween($field, $range);
        }
        elseif ($from) {
            $this->query->where($field, '>=', $from);
        }
        elseif ($to) {
            $this->query->where($field, '<=', $to);
        }
    }

    private function search(string $field, string $value): void
    {
        if (! $value) {
            return;
        }

        $this->query->where($field, 'like', "%$value%");
    }

    private function filterProperties(array $properties): void
    {
        if (! $properties) {
            return;
        }

        $map = ProductProperty::query()
            ->whereIn('slug', array_keys($properties))
            ->get()
            ->pluck('id', 'slug')
            ->filter(fn(string $slug) => !empty($properties[$slug] ?? null))
            ->toArray();

        foreach ($map as $id => $slug) {
            $this->filterProperty($slug, $id, $properties[$slug]);
        }
    }

    private function filterProperty(string $slug, int $id, array $values): void
    {
        $alias = "property_value_$slug";
        $this->query->join(
            "$this->joinTable as $alias",
            fn(JoinClause $join) => $join
                ->on("$this->table.id", "=", "$alias.product_id")
                ->where("$alias.property_id", '=', $id)
        );
        $this->query->whereIn("$alias.value", $values);
    }
}
