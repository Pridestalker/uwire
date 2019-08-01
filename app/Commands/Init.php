<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Filesystem\Filesystem;
use LaravelZero\Framework\Commands\Command;

class Init extends Command
{
	
	private $mustache;
	
	/**
	 * @var Filesystem $filesystem
	 */
	private $filesystem;
	
	/**
	 * @var array $data the config data
	 */
	private $data;
	
	public function __construct(\Mustache_Engine $engine, Filesystem $filesystem)
	{
		parent::__construct();
		$this->mustache = $engine;
		$this->filesystem = $filesystem;
	}
	
	/**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'init';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Create a new uWire theme';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
    	$this->task('Setting up', function () {
            return $this->call('create:config');
	    });
	    
	    $this->task('Create directories', function () {
            $this->call('create:directories');
	    });
	    
	    $this->task('Create package.json', function () {
	    	$this->call('create:package');
	    });
	    
	    $this->task('Creating WP Required files', function() {
	        $this->call('create:css:style');
	        $this->call('create:php:functions');
	        $this->call('create:php:index');
	    });

	    $this->task('Creating Composer files', function () {
	        $this->call('create:composer:json');
	        shell_exec('composer install');
        });
	    
	    $this->task('Setup frontend files', function () {
		    return $this->createBasicLayout();
	    });

		$this->task('Setup app files', function () {
		   return $this->createBasicClasses();
		});
    }
    
    private function createBasicLayout(): bool
    {
	    try {
		    $package ['app.twig']['raw']= $this->filesystem->get(
		    	$this->getPharPath() . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'theme-default' . DIRECTORY_SEPARATOR . 'base.layout.mustache'
		    );
	    } catch (\Exception $e) {
		    $this->error($e->getMessage());
		    return false;
	    }
	    
	    $package ['app.twig']['rendered'] = $this->mustache->render($package['app.twig']['raw'], []);
	
	    $directory = getcwd() . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'layouts' . DIRECTORY_SEPARATOR;
	    
	    if (!$this->filesystem->isDirectory($directory)) {
	    	$this->filesystem->makeDirectory($directory, 0755, true);
	    }
	
	    $this->filesystem->put(
	    	 $directory . 'app.twig', $package['app.twig']['rendered']
	    );
	
	    return $this->createBasicHelpers();
    }
    
    private function createBasicHelpers(): bool
    {
	    $directory = getcwd() . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'layouts' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR;
	
	    if (!$this->filesystem->isDirectory($directory)) {
		    $this->filesystem->makeDirectory($directory, 0755, true);
	    }
	    
	    $this->filesystem->put($directory . 'head.twig', '');
	    $this->filesystem->put($directory . 'header.twig', '');
	    $this->filesystem->put($directory . 'footer.twig', '');
    	
	    return true;
    }
    
    private function createBasicClasses(): bool
    {
	    $directory = getcwd() . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'config';
	
	    if (!$this->filesystem->isDirectory($directory)) {
		    $this->filesystem->makeDirectory($directory, 0755, true);
		    $this->filesystem->makeDirectory(getcwd() . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Providers' . DIRECTORY_SEPARATOR, 0755, true);
	    }
	    
	    $this->filesystem->put(getcwd() . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'app.php', $this->filesystem->get(
	    	$this->getPharPath() . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'theme-default' . DIRECTORY_SEPARATOR . 'app.php')
	    );
	    $this->filesystem->put(getcwd() . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Providers' . DIRECTORY_SEPARATOR . 'AppServiceProvider.php', $this->filesystem->get(
	    	$this->getPharPath() . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'theme-default' . DIRECTORY_SEPARATOR . 'Appservice.php'
	    ));
	    $this->filesystem->put($directory . DIRECTORY_SEPARATOR . 'app.php', $this->filesystem->get(
	    	$this->getPharPath() . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'theme-default' . DIRECTORY_SEPARATOR . 'config.php'
	    ));
	
	    return true;
    }
    
    private function getPharPath(): string
    {
	    $path = \Phar::running(false);
	    
	    if (\Phar::running( false ) !== '') {
		    $path = dirname($path) . DIRECTORY_SEPARATOR;
	    }
	    
	    return $path;
    }
}
