<?php

namespace App\Commands\Creates\Php;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class ClassCreate extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'create:class
                            {type=general : The type of class}
                            {--N|name= : The classname}
                            {--P|path= : The relative path where to add the class}
                            {--namespace= : The namespace the class should be in}
                            ';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Create a class on specific type';
    
    /**
     * @var string $name
     */
    protected $name;
    
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $this->info($this->argument('type'));
        
        $this->handleName();
    }
    
    private function handleName(): void
    {
        if ($name = $this->option('name')) {
            $this->info($name);
            
            $this->name = $name;
        } else {
            $this->name = $this->ask('Enter classname');
        }
    }
}
