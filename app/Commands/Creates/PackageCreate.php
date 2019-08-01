<?php

namespace App\Commands\Creates;

use App\Controllers\FileSystemController;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class PackageCreate extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'create:package';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Creates a basic package.json';
    
    /**
     * @var FileSystemController $fsc
     */
    protected $fsc;
    
    /**
     * PackageCreate constructor.
     *
     * @param FileSystemController $fileSystemController
     */
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
        $templateDirectory = $this->fsc->getDirectory([
            'resources',
            'templates',
            'json'
        ]);
        
        $template = $templateDirectory . 'package.json.mustache';
        
        try {
            $package ['contents'] = $this->fsc->fs->get($template);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
            return;
        }
        
        return $this->fsc->storeFile(
            $this->fsc->trailingSlashIt($this->fsc->getCurrentDirectory()) . 'package.json',
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
