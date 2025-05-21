<?php

namespace App\Exports;

use App\Constants\DepartmentEnum;
use App\Constants\MediaEnum;
use App\Models\Usage;
use Caleb\Practice\QueryFilter;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UsageExport implements FromCollection, WithMapping, WithHeadings
{
    public function __construct(public QueryFilter $filter)
    {
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Usage::filter($this->filter)->with('team', 'agent', 'creator', 'product')->get();
    }

    public function headings(): array
    {
        return [
            'PID', '月/month', '日期/date',
            '部门/bumen', '团队/team', '产品/product',
            '总代/zhongdai', '渠道号/qudaohao',
            '媒体/meiti', '投放方式/method', '代理/agency',
            '实际消耗/spend', '展示/view', '点击/click', '安装量/install',
            'remark/tiaoshuxiaohao', 'remark/danjiafuwufeidianshu',
            'remark/ID', '填报人/reporter'
        ];
    }

    public function map($row): array
    {
        /** @var Usage $row */
        return [
            $row->id,
            $row->month.'月',
            $row->date,
            $row->department_id ? DepartmentEnum::from($row->department_id)->getName() : '',
            $row->team?->name,
            $row->product?->name,
            $row->exclusive_agent,
            $row->channel,
            $row->media ? MediaEnum::from($row->media)->getName() : '',
            $row->placement_method,
            $row->agent?->name,
            $row->actual_usage,
            $row->view,
            $row->click,
            $row->install,
            $row->send_num,
            $row->price,
            $row->unique_id,
            $row->creator?->username ?? $row->creator?->name,
        ];
    }
}
