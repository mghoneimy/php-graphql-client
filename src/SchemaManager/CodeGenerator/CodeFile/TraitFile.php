<?php
/**
 * Created by PhpStorm.
 * User: mostafa
 * Date: 1/19/19
 * Time: 1:45 PM
 */

namespace GraphQL\SchemaManager\CodeGenerator\CodeFile;

/**
 * Class TraitFile
 *
 * @package GraphQL\SchemaManager\CodeGenerator\CodeFile
 */
class TraitFile extends AbstractCodeFile
{
    /**
     * This string constant stores the file structure in a format that can be used with sprintf
     *
     * @var string
     */
    const FILE_FORMAT = '<?php
%1$s%2$s
trait %3$s
{
%4$s%5$s%6$s}';

    /**
     * This string stores the name of the namespace which this class belongs to
     *
     * @var string
     */
    protected $namespace;

    /**
     * This array is a list that stores the fully qualified class names that need to be imported with "use" statements
     *
     * @var array
     */
    protected $imports;

    /**
     * This string stores the name of this file
     *
     * @var string
     */
    protected $fileName;

    /**
     * This array is a map that stores constants defined in a file in a key value manner [constantName] => value
     *
     * @var array
     */
    protected $constants;

    /**
     * This array is a map that stores that properties defined in a file in a key value manner [propertyName] => value
     *
     * @var array
     */
    protected $properties;

    /**
     * This array is a list that stores the string representations of methods in the file
     *
     * @var array
     */
    protected $methods;

    /**
     * TraitFile constructor.
     *
     * @param $writePath
     * @param $fileName
     */
    public function __construct($writePath, $fileName)
    {
        parent::__construct($writePath);
        $this->namespace  = '';
        $this->fileName   = $fileName;
        $this->imports    = [];
        $this->constants  = [];
        $this->properties = [];
        $this->methods    = [];
    }

    /**
     * @param $namespaceName
     */
    public function setNamespace($namespaceName)
    {
        $this->namespace = $namespaceName;
    }

    /**
     * @param string $fullyQualifiedName
     */
    public function addImport($fullyQualifiedName)
    {
        $this->imports[] = $fullyQualifiedName;
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
     * @param string               $name
     * @param null|string|int|bool $value
     */
    public function addProperty($name, $value = null)
    {
        $this->properties[$name] = $value;
    }

    /**
     * @param $methodString
     */
    public function addMethod($methodString)
    {
        $this->methods[] = $methodString;
    }

    /**
     * @inheritdoc
     */
    protected function generateFileContents()
    {
        $namespace  = $this->generateNamespace();
        $imports    = $this->generateImports();
        $className  = $this->fileName;
        $constants  = $this->generateConstants();
        $properties = $this->generateProperties();
        $methods    = $this->generateMethods();

        return sprintf(static::FILE_FORMAT, $namespace, $imports, $className, $constants, $properties, $methods);
    }

    /**
     * @return string
     */
    protected function generateNamespace()
    {
        $string = '';
        if (!empty($this->namespace)) {
            $string = "\nnamespace $this->namespace;\n";
        }

        return $string;
    }

    /**
     * @return string
     */
    protected function generateImports()
    {
        $string = '';
        if (!empty($this->imports)) {
            $string .= PHP_EOL;
            foreach ($this->imports as $import) {
                $string .= "use $import;\n";
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

    /**
     * @return string
     */
    protected function generateProperties()
    {
        $string = '';
        if (!empty($this->properties)) {
            $string .= PHP_EOL;
            foreach ($this->properties as $name => $value) {
                if (is_string($value)) {
                    $value = "'$value'";
                }
                if (empty($value)) {
                    $string .= "protected $name;\n";
                } else {
                    $string .= "protected $name = $value;\n";
                }
            }
        }

        return $string;
    }

    /**
     * @return string
     */
    protected function generateMethods()
    {
        $string = '';
        if (!empty($this->methods)) {
            $string .= PHP_EOL;
            foreach ($this->methods as $method) {
                $string .= $method . PHP_EOL;
            }
        }

        return $string;
    }
}