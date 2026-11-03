<?php

namespace App\Services;

use App\Models\Discount;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class DiscountService
{

    public function getDiscounts(?array $filters): LengthAwarePaginator
    {
        $query = Discount::query();

        if (isset($filters['active'])) {
            $filters['active']
                ? $query->whereDate('expiry_date', '>=', today())
                : $query->whereDate('expiry_date', '<', today());
        }

        if (isset($filters['search'])) {
            $query->whereLike('discount_code', "%{$filters['search']}%");
        }

        return $query
            ->orderBy($filters['sort_by'] ?? 'percentage', $filters['sort_order'] ?? 'asc')
            ->orderBy('id')
            ->paginate($filters['per_page'] ?? 10)
            ->withQueryString();
    }

    public function addNewDiscount(array $data): Discount
    {
        return Discount::create($data);
    }

    public function findDiscountByCode(string $discountCode): ?Discount
    {
        return Discount::where('discount_code', $discountCode);
    }

    public function findDiscountById(int $id): ?Discount
    {
        return Discount::find($id);
    }

    public function updateDiscount(Discount $discount, ?array $data): ?Discount
    {
        $discount->update($data);
        return $discount;
    }

    public function deleteDiscount(Discount $discount)
    {
        return $discount->delete();
    }
}
