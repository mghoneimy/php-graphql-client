<?php

use GraphQL\SchemaGenerator\CodeGenerator\CodeFile\AbstractCodeFile;

class AbstractCodeFileTest extends CodeFileTestCase
{
    /**
     * @var AbstractCodeFile
     */
    protected $codeFile;

    /**
     * @var string
     */
    protected $fileName;

    /**
     * @inheritdoc
     */
    protected static function getExpectedFilesDir()
    {
        return parent::getExpectedFilesDir() . '/abstract_code_files';
    }

    /**
     *
     */
    protected function setUp()
    {
        $this->fileName = 'EmptyCodeFile';
        $this->codeFile = $this->getMockForAbstractClass(
            AbstractCodeFile::class,
            [static::getGeneratedFilesDir(), $this->fileName]
        );
        $this->codeFile->method('generateFileContents')->willReturn("<?php\n");
    }

    /**
     * @testdox Test the behavior of the constructor when provided with an invalid directory
     *
     * @covers AbstractCodeFile::__construct()
     * @covers AbstractCodeFile::validateDirectory
     */
    public function testInvalidWriteDirInConstructor()
    {
        $this->expectException(\Exception::class);
        $mock = $this->getMockForAbstractClass(
            AbstractCodeFile::class,
            [static::getGeneratedFilesDir() . '/invalid', $this->fileName]
        );
        $mock->method('generateFileContents')->willReturn("<?php\n");
    }

    /**
     * @testdox Test the behavior of the constructor when provided with a non-writable directory
     *
     * @covers AbstractCodeFile::__construct()
     * @covers AbstractCodeFile::validateDirectory
     */
    public function testUnwritableDirInConstructor()
    {
        $testDir = static::getGeneratedFilesDir() . '/unwritable-constructor';
        mkdir($testDir);
        chmod($testDir, 0444);

        $this->expectException(\Exception::class);
        $mock = $this->getMockForAbstractClass(
            AbstractCodeFile::class,
            [$testDir, $this->fileName]
        );
        $mock->method('generateFileContents')->willReturn("<?php\n");
    }

    /**
     * @throws Exception
     *
     * @testdox Test the behavior of changeWriteDir method when provided with an invalid directory
     *
     * @covers AbstractCodeFile::changeWriteDir
     * @covers AbstractCodeFile::validateDirectory
     */
    public function testInvalidWriteDir()
    {
        $this->expectException(\Exception::class);
        $this->codeFile->changeWriteDir(static::getGeneratedFilesDir() . '/invalid');
    }

    /**
     * @throws Exception
     *
     * @testdox Test the behavior of changeWriteDir method when provided with a non-writable directory
     *
     * @covers AbstractCodeFile::changeWriteDir
     * @covers AbstractCodeFile::validateDirectory
     */
    public function testUnwritableDir()
    {
        $testDir = static::getGeneratedFilesDir() . '/unwritable';
        mkdir($testDir);
        chmod($testDir, 0444);

        $this->expectException(\Exception::class);
        $this->codeFile->changeWriteDir($testDir);
    }

    /**
     * @covers AbstractCodeFile::getWritePath
     * @covers AbstractCodeFile::writeFileToPath
     */
    public function testWritePathGetter()
    {
        $this->assertEquals(static::getGeneratedFilesDir() . "/$this->fileName.php", $this->codeFile->getWritePath());
    }

    /**
     * @depends testWritePathGetter
     *
     * @covers AbstractCodeFile::writeFile
     * @covers AbstractCodeFile::writeFileToPath
     * @covers AbstractCodeFile::generateFileContents
     */
    public function testFileWritingWorks()
    {
        $this->codeFile->writeFile();
        $this->assertFileEquals(static::getExpectedFilesDir() . "/$this->fileName.php", $this->codeFile->getWritePath());
    }

    /**
     * @throws Exception
     *
     * @depends testFileWritingWorks
     *
     * @covers  AbstractCodeFile::WriteFile
     */
    public function testFileWritingWorksWithTrailingSlash()
    {
        $this->fileName = 'EmptyCodeFileWithSlash';
        $this->codeFile->changeWriteDir($this->codeFile->getWriteDir() . '/');
        $this->codeFile->changeFileName($this->fileName);
        $this->codeFile->writeFile();

        $this->assertFileEquals(static::getExpectedFilesDir() . "/$this->fileName.php", $this->codeFile->getWritePath());
    }
}