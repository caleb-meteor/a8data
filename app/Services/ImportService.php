<?php

namespace App\Services;

use Caleb\Practice\Exceptions\PracticeAppException;
use Caleb\Practice\ThrowException;
use Closure;

class ImportService
{
    use ThrowException;

    public function __construct(protected bool $skipHeader = true, protected int $maxRows = 0, protected Closure|null $parseRow = null)
    {
    }

    /**
     * @param string $filePath
     * @return array
     * @throws PracticeAppException
     * @author Caleb 2025/5/10
     */
    public function getRows(string $filePath)
    {
        $rowCount = 0;
        $rows     = [];
        if (($handle = fopen($filePath, 'r')) !== false) {
            while (($row = fgetcsv($handle)) !== false) {
                $rowCount++;
                if ($this->maxRows && $rowCount > $this->maxRows) {
                    $this->throwAppException('导入数据超过最大限制' . ($this->maxRows - 1));
                }
                if ($this->skipHeader && $rowCount === 1) {
                    continue;
                }
                if ($this->parseRow) {
                    $row = call_user_func($this->parseRow, $row);
                }
                $rows[] = $row;
            }
            fclose($handle);
        } else {
            $this->throwAppException('文件读取失败');
        }

        return $rows;
    }
}
