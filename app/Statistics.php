<?php

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

    public function __construct(PullRequests $pullRequests, Issues $issues)
    {
        $this->pullRequests = $pullRequests;
        $this->issues = $issues;
    }

    /**
     * @param int $start
     * @param int $end
     * @return array
     */
    protected function getRangeArray(int $start, int $end): array
    {
        $data = [];
        $publicRepos = explode(',', getenv('MAGENTO_REPOS'));

        foreach (range($start, $end) as $item) {
            foreach ($publicRepos as $repo) {
                $data[$repo][$item] = 0;
            }
            $data['total'][$item] = 0;
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
     * @return array
     */
    protected function getDataset(string $label, array $data, array $color, string $type = 'bar')
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
            'type' => $type
        ];
    }

    /**
     * @param string $filename
     * @param int $year
     * @param array $data
     */
    protected function storeDataByYear(string $filename, int $year, array $data)
    {
        $json = json_encode($data, true);
        Storage::put(sprintf('public/%d/%s.json', $year, $filename), $json, 'public');
    }
}
