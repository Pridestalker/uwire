<?php

namespace App\Commands\Creates;

use App\Controllers\FileSystemController;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class DirectoryCreate extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'create:directory {dirname* : the name of the directory to create (required)}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Creates a directory';
    
    /**
     * @var FileSystemController $fsc
     */
    protected $fsc;

    public function __construct(FileSystemController $fileSystemController) {
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
        $directory = implode($this->fsc->getSeparator(), $this->argument('dirname'));
        if (!$this->doesDirectoryExists($directory)) {
            return $this->fsc->createDirectory($directory);
        }
        
        $this->warn('Directory already exists.');
        return false;
    }
    
    /**
     * Check if directory exists.
     *
     * @param string $directory the directory to check.
     *
     * @return bool
     */
    protected function doesDirectoryExists($directory): bool {
        return $this->fsc->doesDirectoryExist($directory);
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
