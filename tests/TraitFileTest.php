<?php

use GraphQL\SchemaManager\CodeGenerator\CodeFile\TraitFile;

/**
 * Created by PhpStorm.
 * User: mostafa
 * Date: 1/29/19
 * Time: 12:06 AM
 */

class TraitFileTest extends CodeFileTestCase
{
    /**
     * @throws Exception
     */
    public function testWritePathGetter()
    {
        $testDir  = static::getGeneratedFilesDir();
        $fileName = 'SomeName';
        $trait    = new TraitFile($testDir, $fileName);

        $this->assertEquals("$testDir/$fileName.php", $trait->getWritePath());
    }

    /**
     * Testing scenario when trying to write to a non-existent dir
     *
     * @throws Exception
     */
    public function testInvalidWriteDir()
    {
        $testsDir = static::getGeneratedFilesDir();
        $this->expectException(\Exception::class);
        (new TraitFile($testsDir . '/invalid', 'TraitTester'))->writeFile();
    }

    /**
     * Testing scenario when trying to write to a non-writable dir
     *
     * @throws Exception
     */
    public function testUnwritableDir()
    {
        $testDir = static::getGeneratedFilesDir() . '/unwritable';
        mkdir($testDir);
        chmod($testDir, 0444);
        $this->expectException(\Exception::class);
        (new TraitFile($testDir, 'TesterTrait'))->writeFile();
    }

    /**
     *
     * @throws Exception
     */
    public function testFileWritingWorks()
    {
        $trait = new TraitFile(static::getGeneratedFilesDir(), 'TraitTester');
        $trait->writeFile();

        $this->assertFileExists($trait->getWritePath());
    }

    /**
     * Happy scenario test, create empty trait with just name and write it to file system
     *
     * @throws Exception
     *
     * @depends testFileWritingWorks
     */
    public function testEmptyTrait()
    {
        $fileName = 'EmptyTrait';
        $trait = new TraitFile(static::getGeneratedFilesDir(), $fileName);
        $trait->writeFile();

        $this->assertFileEquals(static::getExpectedFilesDir() . "/$fileName.php", $trait->getWritePath());
    }

    /**
     * @throws Exception
     *
     * @depends testEmptyTrait
     */
    public function testTraitWithNamespace()
    {
        $fileName = 'TraitWithNamespace';
        $trait = new TraitFile(static::getGeneratedFilesDir(), $fileName);
        $trait->setNamespace("GraphQL\Test");
        $trait->writeFile();

        $this->assertFileEquals(static::getExpectedFilesDir() . "/$fileName.php", $trait->getWritePath());
    }

    /**
     * @throws Exception
     *
     * @depends testEmptyTrait
     */
    public function testTraitWithEmptyNamespace()
    {
        $fileName = 'EmptyTrait';
        $trait = new TraitFile(static::getGeneratedFilesDir(), $fileName);
        $trait->setNamespace('');
        $trait->writeFile();

        $this->assertFileEquals(static::getExpectedFilesDir() . "/$fileName.php", $trait->getWritePath());
    }

    /**
     * @throws Exception
     *
     * @depends testEmptyTrait
     */
    public function testTraitWithImports()
    {
        $fileName = 'TraitWithImports';
        $trait = new TraitFile(static::getGeneratedFilesDir(), $fileName);
        $trait->addImport("GraphQL\Query");
        $trait->addImport("GraphQL\Client");
        $trait->writeFile();

        $this->assertFileEquals(static::getExpectedFilesDir() . "/$fileName.php", $trait->getWritePath());
    }

    /**
     * @throws Exception
     *
     * @depends testEmptyTrait
     */
    public function testTraitWithEmptyImport()
    {
        $fileName = 'EmptyTrait';
        $trait = new TraitFile(static::getGeneratedFilesDir(), $fileName);
        $trait->addImport("");
        $trait->writeFile();

        $this->assertFileEquals(static::getExpectedFilesDir() . "/$fileName.php", $trait->getWritePath());
    }

    /**
     * @throws Exception
     *
     * @depends testEmptyTrait
     */
    public function testTraitWithProperties()
    {
        $fileName = 'TraitWithProperties';
        $trait = new TraitFile(static::getGeneratedFilesDir(), $fileName);
        $trait->addProperty('property1');
        $trait->addProperty('propertyTwo');
        $trait->addProperty('property_three');
        $trait->writeFile();

        $this->assertFileEquals(static::getExpectedFilesDir() . "/$fileName.php" , $trait->getWritePath());

        return $trait;
    }

    /**
     * @throws Exception
     *
     * @depends testTraitWithProperties
     */
    public function testTraitWithEmptyProperty()
    {
        $fileName = 'EmptyTrait';
        $trait = new TraitFile(static::getGeneratedFilesDir(), $fileName);
        $trait->addProperty('');
        $trait->writeFile();

        $this->assertFileEquals(static::getExpectedFilesDir() . "/$fileName.php" , $trait->getWritePath());
    }

    /**
     * @param TraitFile $trait
     *
     * @depends testTraitWithProperties
     */
    public function testTraitWithDuplicateProperties(TraitFile $trait)
    {
        // Adding the same property again
        $trait->addProperty('property1');

        $fileName = $trait->getFileName();
        $this->assertFileEquals(static::getExpectedFilesDir() . "/$fileName.php" , $trait->getWritePath());
    }

    /**
     * @throws Exception
     *
     * @depends testTraitWithProperties
     */
    public function testTraitWithPropertiesAndValues()
    {
        $fileName = 'TraitWithPropertiesAndValues';
        $trait = new TraitFile(static::getGeneratedFilesDir(), $fileName);
        $trait->addProperty('propertyOne', null);
        $trait->addProperty('propertyTwo', 2);
        $trait->addProperty('propertyThree', 'three');
        $trait->addProperty('propertyFour', false);
        $trait->addProperty('propertyFive', true);
        $trait->writeFile();

        $this->assertFileEquals(static::getExpectedFilesDir() . "/$fileName.php" , $trait->getWritePath());
    }
}