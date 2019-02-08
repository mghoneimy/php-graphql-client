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
        $this->validateDirectory($writeDir);

        $this->writeDir = $writeDir;
        $this->fileName = $fileName;
    }

    /**
     * @param $dirName
     *
     * @throws \Exception
     */
    private function validateDirectory($dirName)
    {
        if (!is_dir($dirName)) {
            throw new \Exception("'$dirName' is not a valid directory");
        }
        if (!is_writable($dirName)) {
            throw new \Exception("'$dirName' is not writable");
        }
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
     * @param string $fileName
     */
    public function changeFileName($fileName)
    {
        $this->fileName = $fileName;
    }

    /**
     * @return string
     */
    public function getWriteDir()
    {
        return $this->writeDir;
    }

    /**
     * @param string $writeDir
     *
     * @throws \Exception
     */
    public function changeWriteDir($writeDir)
    {
        $this->validateDirectory($writeDir);

        $this->writeDir = $writeDir;
    }

    /**
     * @return string
     */
    public function getWritePath()
    {
        return $this->writeDir . "/$this->fileName.php";
    }
}