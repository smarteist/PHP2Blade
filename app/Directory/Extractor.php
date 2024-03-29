<?php

namespace App\Directory;

use Exception;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class Extractor
{

    /**
     * @param $directory
     * @return array
     */
    public static function scan($directory)
    {
        /**Get all files and directories using recursive iterator.*/
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::LEAVES_ONLY
        );
        /**Add all of iterated files in array */
        $file_paths = [];
        foreach ($iterator as $path){
            if (self::endsWith(pathinfo($path, PATHINFO_BASENAME), ".php")) {
                $file_paths[] = strval($path);
            }
        }
        return $file_paths;
    }

    public static function endsWith($haystack, $needle)
    {
        $length = strlen($needle);
        if ($length == 0) {
            return false;
        }
        return (substr($haystack, -$length) === $needle);
    }

    public static function createBladeFile($file, $content)
    {
        $fs = fopen($file, "w");
        if (!is_resource($fs)) {
            return false;
        }
        fwrite($fs, $content);
        fclose($fs);
        return true;
    }

    public static function makeOutputFileDirectory($file, $inputDir, $outputDir)
    {
        $bladeFileName = str_replace($inputDir, $outputDir, $file);
        $bladeFileName = str_replace(".php", ".blade.php", $bladeFileName);
        // clear any "//" which may created accidentally.
        $bladeFileName = str_replace("//", "/", $bladeFileName);
        if (!file_exists(dirname($bladeFileName))) {
            mkdir(dirname($bladeFileName), 0777, true);
        }
        return $bladeFileName;
    }
}