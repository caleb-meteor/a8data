<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ProductExport;
use App\Filters\ProductFilter;
use App\Http\Controllers\Controller;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ProductFilter $filter)
    {
        return $this->success(
            ProductService::instance()->getProductList($filter)
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:255|unique:products',
            'tech_team' => 'string',
            'remark'    => 'string|max:255',
            'status'    => 'required|boolean'
        ]);

        return $this->success(
            ProductService::instance()->createProduct($data)
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'name'    => ['required', 'string', 'max:255', Rule::unique('products')->ignore($id)],
            'tech_team' => 'string',
            'status'  => 'required|boolean',
            'remark'  => 'string|max:255',
        ]);
        ProductService::instance()->updateProduct($id, $data);
        return $this->success();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        ProductService::instance()->deleteProduct($id);
        return $this->success();
    }

    public function export(ProductFilter $filter)
    {
        return Excel::download(new ProductExport($filter), 'products_' . date('Y_m_d_H_i') . '.xlsx');
    }
}
