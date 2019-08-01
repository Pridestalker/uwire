<?php
namespace App\Models;

use App\Controllers\FileSystemController;

class Config {
    /**
     * @var string $name
     */
    public $name;
    
    /**
     * @var string $slug
     */
    public $slug;
    
    /**
     * @var string $desciption
     */
    public $description;
    
    /**
     * @var string $version
     */
    public $version;
    
    /**
     * @var FileSystemController $fsc
     */
    private $fsc;
    
    public function __construct(FileSystemController $fileSystemController) {
        $this->fsc = $fileSystemController;
        
        try {
            $data = json_decode($this->fsc->fs->get('uwire.config.json'), true);
        } catch (\Exception $exception) {
            $data = false;
        }
        
        if ($data) {
            foreach ($data as $key => $value) {
                $this->{$key} = $value;
            }
        }
    }
    
}
