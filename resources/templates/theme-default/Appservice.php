<?php
namespace App\Providers;

class AppServiceProvider
{
	protected $providers;
	public function __construct()
	{
		$providers = include get_stylesheet_directory() . '/src/config/app.php';
		$this->providers = $providers['providers'];
		$this->boot();
	}
	
	public function boot(): void
	{
		foreach ($this->providers as $provider) {
			new $provider();
		}
	}
}
