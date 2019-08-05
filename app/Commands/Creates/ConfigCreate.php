<?php

namespace App\Commands\Creates;

use App\Controllers\FileSystemController;
use Foo\Bar\Baz;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class ConfigCreate extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'create:config';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Creates the config file.';
    
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
        if (!$this->confirm('Do you want to create a new uWire theme?', true)) {
            return false;
        }
        
        $dirTemp = explode($this->fsc->getSeparator(), $this->fsc->getCurrentDirectory());
        $directory = array_pop($dirTemp);
        
        $name = $this->ask('What is the theme name?', $directory);
        $slug = implode('_', explode(' ', $name));
        $description = $this->ask('Describe your theme');
        $version = $this->ask('What version do you want to start at', '0.0.1');
        
        $data = [
            'name'          => $name,
            'slug'          => $slug,
            'description'   => $description,
            'version'       => $version
        ];
        
        $fileDir = $this->fsc->getDirectory($this->fsc->getCurrentDirectory(), false);
        $fileDir .= 'uwire.config.json';
        
        if ($this->fsc->doesFileExists($fileDir)) {
            if (!$this->confirm('A config file has been found. Do you want to overwrite', true)) {
                $this->error('Aborted');
                return false;
            }
        }
        
        return $this->fsc->storeFile($fileDir, json_encode($data));
    }
}
