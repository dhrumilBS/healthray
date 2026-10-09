<?php

namespace Duplicator\Package\Create\Scan;

use Duplicator\Libs\Snap\SnapServer;

/**
 * Runs a recursive scan on a directory and finds symlinks and unreadable files
 * and returns the results as an array
 */
class ScanToolValidator
{
    /** @var string[] Paths to scan */
    public $scanPaths = [];
    /** @var int The number of files scanned */
    public $fileCount = 0;
    /** @var int The number of directories scanned*/
    public $dirCount = 0;
    /** @var int The maximum count of files before the recursive function stops */
    public $maxFiles = 1000000;
    /** @var int The maximum count of directories before the recursive function stops */
    public $maxDirs = 75000;
    /** @var bool Recursively scan the root directory provided */
    public $recursion = true;
    /** @var string[] Stores a list of symbolic link files */
    public $symLinks = [];
    /** @var string[] Stores a list of files unreadable by PHP */
    public $unreadable = [];
    /** @var string[] Stores a list of directories with UTF8 settings */
    public $nameTestDirs = [];
    /** @var string[] Stores a list of files with utf8 settings */
    public $nameTestFiles = [];
    /** @var bool If the maxFiles or maxDirs limit is reached then true */
    protected $limitReached = false;

    /**
     *  Class constructor
     */
    public function __construct()
    {
    }

    /**
     *  Run the scan
     *
     * @param string[] $scanPaths An array of paths to scan
     *
     * @return self  The scan check object with the results of the scan
     */
    public function run($scanPaths)
    {
        $this->scanPaths = $scanPaths;
        foreach ($this->scanPaths as $path) {
            $this->recursiveScan($path);
        }

        return $this;
    }

    /**
     * Start the scan process
     *
     * @param string   $dir     A valid directory path where the scan will run
     * @param string[] $results Used for recursion, do not pass in value with calling
     *
     * @return self  The scan check object with the results of the scan
     */
    private function recursiveScan($dir, &$results = [])
    {
        //Stop Recursion if Max search is reached
        if ($this->fileCount > $this->maxFiles || $this->dirCount > $this->maxDirs) {
            $this->limitReached = true;
            return $this;
        }

        $files = array_diff(@scandir($dir), ['..', '.']);

        foreach ($files as $key => $value) {
            $path = $dir . '/' . $value;

            //Files
            if (!is_dir($path)) {
                if (!is_readable($path)) {
                    $results[]          = $path;
                    $this->unreadable[] = $path;
                } elseif ($this->isLink($path)) {
                    $results[]        = $path;
                    $this->symLinks[] = $path;
                } else {
                    $name         = basename($path);
                    $invalid_test = preg_match('/(\/|\*|\?|\>|\<|\:|\\|\|)/', $name) ||
                        trim($name) == '' ||
                        (strrpos($name, '.') == strlen($name) - 1 && substr($name, -1) == '.') ||
                        preg_match('/[^\x20-\x7f]/', $name);

                    if ($invalid_test) {
                        $this->nameTestFiles[] = $path;
                    }
                }
                $this->fileCount++;
            } elseif ($value != "." && $value != "..") {
                //Dirs
                if (!$this->isLink($path) && $this->recursion) {
                    $this->recursiveScan($path, $results);
                }

                if (!is_readable($path)) {
                    $results[]          = $path;
                    $this->unreadable[] = $path;
                } elseif ($this->isLink($path)) {
                    $results[]        = $path;
                    $this->symLinks[] = $path;
                } else {
                    $invalid_test = strlen($path) > 244 || trim($path) == '' || preg_match('/[^\x20-\x7f]/', $path);

                    if ($invalid_test) {
                        $this->nameTestDirs[] = $path;
                    }
                }

                $this->dirCount++;
            }
        }

        return $this;
    }

    /**
     * Separation logic for supporting how different operating systems work
     *
     * @param string $target A valid file path
     *
     * @return bool  Is the target a sym link
     */
    private function isLink(string $target): bool
    {
        //Currently Windows does not support sym-link detection
        if (SnapServer::isWindows()) {
            return false;
        } elseif (is_link($target)) {
            return true;
        }
        return false;
    }
}
