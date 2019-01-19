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
     * @var string
     */
    private $writePath;

    /**
     * AbstractCodeFile constructor.
     *
     * @param $writePath
     */
    public function __construct($writePath)
    {
        $this->writePath = $writePath;
    }

    /**
     * @inheritdoc
     */
    public final function writeFile()
    {
        $fileContents = $this->generateFileContents();

        // TODO: Write file contents to file
    }

    /**
     * This method generates and returns the file contents from class properties
     *
     * @return string
     */
    protected abstract function generateFileContents();
}