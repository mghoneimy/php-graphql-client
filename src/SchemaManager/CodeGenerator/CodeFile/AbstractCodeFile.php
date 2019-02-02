<?php
/**
 * Created by PhpStorm.
 * User: mostafa
 * Date: 1/19/19
 * Time: 2:22 PM
 */

namespace GraphQL\SchemaManager\CodeGenerator\CodeFile;

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
    const FILE_FORMAT = '<?php
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
     * @param $writeDir
     * @param $fileName
     *
     * @throws \Exception
     */
    public function __construct($writeDir, $fileName)
    {
        if (!is_dir($writeDir)) {
            throw new \Exception("'$writeDir' is not a valid directory");
        }
        if (!is_writable($writeDir)) {
            throw new \Exception("'$writeDir' is not writable");
        }

        $this->writeDir = $writeDir;
        $this->fileName = $fileName;
    }

    /**
     * @inheritdoc
     */
    public final function writeFile()
    {
        $fileContents = $this->generateFileContents();

        $filePath = $this->writeDir;
        if (substr($filePath, -1) !== '/') {
            $filePath .= '/';
        }
        $filePath .= $this->fileName . '.php';

        $this->writeFileToPath($fileContents, $filePath);
    }

    /**
     * This method generates and returns the file contents from class properties
     *
     * @return string
     */
    protected abstract function generateFileContents();

    /**
     * @param $fileContents
     * @param $filePath
     */
    private function writeFileToPath($fileContents, $filePath)
    {
        file_put_contents($filePath, $fileContents);
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * @return string
     */
    public function getWriteDir()
    {
        return $this->writeDir;
    }

    public function getWritePath()
    {
        return $this->writeDir . "/$this->fileName.php";
    }
}