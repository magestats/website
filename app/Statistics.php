<?php
declare(strict_types=1);

namespace App;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class Statistics
{
    const DATASET_FILL = false;
    const DATASET_TRANSPARENCY = 0.8;
    const STATUS_CLOSED = 'closed';
    const STATUS_CREATED = 'created';
    const STATUS_MERGED = 'merged';

    /**
     * @var PullRequests
     */
    protected $pullRequests;
    /**
     * @var Issues
     */
    protected $issues;
    /**
     * @var array
     */
    protected $createdColor = [159, 215, 203];
    /**
     * @var array
     */
    protected $closedColor = [188, 183, 184];
    /**
     * @var array
     */
    protected $mergedColor = [255, 154, 114];
    /**
     * @var array
     */
    protected $rejectedColor = [255, 238, 114];
    /**
     * @var bool
     */
    protected $hasItems = false;

    /**
     * Statistics constructor.
     * @param PullRequests $pullRequests
     * @param Issues $issues
     */
    public function __construct(PullRequests $pullRequests, Issues $issues)
    {
        $this->pullRequests = $pullRequests;
        $this->issues = $issues;
    }

    /**
     * @param int $year
     * @return array
     */
    protected function getRangeArray(int $year): array
    {
        $data = [];
        $publicRepos = explode(',', getenv('MAGENTO_REPOS'));

        foreach (range(1, 12) as $month) {
            $days = Carbon::create($year, $month)->daysInMonth;

            foreach ($publicRepos as $repo) {
                $data[$repo]['total'][$month] = 0;
                foreach (range(1, $days) as $day) {
                    $data[$repo]['months'][$month][$day] = 0;
                }
            }
            $data['total'][$month] = 0;
        }
        return $data;
    }

    /**
     * @param int $year
     * @return array
     */
    protected function getMonthRange(int $year): array
    {
        $data = [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December'
        ];

        if (Carbon::create($year)->isCurrentYear()) {
            foreach ($data as $month => $row) {
                if ($month > date('n')) {
                    unset($data[$month]);
                }
            }
        }

        return array_values($data);
    }

    /**
     * @param string $label
     * @param array $data
     * @param array $color
     * @param string $type
     * @param bool $hidden
     * @return array
     */
    protected function getDataset(string $label, array $data, array $color, string $type = 'bar', bool $hidden = false): array
    {
        $transparency = self::DATASET_TRANSPARENCY;
        return [
            'label' => $label,
            'data' => array_values($data),
            'fill' => self::DATASET_FILL,
            'backgroundColor' => sprintf('rgba(%s, %s)', implode(',', $color), $transparency),
            'borderColor' => sprintf('rgba(%s, %s)', implode(',', $color), $transparency),
            'pointBackgroundColor' => sprintf('rgba(%s, %s)', implode(',', $color), $transparency),
            'pointBorderColor' => sprintf('rgba(%s, %s)', implode(',', $color), $transparency),
            'pointHoverBackgroundColor' => sprintf('rgba(%s, %s)', implode(',', $color), $transparency),
            'type' => $type,
            'hidden' => $hidden,
        ];
    }

    /**
     * @param array $closed
     * @param array $merged
     * @return array
     */
    protected function getRejected(array $closed, array $merged): array
    {
        $data = [];
        foreach ($closed as $month => $value) {
            $data[$month] = $value - $merged[$month];
        }
        return $data;
    }

    /**
     * @param array $closed
     * @param array $merged
     * @return array
     */
    protected function getAcceptanceRate(array $closed, array $merged): array
    {
        $data = [];
        foreach ($closed as $month => $value) {
            if ($value === 0) {
                $data[$month] = '0%';
                continue;
            }
            $data[$month] = sprintf('%s%%', round(($merged[$month] / $value) * 100));
        }
        return $data;
    }

    /**
     * @param string $filename
     * @param int $year
     * @param array $data
     */
    protected function storeDataByYear(string $filename, int $year, array $data)
    {
        $json = json_encode($data);
        Storage::put(sprintf('public/%d/%s.json', $year, $filename), $json, 'public');
    }

    /**
     * @param array $data
     * @return int
     */
    protected function countTotals(array $data): int
    {
        $total = 0;
        foreach ($data as $value) {
            $total += (int)$value;
        }
        if ($total > 0) {
            $this->hasItems = true;
        }
        return $total;
    }

    protected function reset()
    {
        $this->hasItems = false;
    }
}
