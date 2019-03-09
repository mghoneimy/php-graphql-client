<?php

namespace GraphQL\SchemaGenerator\CodeGenerator\CodeFile;

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
    protected const FILE_FORMAT = '<?php
%1$s%2$s
class %3$s
{%4$s%5$s%6$s%7$s}';

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
     * @param string $writeDir
     * @param string $fileName
     */
    public function __construct(string $writeDir, string $fileName)
    {
        parent::__construct($writeDir, $fileName);
        $this->baseClass  = '';
        $this->interfaces = [];
        $this->traits     = [];
        $this->constants  = [];
    }

    /**
     * @param string $className
     */
    public function extendsClass(string $className)
    {
        if (!empty($className)) {
            $this->baseClass = $className;
        }
    }

    /**
     * @param string $interfaceName
     */
    public function implementsInterface(string $interfaceName)
    {
        if (!empty($interfaceName)) {
            $this->interfaces[$interfaceName] = null;
        }
    }

    /**
     * @param string $traitName
     */
    public function addTrait(string $traitName)
    {
        if (!empty($traitName)) {
            $this->traits[$traitName] = null;
        }
    }

    /**
     * @param string          $name
     * @param string|int|bool $value
     */
    public function addConstant(string $name, $value)
    {
        if (!empty($name)) {
            $this->constants[$name] = $value;
        }
    }

    /**
     * @inheritdoc
     */
    protected function generateFileContents(): string
    {
        $className  = $this->generateClassName();

        // Generate class headers
        $namespace = $this->generateNamespace();
        if (!empty($namespace)) $namespace = PHP_EOL . $namespace;
        $imports = $this->generateImports();
        if (!empty($imports)) $imports = PHP_EOL . $imports;

        // Generate class body
        $traits = $this->generateTraits();
        if (!empty($traits)) $traits = PHP_EOL . $traits;
        $constants = $this->generateConstants();
        if (!empty($constants)) $constants = PHP_EOL . $constants;
        $properties = $this->generateProperties();
        if (!empty($properties)) $properties = PHP_EOL . $properties;
        $methods = $this->generateMethods();

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
    protected function generateClassName(): string
    {
        $string = $this->fileName;
        if (!empty($this->baseClass)) {
            $string .= " extends $this->baseClass";
        }

        // Append interfaces list
        if (!empty($this->interfaces)) {
            $string .= ' implements ';
            $first  = true;
            foreach ($this->interfaces as $interfaceName => $nothing) {
                if (!$first) {
                    $string .= ', ';
                }
                $string .= $interfaceName;
                $first  = false;
            }
        }

        return $string;
    }

    /**
     * @return string
     */
    protected function generateTraits(): string
    {
        $string = '';
        if (!empty($this->traits)) {
            foreach ($this->traits as $traitName => $nothing) {
                $string .= "    use $traitName;\n";
            }
        }

        return $string;
    }

    /**
     * @return string
     */
    protected function generateConstants(): string
    {
        $string = '';
        if (!empty($this->constants)) {
            foreach ($this->constants as $name => $value) {
                $value = $this->serializeParameterValue($value);
                $string .= "    const $name = $value;\n";
            }
        }

        return $string;
    }
}
