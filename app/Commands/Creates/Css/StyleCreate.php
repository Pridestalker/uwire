<?php

namespace App\Commands\Creates\Css;

use App\Models\Config;
use App\Controllers\FileSystemController;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class StyleCreate extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'create:css:style';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Creates WP Style.css';
    
    /**
     * @var FileSystemController $fsc
     */
    protected $fsc;
    
    /**
     * @var \Mustache_Engine $engine
     */
    protected $engine;
    
    /**
     * @var Config $config
     */
    protected $config;
    
    public function __construct(
        FileSystemController $fileSystemController,
        \Mustache_Engine $engine,
        Config $config
    )
    {
        parent::__construct();
        $this->fsc = $fileSystemController;
        $this->engine = $engine;
        $this->config = $config;
    }
    
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $templateDir = $this->fsc->getDirectory([
            'resources',
            'templates',
            'wp-default'
        ]);
    
        $template = $templateDir . 'style.css.mustache';
        
        try {
            $package ['style']['raw']= $this->fsc->fs->get($template);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
            return false;
        }
    
        $package ['style']['rendered'] = $this->engine->render($package['style']['raw'], [
            'theme_name'        => $this->config->name,
            'theme_uri'         => $this->ask('What is the theme uri'),
            'theme_author'      => $this->ask('What\'s your name?'),
            'author_uri'        => $this->ask('What\'s your website'),
            'theme_description' => $this->ask('Describe your theme', $this->config->description),
            'license'           => $this->ask('what license do you want?'),
            'license_url'       => $this->ask('License url'),
            'text_domain'       => $this->ask('What should the text domain be?', $this->config->slug),
            'tags'              => $this->ask('Any tags for this theme?'),
            'version'           => $this->ask('Theme version?', $this->config->version)
        ]);
    
    
        $this->fsc->fs->put($this->fsc->trailingSlashIt($this->fsc->getCurrentDirectory()) . 'style.css', $package['style']['rendered']);
    
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
