<?php

namespace App\Commands\Creates\Php;

use App\Controllers\FileSystemController;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class FunctionsCreate extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'create:php:functions';
    
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
    
    /**
     * @var \Mustache_Engine $engine
     */
    protected $engine;
    
    public function __construct(FileSystemController $fileSystemController, \Mustache_Engine $engine)
    {
        parent::__construct();
        $this->fsc = $fileSystemController;
        $this->engine = $engine;
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
        
        $template = $templateDir . 'functions.php.mustache';
        
        try {
            $package ['contents'] = $this->fsc->fs->get($template);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
            return;
        }
        
        $package ['data'] = $this->engine->render($package['contents'], [
            'startLine'     => "<?php",
            'customLogo'    => $this->confirm('Do you want to add theme support for `custom-logo`?', true),
            'wooCommerce'   => $this->confirm('Do you want to add theme support for `woocommerce`?', false)
        ]);
        
        return $this->fsc->storeFile(
            $this->fsc->trailingSlashIt($this->fsc->getCurrentDirectory()) . 'functions.php',
            $package['data']
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
