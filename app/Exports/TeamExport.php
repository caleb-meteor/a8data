<?php

namespace App\Exports;

use App\Constants\DepartmentEnum;
use App\Models\Team;
use Caleb\Practice\QueryFilter;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TeamExport implements FromCollection, WithMapping, WithHeadings
{
    public function __construct(public QueryFilter $filter)
    {
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Team::filter($this->filter)->orderByDesc('id')->get();
    }

    public function headings(): array
    {
        return ['PID', '名称', '部门', '备注'];
    }

    public function map($row): array
    {
        /** @var Team $row */
        return [
            $row->id,
            $row->name,
            $row->department_id ? DepartmentEnum::from($row->department_id)->getName() : '',
            $row->remark,
        ];
    }
}
