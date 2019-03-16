<?php

namespace GraphQL\SchemaGenerator\CodeGenerator\CodeFile;

use RuntimeException;

/**
 * Class AbstractCodeFile
 *
 * @package GraphQL\SchemaManager\CodeGenerator\CodeFile
 */
abstract class AbstractCodeFile implements CodeFileInterface
{
    /**
     * This string constant stores the file structure in a format that can be used with sprintf
     *
     * @var string
     */
    protected const FILE_FORMAT = '<?php
';
    /**
     * This string stores the name of this file
     *
     * @var string
     */
    protected $fileName;

    /**
     * @var string
     */
    private $writeDir;

    /**
     * AbstractCodeFile constructor.
     *
     * @param string $writeDir
     * @param string $fileName
     */
    public function __construct(string $writeDir, string $fileName)
    {
        $this->validateDirectory($writeDir);

        $this->writeDir = $writeDir;
        $this->fileName = $fileName;
    }

    /**
     * @param string $dirName
     *
     * @return bool
     */
    private function validateDirectory(string $dirName): bool
    {
        if (!is_dir($dirName)) {
            throw new RuntimeException("$dirName is not a valid directory");
        }
        if (!is_writable($dirName)) {
            throw new RuntimeException("$dirName is not writable");
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public final function writeFile(): bool
    {
        $fileContents = $this->generateFileContents();

        $filePath = $this->writeDir;
        if (substr($filePath, -1) !== '/') {
            $filePath .= '/';
        }
        $filePath .= $this->fileName . '.php';

        return $this->writeFileToPath($fileContents, $filePath);
    }

    /**
     * This method generates and returns the file contents from class properties
     *
     * @return string
     */
    protected abstract function generateFileContents(): string;

    /**
     * @param string $fileContents
     * @param string $filePath
     *
     * @return bool
     */
    private function writeFileToPath(string $fileContents, string $filePath): bool
    {
        return file_put_contents($filePath, $fileContents) !== false;
    }

    /**
     * @codeCoverageIgnore
     *
     * @return string
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }

    /**
     * @param string $fileName
     */
    public function changeFileName(string $fileName)
    {
        $this->fileName = $fileName;
    }

    /**
     * @codeCoverageIgnore
     *
     * @return string
     */
    public function getWriteDir(): string
    {
        return $this->writeDir;
    }

    /**
     * @param string $writeDir
     */
    public function changeWriteDir(string $writeDir)
    {
        $this->validateDirectory($writeDir);

        $this->writeDir = $writeDir;
    }

    /**
     * @return string
     */
    public function getWritePath(): string
    {
        return $this->writeDir . "/$this->fileName.php";
    }
}