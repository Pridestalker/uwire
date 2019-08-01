<?php

namespace App\Commands\Creates;

use App\Controllers\FileSystemController;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class DirectoriesCreate extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'create:directories';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Creates the base directories';

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
        $directories = [
            $this->fsc->getDirectory([$this->fsc->getCurrentDirectory(), 'assets'], false),
            $this->fsc->getDirectory([$this->fsc->getCurrentDirectory(), 'src'], false),
            $this->fsc->getDirectory([$this->fsc->getCurrentDirectory(), 'src', 'Providers'], false),
            $this->fsc->getDirectory([$this->fsc->getCurrentDirectory(), 'src', 'config'], false),
            $this->fsc->getDirectory([$this->fsc->getCurrentDirectory(), 'templates'], false),
            $this->fsc->getDirectory([$this->fsc->getCurrentDirectory(), 'templates', 'layouts'], false),
            $this->fsc->getDirectory([$this->fsc->getCurrentDirectory(), 'templates', 'layouts', 'helpers'], false),
        ];
        
        foreach ($directories as $directory) {
            $this->call('create:directory', [
                'dirname'   => [
                    $directory
                ]
            ]);
        }
        
        return true;
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
