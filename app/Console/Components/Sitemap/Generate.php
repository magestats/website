<?php
declare(strict_types=1);

namespace App\Console\Components\Sitemap;

use App\Console\Components\AbstractCommand;
use Illuminate\Support\Facades\Storage;
use Spatie\Crawler\Crawler;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\SitemapGenerator;
use Spatie\Sitemap\Tags\Url;

class Generate extends AbstractCommand
{
    const FILE_PATH = 'public/sitemap.xml';

    protected $signature = 'sitemap:generate';
    protected $description = 'Generate Sitemap';

    public function handle()
    {
        $this->output->title('Generate Sitemap');
        $this->generateManualSitemap();
        $this->output->writeln($this->getMemoryUsage());
    }

    private function generateManualSitemap()
    {
        $sitemap = Sitemap::create();
        $appUrl = env('APP_URL');
        $contributors = [];

        $files = Storage::allFiles('public');

        foreach ($files as $file) {
            $file = str_replace(['public/','.json'], '', $file);

            if ($this->hasIgnoreWord($file)) {
                continue;
            }
            $fileParts = explode('/', $file);
            if (isset($fileParts[4])) {
                if ($fileParts[4] <= 9) {
                    $fileParts[4] = sprintf('0%s', $fileParts[4]);
                }
                $sitemap->add(Url::create($this->getUrl($appUrl, ['repositories', $fileParts[1], $fileParts[2], $fileParts[0], $fileParts[4]]))->setPriority(0.8));
            } elseif (isset($fileParts[3])) {
                $sitemap->add(Url::create($this->getUrl($appUrl, ['repositories', $fileParts[1], $fileParts[2], $fileParts[0]]))->setPriority(0.8));
            } elseif (isset($fileParts[2])) {
                if ($fileParts[1] === 'contributors') {
                    $contributors[] = $fileParts[2];
                }
            } elseif (isset($fileParts[1])) {
                $sitemap->add(Url::create($this->getUrl($appUrl, [$fileParts[1], $fileParts[0]]))->setPriority(0.8));
            }
        }
        foreach (range(2011, date('Y')) as $year) {
            $sitemap->add(Url::create($this->getUrl($appUrl, ['reports', $year]))->setPriority(0.8));
        }

        foreach (array_unique($contributors) as $contributor) {
            $sitemap->add(Url::create($this->getUrl($appUrl, ['contributor', $contributor]))->setPriority(0.8));
        }
        $sitemap->add(Url::create($this->getUrl($appUrl, ['about']))->setPriority(0.8));
        $sitemap->add(Url::create($this->getUrl($appUrl, ['']))->setPriority(1.0));
        $sitemap->writeToFile(self::FILE_PATH);
    }

    private function getUrl(string $appUrl, array $paths)
    {
        return sprintf('%s/%s', $appUrl, implode('/', $paths));
    }

    private function hasIgnoreWord(string $text)
    {
        $ignorelist = [
            'issues',
            'pullrequests'
        ];

        foreach ($ignorelist as $ignore) {
            if (stripos($text, $ignore) !== false) {
                return true;
            }
        }
        return false;
    }

    private function generateCrawledSitemap()
    {
        SitemapGenerator::create(env('APP_URL'))
            ->configureCrawler(function (Crawler $crawler) {
                $crawler->ignoreRobots();
            })->writeToFile(self::FILE_PATH);
    }
}
