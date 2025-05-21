<?php

namespace App\Exports;

use App\Constants\MediaEnum;
use App\Models\Agent;
use Caleb\Practice\QueryFilter;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AgentExport implements FromCollection, WithMapping, WithHeadings
{
    public function __construct(public QueryFilter $filter)
    {
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Agent::filter($this->filter)->orderByDesc('id')->get();
    }

    public function headings(): array
    {
        return ['PID', '名称', '返点', '备注', '媒体'];
    }

    public function map($row): array
    {
        /** @var Agent $row */
        return [
            $row->id,
            $row->name,
            $row->commission,
            $row->remark,
            $row->media ? MediaEnum::from($row->media)->getName() : '',
        ];
    }
}
