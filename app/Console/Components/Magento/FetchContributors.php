<?php
declare(strict_types=1);

namespace App\Console\Components\Magento;

use App\Console\Components\AbstractCommand;
use App\Contributors;
use App\PullRequests;
use App\Services\GitHub\Api;
use Carbon\Carbon;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

class FetchContributors extends AbstractCommand
{
    const ARGUMENT_YEAR = 'year';
    protected $signature = 'magento:fetch:contributors {year=current}';
    protected $description = 'Fetch Contributors';

    public function handle(Api $api, PullRequests $pullRequests, Contributors $contributors)
    {
        $existing = $this->getExisting($contributors);
        $year = $this->input->getArgument(self::ARGUMENT_YEAR);
        if ($year && $year === 'current' && !is_numeric($year)) {
            $year = (int)date('Y');
        }

        $query = $pullRequests->select(['author', 'created']);
        if ($year !== 'all') {
            $query
                ->where('created', '>', Carbon::createFromDate($year)->firstOfYear())
                ->where('created', '<', Carbon::createFromDate($year)->lastOfYear());
        }
        $query->orderBy('created', 'ASC');

        $result = $query->get()->toArray();

        $progressBar = new ProgressBar($this->output, count($result));
        $progressBar->start();
        foreach ($result as $contributor) {
            try {
                if (isset($existing[$contributor['author']])) {
                    $progressBar->advance();
                    continue;
                }
                $authorData = $api->fetchContributor($contributor['author'])->toArray();
                $data = [
                    'author' => $contributor['author'],
                    'first_contribution' => $contributor['created'],
                    'author_id' => $authorData[Api::GITHUB_API_RESULT_DATA]['id'],
                    'node_id' => $authorData[Api::GITHUB_API_RESULT_DATA]['node_id'],
                    'name' => $authorData[Api::GITHUB_API_RESULT_DATA]['name'],
                    'company' => $authorData[Api::GITHUB_API_RESULT_DATA]['company'],
                    'blog' => $authorData[Api::GITHUB_API_RESULT_DATA]['blog'],
                    'location' => $authorData[Api::GITHUB_API_RESULT_DATA]['location'],
                    'bio' => $authorData[Api::GITHUB_API_RESULT_DATA]['bio'],
                    'meta' => json_encode($authorData[Api::GITHUB_API_RESULT_DATA])
                ];
                $contributors->store($data);
                $existing[$contributor['author']] = $contributor['created'];
                $progressBar->advance();
            } catch (\Exception $e) {
                $this->output->writeln($this->getMemoryUsage());
                $this->warn($e->getMessage());
            }
        }
        $progressBar->finish();
    }

    /**
     * @param Contributors $contributors
     * @return array
     */
    private function getExisting(Contributors $contributors): array
    {
        $data = [];
        $result = $contributors->select(['author', 'first_contribution'])->get()->toArray();
        if(\count($result)) {
            foreach ($result as $item) {
                $data[$item['author']] = $item['first_contribution'];
            }
        }
        return $data;
    }
}
