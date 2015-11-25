<?php declare(strict_types=1);
namespace Phan\CodeBase;

use \Phan\Language\Element\Method;
use \Phan\Language\FQSEN;
use \Phan\Language\FQSEN\FullyQualifiedClassName;
use \Phan\Language\FQSEN\FullyQualifiedFunctionName;
use \Phan\Language\FQSEN\FullyQualifiedMethodName;

trait MethodMap {

    /**
     * @var Method[][]
     * A map from FQSEN to name to a method
     */
    protected $method_map = [];

    /**
     * @return Method[][]
     * A map from FQSEN to name to method
     */
    public function getMethodMap() : array {
        return $this->method_map;
    }

    /**
     * @return Method[]
     * A map from name to method
     */
    public function getMethodMapForScope(
        FullyQualifiedClassName $fqsen
    ) {
        if (empty($this->method_map[(string)$fqsen])) {
            return [];
        }

        return $this->method_map[(string)$fqsen];
    }

    /**
     * @param Method[][] $method_map
     * A map from FQSEN to Method
     *
     * @return null
     */
    public function setMethodMap(array $method_map) {
        $this->method_map = $method_map;
    }

    /**
     * @param FullyQualifiedMethodName|FullyQualifiedFunctionName $fqsen
     *
     * @return bool
     */
    public function hasMethod($fqsen) : bool {
        if ($fqsen instanceof FullyQualifiedMethodName) {
            return $this->hasMethodWithMethodFQSEN($fqsen);
        } else {
            return $this->hasMethodWithFunctionFQSEN($fqsen);
        }
    }

    /**
     * @param FullyQualifiedMethodName $fqsen
     *
     * @return bool
     */
    private function hasMethodWithMethodFQSEN(
        FullyQualifiedMethodName $fqsen
    ) : bool {
        return !empty($this->method_map
            [(string)$fqsen->getFullyQualifiedClassName()]
            [$fqsen->getNameWithAlternateId()]
        );
    }

    /**
     * @param FullyQualifiedFunctionName $fqsen
     *
     * @return bool
     */
    private function hasMethodWithFunctionFQSEN(
        FullyQualifiedFunctionName $fqsen
    ) : bool {
        return !empty($this->method_map
            [$fqsen->getNamespace()]
            [$fqsen->getNameWithAlternateId()]);
    }

    /**
     * @param FullyQualifiedMethodName|FullyQualifiedFunctionName $fqsen
     *
     * @return Method
     * Get the method with the given FQSEN
     */
    public function getMethod($fqsen) : Method {
        if ($fqsen instanceof FullyQualifiedMethodName) {
            return $this->getMethodByMethodFQSEN($fqsen);
        } else {
            return $this->getMethodByFunctionFQSEN($fqsen);
        }
    }

    /**
     * @param FullyQualifiedMethodName $fqsen
     *
     * @return Method
     * Get the method with the given FQSEN
     */
    private function getMethodByMethodFQSEN(
        FullyQualifiedMethodName $fqsen
    ) : Method {
        return $this->method_map
            [(string)$fqsen->getFullyQualifiedClassName()]
            [$fqsen->getNameWithAlternateId()];
    }

    /**
     * @param FullyQualifiedFunctionName $fqsen
     *
     * @return Method
     * Get the method with the given FQSEN
     */
    private function getMethodByFunctionFQSEN(
        FullyQualifiedFunctionName $fqsen
    ) : Method {
        return $this->method_map
            [$fqsen->getNamespace()]
            [$fqsen->getNameWithAlternateId()];
    }

    /**
     * @param Method $method
     * Any method
     *
     * @return null
     */
    public function addMethod(Method $method) {
        if ($method->getFQSEN() instanceof FullyQualifiedMethodName) {
            $this->addMethodWithMethodFQSEN(
                $method,
                $method->getFQSEN()
            );
        } else {
            assert($method->getFQSEN() instanceof FullyQualifiedFunctionName,
                "Method given must have FQSEN of type FullyQualifiedMethodName");
            $this->addMethodWithFunctionFQSEN(
                $method,
                $method->getFQSEN()
            );
        }
    }

    /**
     * @param Method $method
     * Any method
     *
     * @param FullyQualifiedMethodName $fqsen
     * The FQSEN for the method
     *
     * @return null
     */
    private function addMethodWithMethodFQSEN(
        Method $method,
        FullyQualifiedMethodName $fqsen
    ) {
        $this->addMethodInScope(
            $method,
            $fqsen->getFullyQualifiedClassName()
        );
    }

    /**
     * @param Method $method
     * Any method
     *
     * @param FullyQualifiedFunctionName $fqsen
     * The FQSEN for the method
     *
     * @return null
     */
    private function addMethodWithFunctionFQSEN(
        Method $method,
        FullyQualifiedFunctionName $fqsen
    ) {
        $this->method_map
            [$fqsen->getNamespace()]
            [$fqsen->getNameWithAlternateId()]
            = $method;
    }

    /**
     * @param Method $method
     * Any method
     *
     * @param FQSEN $fqsen
     * The FQSEN to index the method by
     *
     * @return null
     */
    public function addMethodInScope(
        Method $method,
        FullyQualifiedClassName $fqsen
    ) {
        $name = $method->getFQSEN()->getNameWithAlternateId();
        $this->method_map[(string)$fqsen][$name] = $method;
    }

}
