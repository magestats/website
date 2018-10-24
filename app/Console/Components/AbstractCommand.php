<?php
declare(strict_types=1);

namespace App\Console\Components;

use Illuminate\Console\Command;


abstract class AbstractCommand extends Command
{
    /**
     * @return string
     */
    protected function getMemoryUsage(): string
    {
        return sprintf(
            'memory: %s | peak: %s',
            $this->byteFormat(memory_get_usage(false), 'MB', 2),
            $this->byteFormat(memory_get_peak_usage(false), 'MB', 2)
        );
    }

    /**
     * @param $bytes
     * @param string $unit
     * @param int $decimals
     * @return string
     */
    protected function byteFormat(float $bytes, string $unit = 'MB', int $decimals = 2): string
    {
        $units = [
            'B' => 0,
            'KB' => 1,
            'MB' => 2,
            'GB' => 3,
            'TB' => 4,
            'PB' => 5,
            'EB' => 6,
            'ZB' => 7,
            'YB' => 8
        ];

        $value = 0;

        if ($bytes > 0) {
            if (!array_key_exists($unit, $units)) {
                $pow = floor(log($bytes)/log(1024));
                $unit = array_search($pow, $units);
            }
            $value = ($bytes/pow(1024, floor($units[$unit])));
        }

        if (!is_numeric($decimals) || $decimals < 0) {
            $decimals = 2;
        }

        return sprintf('%.' . $decimals . 'f '. $unit, $value);
    }
}
