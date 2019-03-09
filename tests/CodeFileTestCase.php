<?php

namespace GraphQL\Tests;

use PHPUnit\Framework\TestCase;

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
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        mkdir(static::getGeneratedFilesDir());
    }

    /**
     * Remove directory created during running this class' tests
     */
    public static function tearDownAfterClass(): void
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