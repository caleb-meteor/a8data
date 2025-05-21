<?php

namespace App\Exports;

use App\Constants\DepartmentEnum;
use App\Models\Finance;
use Caleb\Practice\QueryFilter;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class FinanceExport implements FromCollection, WithMapping, WithHeadings
{
    public function __construct(public QueryFilter $filter)
    {
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Finance::filter($this->filter)->with('agent', 'product')->get();
    }

    public function headings(): array
    {
        return [
            'PID', '月/month', '日期/date', '欠对方/qianduifang',
            '欠媒体/qianmeiti', '手续费/shouxufei', '服务费/servicefei',
            'u损/ushun', '美金/meijin', 'USDT/usdt', '返货/fanhuo',
            '用途/yongtu', '代理/agency', '费用明细/money_desc',
            '账号信息/accdesc', '部门/bumen', '经手人/jingshou',
            '备注/remark', '产品/product', '钱包余额/banlance'
        ];
    }

    public function map($row): array
    {
        /** @var Finance $row */
        return [
            $row->id,
            $row->month . '月',
            $row->date,
            $row->counterparty_fee,
            $row->media_fee,
            $row->transaction_fee,
            $row->service_fee,
            $row->usd_loss_percent,
            $row->usd,
            $row->ustd,
            $row->commission,
            $row->purpose,
            $row->agent?->name,
            $row->description,
            $row->account,
            $row->department_id ? DepartmentEnum::from($row->department_id)->getName() : '',
            $row->handler,
            $row->remark,
            $row->product?->name,
            $row->balance,
        ];
    }
}
