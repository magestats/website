<?php
declare(strict_types=1);

namespace App\Console\Components\Sitemap;

use App\Console\Components\AbstractCommand;
use Spatie\Crawler\Crawler;
use Spatie\Sitemap\SitemapGenerator;

class Generate extends AbstractCommand
{
    protected $signature = 'sitemap:generate';
    protected $description = 'Generate Sitemap';

    public function handle()
    {
        $this->output->title('Generate Sitemap');
        SitemapGenerator::create(env('APP_URL'))
            ->configureCrawler(function (Crawler $crawler) {
                $crawler->ignoreRobots();
            })->writeToFile('public/sitemap.xml');
        $this->output->writeln($this->getMemoryUsage());
    }
}
