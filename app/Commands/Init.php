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
    	$this->task( 'Setting up', function () {
	    	return $this->createConfigFile();
	    });
	    
	    $this->task('Create directories', function () {
	    	return $this->createDirectories();
	    });
	    
	    $this->task('Create package.json', function () {
	    	return $this->createPackageJson();
	    });
	    
	    $this->task('Creating WP Required files', function() {
		    return $this->createStyleCss() && $this->createFunctionsPhp() && $this->createIndexPhp();
	    });

	    $this->task('Setup frontend files', function () {
		    return $this->createBasicLayout();
	    });

		$this->task('Setup app files', function () {
		   return $this->createBasicClasses();
		});
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
    
    private function createConfigFile(): bool
    {
    	if (!$this->confirm('Do you want to create a new uWire theme?', true)) {
    		return false;
	    }
    	
    	$dirTemp = explode('/', getcwd());
    	$directory = array_pop($dirTemp);
    	
    	$name = $this->ask('What is the theme name?', $directory);
    	$slug = implode('_', explode(' ', $name));
    	$description = $this->ask('Theme description');
    	$version = $this->ask('Version', '0.0.1');
    	
    	$this->data = [
		    'name'          => $name,
		    'slug'          => $slug,
		    'description'   => $description?? '',
		    'version'       => $version?? '0.0.0'
	    ];
    	
    	if ($this->filesystem->isFile(getcwd() . DIRECTORY_SEPARATOR . 'uwire.config.json')) {
    		if (!$this->confirm('A config file has been found already. Do you want to overwrite?', true)) {
    			$this->error('Aborted by user');
    			return false;
		    }
	    }
    	
    	return $this->filesystem->put(getcwd() . DIRECTORY_SEPARATOR . 'uwire.config.json', json_encode($this->data));
    }
    
    private function createDirectories(): bool
    {
    	if (!$this->filesystem->isDirectory(getcwd() . DIRECTORY_SEPARATOR . 'assets')) {
    		$this->filesystem->makeDirectory( getcwd() . DIRECTORY_SEPARATOR . 'assets');
	    }
    	if (!$this->filesystem->isDirectory(getcwd() . DIRECTORY_SEPARATOR . 'src')) {
    		$this->filesystem->makeDirectory( getcwd() . DIRECTORY_SEPARATOR . 'src');
	    }
    	if (!$this->filesystem->isDirectory(getcwd() . DIRECTORY_SEPARATOR . 'templates')) {
    		$this->filesystem->makeDirectory( getcwd() . DIRECTORY_SEPARATOR . 'templates');
	    }
    	
    	if (!$this->filesystem->isDirectory(getcwd() . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR  . 'layouts')) {
    		$this->filesystem->makeDirectory(getcwd() . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR  . 'layouts');
	    }
    	
    	if (!$this->filesystem->isDirectory(getcwd() . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR  . 'config')) {
    		$this->filesystem->makeDirectory(getcwd() . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR  . 'config');
	    }
    	if (!$this->filesystem->isDirectory(getcwd() . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR  . 'Providers')) {
    		$this->filesystem->makeDirectory(getcwd() . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR  . 'Providers');
	    }
    	
    	if (!$this->filesystem->isDirectory(getcwd() . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR  . 'layouts' . DIRECTORY_SEPARATOR . 'helpers')) {
    		$this->filesystem->makeDirectory(getcwd() . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR  . 'layouts' . DIRECTORY_SEPARATOR . 'helpers');
	    }
    	
    	
    	return true;
    }
    
    private function createPackageJson(): bool
    {
    	try {
    		$package ['content']= $this->filesystem->get($this->getPharPath() . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'json' . DIRECTORY_SEPARATOR . 'package.json.mustache');
	    } catch (\Exception $e) {
    		$this->error($e->getMessage());
    		return false;
	    }
	    
	    $this->filesystem->put(getcwd() . DIRECTORY_SEPARATOR . 'package.json', $package['content']);
    	
	    return true;
    }
    
    private function createIndexPhp(): bool
    {
	    $this->filesystem->put(getcwd() . DIRECTORY_SEPARATOR . 'index.php', '<?php');
	    
    	return true;
    }
    
    private function createFunctionsPhp(): bool
    {
	    $bareData = "<?php\r\n\r\n
include_once get_stylesheet_directory() . '/vendor/autoload.php'; \r\n";
	    
	    $bareData .= $this->confirm('Do you want to add theme support for `custom-logo`?', true)
	        ? "add_theme_support('custom-logo');\r\n"
		    : '';
	     
	    $bareData .= $this->confirm('Do you want to add theme support for `woocommerce`?', false)
	        ? "add_theme_support('woocommerce');\r\n"
		    : '';
	    
	    
	    $this->filesystem->put(getcwd() . DIRECTORY_SEPARATOR . 'functions.php', $bareData);
	
	    return true;
    }
    
    private function createStyleCss(): bool
    {
	    try {
		    $package ['style']['raw']= $this->filesystem->get(
		    	$this->getPharPath() . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'wp-default' . DIRECTORY_SEPARATOR . 'style.css.mustache'
		    );
	    } catch (\Exception $e) {
		    $this->error($e->getMessage());
		    return false;
	    }
	    
	    $package ['style']['rendered'] = $this->mustache->render($package['style']['raw'], [
	    	'theme_name'        => $this->data['name'],
		    'theme_uri'         => $this->ask('What is the theme uri'),
		    'theme_author'      => $this->ask('What\'s your name?'),
		    'author_uri'        => $this->ask('What\'s your website'),
		    'theme_description' => $this->ask('Describe your theme'),
		    'license'           => $this->ask('what license do you want?'),
		    'license_url'       => $this->ask('License url'),
		    'text_domain'       => $this->ask('What should the text domain be?', $this->data['slug']),
		    'tags'              => $this->ask('Any tags for this theme?'),
	    ]);
	    
	
	    $this->filesystem->put(getcwd() . DIRECTORY_SEPARATOR . 'style.css', $package['style']['rendered']);
	
	    return true;
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
