<?php

namespace App\Console\Commands;

use App\Helpers\CacheHelper;
use Illuminate\Console\Command;

class ClearLmsCacheCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:clear-lms
                            {--all : Also run config:clear, route:clear, view:clear}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear LMS application cache (tags, courses). Use --all to also clear config, route, and view cache.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Clearing LMS application cache...');

        CacheHelper::clearTagsCache();
        CacheHelper::clearCoursesCache();

        $this->info('Tags and courses cache cleared.');

        if ($this->option('all')) {
            $this->call('config:clear');
            $this->call('route:clear');
            $this->call('view:clear');
            $this->info('Config, route, and view cache cleared.');
        }

        return self::SUCCESS;
    }
}
