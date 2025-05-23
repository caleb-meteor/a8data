<?php

namespace App\Services;

use App\Constants\DepartmentEnum;
use App\Imports\FinanceImport;
use App\Models\Agent;
use App\Models\Finance;
use App\Models\Product;
use Carbon\Carbon;
use Caleb\Practice\Exceptions\PracticeAppException;
use Caleb\Practice\QueryFilter;
use Caleb\Practice\Service;
use Illuminate\Support\Benchmark;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class FinanceService extends Service
{
    public function getFinanceList(QueryFilter $filter)
    {
        return Finance::filter($filter)
            ->with('creator', 'team', 'product', 'agent')
            ->orderByDesc('id')->paginate();
    }

    public function createFinance(array $data)
    {
        return Finance::query()->create($data);
    }

    /**
     * @param int|Finance $finance
     * @return Finance
     * @author Caleb 2025/5/8
     */
    public function getFinance(int|Finance $finance)
    {
        return $finance instanceof Finance ? $finance : Finance::query()->find($finance);
    }

    public function updateFinance(int $finance, array $data)
    {
        $finance = $this->getFinance($finance);
        if (!$finance) {
            $this->throwAppException('记录不存在');
        }
        return $finance->update($data);
    }

    public function deleteFinance(int $finance)
    {
        $finance = $this->getFinance($finance);
        if (!$finance) {
            return true;
        }
        return $finance->delete();
    }

    /**
     * @param $file
     * @return int
     * @throws PracticeAppException
     * @author Caleb 2025/5/10
     */
    public function import($file)
    {
        ini_set('memory_limit', '1024M');      // 设置最大内存，例如 1024M、-1（无限制）
        ini_set('max_execution_time', 300);   // 设置最大执行时间（秒），这里是 5 分钟

        // 代理 产品  部门
        $data = Excel::toArray(new FinanceImport(), $file)[0] ?? [];
        $data = array_slice($data, 1);

        $agents = $products = $departments = [];
        $rows   = [];
        foreach ($data as $row) {
            $row                   = array_slice($row, 1);
            $row[1]                = is_numeric($row[1]) ? Date::excelToDateTimeObject($row[1])->format('Y-m-d') : $row[1];
            $row                   = array_map(fn($item) => trim($item), $row);
            $agents[$row[11]]      = 1;
            $departments[$row[14]] = 1;
            $products[$row[17]]    = 1;
            $rows[]                = $row;
        }
        if (count($rows) > 50000) {
            throw new PracticeAppException('数据量过大，请分批导入');
        }

        // $importer = new ImportService(maxRows: 20000, parseRow: function ($row) use (&$agents, &$products, &$departments) {
        //     $row                   = array_slice($row, 1);
        //     $row                   = array_map(fn($item) => trim($item), $row);
        //     $agents[$row[11]]      = 1;
        //     $departments[$row[14]] = 1;
        //     $products[$row[17]]    = 1;
        //     return $row;
        // });
        // $rows     = $importer->getRows($filePath);

        $agents      = array_filter(array_keys($agents));
        $products    = array_filter(array_keys($products));
        $departments = array_keys($departments);


        // 格式化入库数据
        list($agents, $products) = $this->formatData($agents, $products, $departments);
        try {
            return $this->importDataToDB($rows, $agents, $products);
        } catch (\Exception $e) {
            if ($e instanceof PracticeAppException) {
                throw $e;
            } else {
                report($e);
                $this->throwAppException('请检查csv文件是否为标准csv,保证数据格式正确,确定字母大小写,数字单元格不要出现字符串！');
            }
        }
    }

    /**
     * @param $rows
     * @param $agents
     * @param $products
     * @return int
     * @author Caleb 2025/5/11
     */
    public function importDataToDB($rows, $agents, $products)
    {

        $dateTime = now()->format('Y-m-d H:i:s');
        $pdo      = DB::connection()->getPdo();
        if ($pdo->getAttribute(\PDO::ATTR_DRIVER_NAME) == 'pgsql') {
            $data   = [];
            $fields = [
                'month', 'date',
                'counterparty_fee', 'media_fee', 'transaction_fee', 'service_fee',
                'usd_loss_percent', 'usd', 'ustd', 'commission',
                'purpose', 'agent_id', 'description', 'account', 'department_id', 'handler', 'remark',
                'product_id', 'balance', 'creator_id', 'created_at', 'updated_at',
            ];
            foreach ($rows as $insertRow) {
                if ($insertRow[1]) $data[] = $this->formatRowsForPgsql($insertRow, $agents, $products, $dateTime);
            }
            // try {
            //     foreach (array_chunk($data, 20) as $datum) {
            //         $datum && $pdo->pgsqlCopyFromArray((new Finance())->getTable(), $datum, separator: ',', fields: implode(',', $fields));
            //     }
            // } catch (\Exception $e) {
            //     dd($datum, $e->getMessage());
            // }

            $data && $pdo->pgsqlCopyFromArray((new Finance())->getTable(), $data, separator: ',', fields: implode(',', $fields));
            return count($data);
        } else {
            $total = 0;
            DB::beginTransaction();
            foreach (array_chunk($rows, 350) as $insertRows) {
                $data = [];
                foreach ($insertRows as $insertRow) {
                    if ($insertRow[0]) $data[] = $this->formatRows($insertRow, $agents, $products, $dateTime);
                    if (count($data) > 1) {
                        break;
                    }
                }
                $data && Finance::query()->insert($data);

                $total += count($data);
            }
            DB::commit();
            return $total;
        }
    }

    /**
     * 统计财务数据的总和与平均值
     *
     * @param QueryFilter $filter
     * @return array 包含总和与平均值的数组
     */
    public function statistic(QueryFilter $filter)
    {
        $date = $filter->getFilters()['date'];

        $sum = Finance::filter($filter)->selectRaw('sum(counterparty_fee) as counterparty_fee, sum(media_fee) as media_fee, sum(transaction_fee) as transaction_fee,sum(service_fee) as service_fee,sum(usd) as usd,sum(ustd) as ustd,sum(usd_loss_percent) as usd_loss_percent')->first();

        $diffDay = Carbon::create($date[0])->diffInDays($date[1]) + 1;


        $sumData = $sum ? $sum->toArray() : [];

        // 确保所有字段值不为 null，如果为 null 则设置为 0
        foreach ($sumData as $field => $value) {
            $sumData[$field] = $value ?: 0;
        }

        $result = [
            'sum'     => $sumData,
            'average' => []
        ];

        // 计算平均值
        foreach ($sumData as $field => $value) {
            $averageValue              = $diffDay > 0 ? $value / $diffDay : 0;
            $result['average'][$field] = round($averageValue, 6, PHP_ROUND_HALF_UP);
        }

        return $result;
    }

    public function formatRowsForPgsql($row, $agents, $products, $dateTime)
    {
        return implode(',', $this->formatRows($row, $agents, $products, $dateTime));
    }

    public function formatRows($row, $agents, $products, $dateTime)
    {
        // 长度不足则补齐
        $row = array_pad($row, 19, '');
        return [
            'month'            => str_replace('月', '', $row[0]) ?: Carbon::parse($row[1])->month,
            'date'             => $row[1],
            'counterparty_fee' => parse_number($row[2] ?: 0),
            'media_fee'        => parse_number($row[3] ?: 0),
            'transaction_fee'  => parse_number($row[4] ?: 0),
            'service_fee'      => parse_number($row[5] ?: 0),
            'usd_loss_percent' => parse_number($row[6] ?: 0),
            'usd'              => parse_number($row[7] ?: 0),
            'ustd'             => parse_number($row[8] ?: 0),
            'commission'       => parse_number($row[9] ?: 0),
            'purpose'          => $row[10],
            'agent_id'         => $row[11] ? $agents[$row[11]] : 0,
            'description'      => $row[12],
            'account'          => $row[13],
            'department_id'    => DepartmentEnum::fromName($row[14]) ?: 0,
            'handler'          => $row[15],
            'remark'           => $row[16],
            'product_id'       => $row[17] ? $products[$row[17]] : 0,
            'balance'          => parse_number($row[18] ?: 0),
            'creator_id'       => auth()?->user()?->id ?? 0,
            'created_at'       => $dateTime,
            'updated_at'       => $dateTime,
        ];
    }

    /**
     * @param array $agents
     * @param array $products
     * @param array $departments
     * @return array|void
     * @throws PracticeAppException
     * @author Caleb 2025/5/11
     */
    public function formatData(array $agents, array $products, array $departments)
    {
        $errors = [];
        try {
            $this->checkDepartment($departments);
        } catch (PracticeAppException $e) {
            $errors = array_merge($errors, $e->getData());
        }

        try {
            return $this->formatDBData($agents, $products);
        } catch (PracticeAppException $e) {
            $errors = array_merge($errors, $e->getData());
        }

        $errors && $this->throwAppException('数据错误', 4735, $errors);

    }

    /**
     * @param $departments
     * @return void
     * @throws \Caleb\Practice\Exceptions\PracticeAppException
     * @author Caleb 2025/5/11
     */
    public function checkDepartment($departments)
    {
        $errors = [];

        foreach ($departments as $department) {
            if (!$department) {
                continue;
            }

            if (!DepartmentEnum::fromName($department)) {
                $errors[] = $department;
            }
        }
        $errors && $this->throwAppException('数据错误', 4735, ['departments' => $errors]);
    }

    /**
     * @param array $agents
     * @param array $products
     * @return array
     * @throws PracticeAppException
     * @author Caleb 2025/5/11
     */
    public function formatDBData(array $agents, array $products)
    {
        $agents     = array_filter($agents);
        $products   = array_filter($products);
        $dbAgents   = $agents ? AgentService::instance()->getAgentByNames($agents) : collect();
        $dbProducts = $products ? ProductService::instance()->getProductByNames($products) : collect();
        $errors     = [];
        // 代理
        $diffAgents = array_diff($agents, $dbAgents->pluck('name')->toArray());
        if ($diffAgents) {
            $errors['agents'] = array_values($diffAgents);
            // $this->throwAppException('代理不存在:' . implode(',', $diffAgents));
        }

        // 产品
        $diffProducts = array_diff($products, $dbProducts->pluck('name')->toArray());
        if ($diffProducts) {
            $errors['products'] = array_values($diffProducts);
            // $this->throwAppException('产品不存在:' . implode(',', $diffProducts));
        }

        $errors && $this->throwAppException('数据错误', 4735, $errors);
        return [
            $dbAgents->pluck('id', 'name')->toArray(),
            $dbProducts->pluck('id', 'name')->toArray(),
        ];
    }
}
