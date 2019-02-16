<?php

use PHPUnit\Framework\TestCase;

/**
 * Created by PhpStorm.
 * User: mostafa
 * Date: 2/2/19
 * Time: 6:18 PM
 */

/**
 * Class CodeFileTestCase
 */
abstract class CodeFileTestCase extends TestCase
{
    /**
     * @return string
     */
    protected static function getGeneratedFilesDir()
    {
        return dirname(__FILE__) . '/files_generated';
    }

    /**
     * @return string
     */
    protected static function getExpectedFilesDir()
    {
        return dirname(__FILE__) . '/files_expected';
    }

    /**
     * Create directory before executing the tests
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        mkdir(static::getGeneratedFilesDir());
    }

    /**
     * Remove directory created during running this class' tests
     */
    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();
        static::removeDirRecursive(static::getGeneratedFilesDir());
    }

    /**
     * @param $dirName
     */
    private static function removeDirRecursive($dirName)
    {
        foreach (scandir($dirName) as $fileName) {
            if ($fileName !== '.' && $fileName !== '..') {
                $filePath = "$dirName/$fileName";
                if (is_dir($filePath)) {
                    static::removeDirRecursive($filePath);
                } else {
                    unlink($filePath);
                }
            }
        }
        rmdir($dirName);
    }
}