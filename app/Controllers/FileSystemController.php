<?php

namespace App\Controllers;

use Illuminate\Filesystem\Filesystem;

class FileSystemController
{
    /**
     * Holds the Filesystem.
     *
     * @var Filesystem $fs
     */
    public $fs;
    
    private $separator = DIRECTORY_SEPARATOR;
    
    public function __construct(Filesystem $fs)
    {
        $this->fs = $fs;
    }
    
    /**
     * Gets the directory
     *
     * @param array|string $directory
     * @param boolean      $addPharPath
     *
     * @return string the directory
     */
    public function getDirectory($directory, $addPharPath = true): string
    {
        if (is_array($directory)) {
            $directory = implode($this->separator, $directory);
        }
        
        if ($addPharPath) {
            $directory = $this->trailingSlashIt($this->getPharPath()) . $directory;
        }
        
        return $this->trailingSlashIt($directory);
    }
    
    /**
     * @return string the phar path if it exists
     */
    public function getPharPath(): string
    {
        $path = \Phar::running(false);
        
        if ($path !== '') {
            $path = dirname($path) . $this->separator;
        } else {
            $path = $this->getCurrentDirectory();
        }
        
        return $path;
    }
    
    /**
     * Uses the Filesystem to store data
     *
     * @param string $directory
     * @param string $data
     *
     * @return bool
     */
    public function storeFile($directory, $data): bool {
        return $this->fs->put($directory, $data) > 0?: false;
    }
    
    /**
     * Uses the Filesystem to create a directory
     *
     * @param $directory
     *
     * @return bool
     */
    public function createDirectory($directory): bool {
        return $this->fs->makeDirectory($directory) > 0?: false;
    }
    
    /**
     * Returns the cwd
     *
     * @return string|null
     */
    public function getCurrentDirectory(): string {
        return getcwd()?: null;
    }
    
    /**
     * Check if file exists
     *
     * @param string  $file
     *
     * @return bool
     */
    public function doesFileExists($file): bool {
        return $this->fs->isFile($file);
    }
    
    /**
     * Check if the directory exists
     *
     * @param string $directory
     *
     * @return bool
     */
    public function doesDirectoryExist($directory): bool {
        return $this->fs->isDirectory($directory);
    }
    
    /**
     * @param string $directory the directory to slash at the end
     *
     * @return string
     */
    public function trailingSlashIt($directory): string {
        if (substr($directory, -1) !== $this->separator) {
            $directory .= $this->separator;
        }
        return  $directory;
    }
    
    public function getSeparator(): string {
        return $this->separator;
    }
}
