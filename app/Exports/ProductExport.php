<?php

namespace App\Exports;

use App\Models\Product;
use Caleb\Practice\QueryFilter;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductExport implements FromCollection, WithMapping, WithHeadings
{
    public function __construct(public QueryFilter $filter)
    {
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Product::filter($this->filter)->orderByDesc('id')->get();
    }

    public function headings(): array
    {
        return ['PID', '名称', '所属技术团队', '状态', '备注'];
    }

    public function map($row): array
    {
        /** @var Product $row */
        return [
            $row->id,
            $row->name,
            $row->tech_team,
            $row->status ? '启用' : '禁用',
            $row->remark,
        ];
    }
}
