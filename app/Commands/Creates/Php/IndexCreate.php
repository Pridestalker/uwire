<?php

namespace App\Commands\Creates\Php;

use App\Controllers\FileSystemController;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class IndexCreate extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'create:php:index';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Command description';
    
    /**
     * @var FileSystemController $fsc
     */
    protected $fsc;
    
    public function __construct(FileSystemController $fileSystemController)
    {
        parent::__construct();
        $this->fsc = $fileSystemController;
    }
    
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $templateDir = $this->fsc->getDirectory([
            'resources',
            'templates',
            'theme-default'
        ]);
        
        $template = $templateDir . 'index.php';
    
        try {
            $package ['contents'] = $this->fsc->fs->get($template);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
            return;
        }
        
        return $this->fsc->storeFile(
            $this->fsc->trailingSlashIt($this->fsc->getCurrentDirectory()) . 'index.php',
            $package['contents']
        ) > 0?: false;
    }

    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
