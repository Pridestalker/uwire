<?php

namespace App\Commands\Creates\Composer;

use App\Controllers\FileSystemController;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class ComposerCreate extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'create:composer:json';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Creates composer package.json';
    
    /**
     * @var array[]
     */
    private $requiredPackages;
    
    /**
     * @var array[]
     */
    private $devPackages;
    
    /**
     * @var FileSystemController $fsc
     */
    protected $fsc;
    
    /**
     * @var \Mustache_Engine $engine
     */
    protected $engine;
    
    /**
     * PackageCreate constructor.
     *
     * @param FileSystemController $fileSystemController
     * @param \Mustache_Engine $engine
     */
    public function __construct(
        FileSystemController $fileSystemController,
        \Mustache_Engine $engine
    )
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
        $this->setBasePackages();
        
        $this->writeFile();
    }
    
    private function writeFile() {
        $templateDirectory = $this->fsc->getDirectory([
            'resources',
            'templates',
            'json'
        ]);
        
        $template = $templateDirectory . 'composer.json.mustache';
    
        try {
            $package ['contents'] = $this->fsc->fs->get($template);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
            return;
        }
        
        $required = $this->getRequiredPackages();
        $required[count($required) -1]['hasComma'] = false;
        
        $dev = $this->getDevPackages();
        $dev[count($dev) -1]['hasComma'] = false;
        
        $package ['data'] = $this->engine->render($package['contents'], [
            'requiredPackages'  => $required,
            'devPackages'  => $dev
        ]);
    
        return $this->fsc->storeFile(
            $this->fsc->trailingSlashIt($this->fsc->getCurrentDirectory()) . 'composer.json',
            $package['data']
        ) > 0?: false;
    }
    
    private function setBasePackages(): void {
        $packages ['required'] = [
            'yahnis-elsts/plugin-update-checker' => '^4.6',
            'aristath/kirki'                     => '^3.0',
            'timber/timber'                      => '^1.10',
            'jjgrainger/posttypes'               => '^2.0',
            'htmlburger/carbon-fields'           => '^3.1',
            'dusank/knapsack'                    => '^10.0'
        ];
        
        $packages ['dev'] = [
            'roave/security-advisories' => 'dev-master'
        ];
        
        foreach ($packages['required'] as $key => $value) {
            $this->addRequiredPackages($key, $value);
        }
        
        foreach ($packages['dev'] as $key => $value) {
            $this->addDevPackages($key, $value);
        }
    }
    
    public function getRequiredPackages(): array {
        return $this->requiredPackages;
    }
    
    public function addRequiredPackages($package, $version = '*'): void {
        $this->requiredPackages [] = [
            'package'   => $package,
            'version'   => $version,
            'hasComma'  => true,
        ];
    }
    
    public function getDevPackages(): array {
        return $this->devPackages;
    }
    
    public function addDevPackages($package, $version = '*'): void {
        $this->devPackages [] = [
            'package'   => $package,
            'version'   => $version,
            'hasComma'  => true
        ];
    }
}
