<?php
/**
 * Created by PhpStorm.
 * User: mostafa
 * Date: 1/19/19
 * Time: 1:45 PM
 */

namespace GraphQL\SchemaManager\CodeGenerator\CodeFile;

/**
 * Class ClassFile
 *
 * @package GraphQL\SchemaManager\CodeGenerator\CodeFile
 */
class ClassFile extends TraitFile
{
    /**
     * This string constant stores the file structure in a format that can be used with sprintf
     *
     * @var string
     */
    const FILE_FORMAT = '<?php
%1$s%2$s
class %3$s
{
%4$s%5$s%6$s%7$s}';

    /**
     * The name of the base class extended by this class
     *
     * @var string
     */
    protected $baseClass;

    /**
     * The list of interfaces implemented by this class
     *
     * @var array
     */
    protected $interfaces;

    /**
     * The list of traits used by this class
     *
     * @var array
     */
    protected $traits;

    /**
     * ClassFile constructor.
     *
     * @param $writePath
     * @param $fileName
     */
    public function __construct($writePath, $fileName)
    {
        parent::__construct($writePath, $fileName);
        $this->baseClass  = '';
        $this->interfaces = [];
        $this->traits     = [];
    }

    /**
     * @param $className
     */
    public function extendsClass($className)
    {
        $this->baseClass = $className;
    }

    /**
     * @param $interfaceName
     */
    public function implementsInterface($interfaceName)
    {
        $this->interfaces[] = $interfaceName;
    }

    /**
     * @param $traitName
     */
    public function addTrait($traitName)
    {
        $this->traits[] = $traitName;
    }

    /**
     * @inheritdoc
     */
    protected function generateFileContents()
    {
        $namespace  = $this->generateNamespace();
        $imports    = $this->generateImports();
        $className  = $this->generateClassName();
        $traits     = $this->generateTraits();
        $constants  = $this->generateConstants();
        $properties = $this->generateProperties();
        $methods    = $this->generateMethods();

        return sprintf(
            static::FILE_FORMAT,
            $namespace,
            $imports,
            $className,
            $traits,
            $constants,
            $properties,
            $methods
        );
    }

    /**
     * @return string
     */
    protected function generateClassName()
    {
        $string = $this->fileName;
        if (!empty($this->baseClass)) {
            $string .= " extends $this->baseClass";
        }

        // Append interfaces list
        if (!empty($this->interfaces)) {
            $string .= ' implements ';
            $first  = true;
            foreach ($this->interfaces as $interface) {
                if (!$first) {
                    $string .= ', ';
                }
                $string .= $interface;
                $first  = false;
            }
        }

        return $string;
    }

    /**
     * @return string
     */
    protected function generateTraits()
    {
        $string = '';
        if (!empty($this->traits)) {
            $string .= PHP_EOL;
            foreach ($this->traits as $trait) {
                $string .= "use $trait;\n";
            }
        }

        return $string;
    }
}