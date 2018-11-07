<?php
declare(strict_types=1);

namespace App\Utils;

use Carbon\Carbon;

trait Date
{
    /**
     * @param int $year
     * @param int $createdYear
     * @param int $createdMonth
     * @return array
     */
    public function getMonthRange(int $year, int $createdYear, int $createdMonth): array
    {
        $minMonth = 1;
        $maxMonth= 12;
        if ($year === (int) date('Y')) {
            $maxMonth = date('m');
        }

        if ($year === $createdYear) {
            $minMonth = $createdMonth;
        }

        $months = [];
        foreach (range($minMonth, $maxMonth) as $month) {
            if ($month < 10) {
                $month = sprintf('0%d', $month);
            }
            $months[$month] = Carbon::create($year, $month)->format('F');
        }
        return $months;
    }
}
