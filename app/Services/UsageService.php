<?php

namespace App\Services;

use App\Constants\DepartmentEnum;
use App\Constants\MediaEnum;
use App\Models\Agent;
use App\Models\Product;
use App\Models\Team;
use App\Models\Usage;
use App\Models\User;
use Caleb\Practice\Exceptions\PracticeAppException;
use Caleb\Practice\QueryFilter;
use Caleb\Practice\Service;
use Illuminate\Support\Benchmark;
use Illuminate\Support\Facades\DB;

class UsageService extends Service
{
    public function getUsageList(QueryFilter $filter)
    {
        return Usage::filter($filter)
            ->with('creator', 'team', 'product', 'agent')
            ->orderByDesc('id')->paginate();
    }

    public function createUsage(array $data)
    {
        return Usage::query()->create($data);
    }

    /**
     * @param int|Usage $usage
     * @return Usage
     * @author Caleb 2025/5/8
     */
    public function getUsage(int|Usage $usage)
    {
        return $usage instanceof Usage ? $usage : Usage::query()->find($usage);
    }

    public function updateUsage(int $usage, array $data)
    {
        $usage = $this->getUsage($usage);
        return $usage->update($data);
    }

    public function deleteUsage(int $usage)
    {
        $usage = $this->getUsage($usage);
        return $usage->delete();
    }

    /**
     * @param string $filePath
     * @return int
     * @throws \Caleb\Practice\Exceptions\PracticeAppException
     * @author Caleb 2025/5/10
     */
    public function import(string $filePath)
    {
        // 代理 产品 团队 报告人 部门 媒体
        $agents = $products = $teams = $creators = $departments = $medias = [];

        $importer = new ImportService(maxRows: 20000, parseRow: function ($row) use (&$agents, &$products, &$teams, &$creators, &$departments, &$medias) {
            $row                  = array_slice($row, 1);
            $agents[$row[9]]      = 1;
            $products[$row[4]]    = 1;
            $teams[$row[3]]       = 1;
            $creators[$row[17]]   = 1;
            $departments[$row[2]] = 1;
            $medias[$row[7]]      = 1;
            return $row;
        });

        $rows = $importer->getRows($filePath);

        $agents      = array_filter(array_keys($agents));
        $products    = array_filter(array_keys($products));
        $teams       = array_filter(array_keys($teams));
        $creators    = array_filter(array_keys($creators));
        $departments = array_keys($departments);
        $medias      = array_keys($medias);

        // 格式化入库数据
        list($agents, $products, $teams, $creators) = $this->formatData($agents, $products, $teams, $creators, $departments, $medias);

        return $this->importDataToDB($rows, $agents, $products, $teams, $creators);
    }

    /**
     * @param $rows
     * @param $agents
     * @param $products
     * @param $teams
     * @param $creators
     * @return int
     * @author Caleb 2025/5/11
     */
    public function importDataToDB($rows, $agents, $products, $teams, $creators)
    {
        $dateTime = now()->format('Y-m-d H:i:s');
        $pdo      = DB::connection()->getPdo();
        if ($pdo->getAttribute(\PDO::ATTR_DRIVER_NAME) == 'pgsql') {
            $data   = [];
            $fields = ['month', 'date', 'department_id', 'team_id', 'product_id', 'exclusive_agent', 'channel', 'media', 'placement_method', 'agent_id', 'actual_usage', 'view', 'click', 'install', 'send_num', 'price', 'unique_id', 'creator_id', 'created_at', 'updated_at'];
            foreach ($rows as $insertRow) {
                if ($insertRow[0]) $data[] = $this->formatRowsForPgsql($insertRow, $agents, $products, $teams, $creators, $dateTime);
            }
            // $execTime = Benchmark::measure(fn() => $data && $pdo->pgsqlCopyFromArray((new Usage())->getTable(), $data, ',',fields: implode(',', $fields)));
            $data && $pdo->pgsqlCopyFromArray((new Usage())->getTable(), $data, ',', fields: implode(',', $fields));
            return count($data);
        } else {
            $total = 0;
            DB::beginTransaction();
            foreach (array_chunk($rows, 350) as $insertRows) {
                $data = [];
                foreach ($insertRows as $insertRow) {
                    if ($insertRow[0]) $data[] = $this->formatRows($insertRow, $agents, $products, $teams, $creators, $dateTime);
                    if (count($data) > 1) {
                        break;
                    }
                }
                $data && Usage::query()->insert($data);

                $total += count($data);
            }
            DB::commit();
            return $total;
        }
    }

