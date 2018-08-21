<?php
declare(strict_types=1);

namespace App\Console\Components\Sitemap;

use App\Repositories;
use App\Statistics;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Spatie\Sitemap\SitemapGenerator;
use Symfony\Component\Console\Input\ArrayInput;

class Generate extends Command
{
    protected $signature = 'sitemap:generate';
    protected $description = 'Generate Sitemap';

    public function handle()
    {
        $this->output->title('Generate Sitemap');
        SitemapGenerator::create(env('APP_URL'))->writeToFile('public/sitemap.xml');
    }
}
