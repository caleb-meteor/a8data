<?php

namespace App\Services;

use App\Models\Product;
use Caleb\Practice\QueryFilter;
use Caleb\Practice\Service;

class ProductService extends Service
{
    public function getProductList(QueryFilter $filter)
    {
        return Product::filter($filter)
            ->with('creator', 'team')
            ->orderByDesc('id')->paginate();
    }

    public function createProduct(array $data)
    {
        return Product::query()->create($data);
    }

    /**
     * @param int|Product $product
     * @return Product
     * @author Caleb 2025/5/8
     */
    public function getProduct(int|Product $product)
    {
        return $product instanceof Product ? $product : Product::query()->find($product);
    }

    public function updateProduct(int $product, array $data)
    {
        $product = $this->getProduct($product);
        return $product->update($data);
    }

    public function deleteProduct(int $product)
    {
        $product = $this->getProduct($product);
        return $product->delete();
    }

    public function getProductByNames(array $names)
    {
        return Product::query()->whereIn('name', $names)->get();
    }
}