    public function formatRowsForPgsql($row, $agents, $products, $teams, $creators, $dateTime)
    {
        $row = array_pad($row, 18, '');
        return implode(',', [
            str_replace('月', '', $row[0]),
            $row[1],
            DepartmentEnum::fromName($row[2]),
            $row[3] ? $teams[$row[3]] : 0,
            $row[4] ? $products[$row[4]] : 0,
            $row[5],
            $row[6],
            $row[7] ? MediaEnum::fromName($row[7]) : 0,
            $row[8],
            $agents[$row[9]],
            $row[10] ?: 0,
            $row[11] ?: 0,
            $row[12] ?: 0,
            $row[13] ?: 0,
            $row[14] ?: 0,
            $row[15] ?: 0,
            $row[16],
            $row[17] ? $creators[$row[17]] : (auth()?->user()?->id ?? 0),
            $dateTime,
            $dateTime,
        ]);
    }

    public function formatRows($row, $agents, $products, $teams, $creators, $dateTime)
    {
        // 长度不足则补齐
        $row = array_pad($row, 18, '');
        return [
            'month'            => str_replace('月', '', $row[0]),
            'date'             => $row[1],
            'department_id'    => DepartmentEnum::fromName($row[2]),
            'team_id'          => $row[3] ? $teams[$row[3]] : 0,
            'product_id'       => $row[4] ? $products[$row[4]] : 0,
            'exclusive_agent'  => $row[5],
            'channel'          => $row[6],
            'media'            => $row[7] ? MediaEnum::fromName($row[7]) : 0,
            'placement_method' => $row[8],
            'agent_id'         => $agents[$row[9]],
            'actual_usage'     => $row[10] ?: 0,
            'view'             => $row[11] ?: 0,
            'click'            => $row[12] ?: 0,
            'install'          => $row[13] ?: 0,
            'send_num'         => $row[14] ?: 0,
            'price'            => $row[15] ?: 0,
            'remark'           => $row[16],
            'creator_id'       => $row[17] ? $creators[$row[17]] : (auth()?->user()?->id ?? 0),
            'created_at'       => $dateTime,
            'updated_at'       => $dateTime,
        ];
    }

    /**
     * @param array $agents
     * @param array $products
     * @param array $teams
     * @param array $creators
     * @param array $departments
     * @param array $medias
     * @return array|void
     * @throws PracticeAppException
     * @author Caleb 2025/5/11
     */
    public function formatData(array $agents, array $products, array $teams, array $creators, array $departments, array $medias)
    {
        $errors = [];
        try {
            $this->checkMedia($medias);
        } catch (PracticeAppException $e) {
            $errors = array_merge($errors, $e->getData());
        }
        try {
            $this->checkDepartment($departments);
        } catch (PracticeAppException $e) {
            $errors = array_merge($errors, $e->getData());
        }

        try {
            return $this->formatDBData($agents, $products, $teams, $creators);
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
     * @param $medias
     * @return void
     * @throws \Caleb\Practice\Exceptions\PracticeAppException
     * @author Caleb 2025/5/11
     */
    public function checkMedia($medias)
    {
        $errors = [];
        foreach ($medias as $media) {
            if (!$media) {
                continue;
            }

            if (!MediaEnum::fromName($media)) {
                $errors[] = $media;
            }
        }
        $errors && $this->throwAppException('数据错误', 4735, ['medias' => $errors]);
    }

    /**
     * @param array $agents
     * @param array $products
     * @param array $teams
     * @param array $creators
     * @return array
     * @throws PracticeAppException
     * @author Caleb 2025/5/11
     */
    public function formatDBData(array $agents, array $products, array $teams, array $creators)
    {
        $dbAgents   = $agents ? AgentService::instance()->getAgentByNames($agents) : collect();
        $dbProducts = $products ? ProductService::instance()->getProductByNames($products) : collect();
        $dbTeams    = $teams ? TeamService::instance()->getTeamByNames($teams) : collect();
        $dbCreators = $creators ? UserService::instance()->getUserByUsernames($creators) : collect();

        $errors = [];
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

        // 团队
        $diffTeams = array_diff($teams, $dbTeams->pluck('name')->toArray());
        if ($diffTeams) {
            $errors['teams'] = array_values($diffTeams);
            // $this->throwAppException('团队不存在:' . implode(',', $diffTeams));
        }

        // 报告人
        $diffCreators = array_diff($creators, $dbCreators->pluck('username')->toArray());
        if ($diffCreators) {
            $errors['creators'] = array_values($diffCreators);
            // $this->throwAppException('报告人不存在:' . implode(',', $diffCreators));
        }

        $errors && $this->throwAppException('数据错误', 4735, $errors);

        return [
            $dbAgents->pluck('id', 'name')->toArray(),
            $dbProducts->pluck('id', 'name')->toArray(),
            $dbTeams->pluck('id', 'name')->toArray(),
            $dbCreators->pluck('id', 'username')->toArray(),
        ];
    }
}
