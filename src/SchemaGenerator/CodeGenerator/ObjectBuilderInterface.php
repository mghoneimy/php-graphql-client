<?php

namespace GraphQL\SchemaGenerator\CodeGenerator;

/**
 * Interface ObjectBuilderInterface
 *
 * @package GraphQL\SchemaGenerator\CodeGenerator
 */
interface ObjectBuilderInterface
{
    const DEFAULT_NAMESPACE = 'GraphQL\\SchemaObject';

    /**
     * @return void
     */
    public function build(): void;
}