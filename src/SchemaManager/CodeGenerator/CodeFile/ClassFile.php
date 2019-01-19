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
     * This array is a map that stores constants defined in a file in a key value manner [constantName] => value
     *
     * @var array
     */
    protected $constants;

    /**
     * ClassFile constructor.
     *
     * @param $writeDir
     * @param $fileName
     *
     * @throws \Exception
     */
    public function __construct($writeDir, $fileName)
    {
        parent::__construct($writeDir, $fileName);
        $this->baseClass  = '';
        $this->interfaces = [];
        $this->traits     = [];
        $this->constants  = [];
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
     * @param string          $name
     * @param string|int|bool $value
     */
    public function addConstant($name, $value)
    {
        $this->constants[$name] = $value;
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

    /**
     * @return string
     */
    protected function generateConstants()
    {
        $string = '';
        if (!empty($this->constants)) {
            $string .= PHP_EOL;
            foreach ($this->constants as $name => $value) {
                if (is_string($value)) {
                    $value = "'$value'";
                }
                $string .= "const $name = $value;\n";
            }
        }

        return $string;
    }
}