<?php

namespace DeSmart\ADR\Transformers;

use DeSmart\ADR\Transformers\Exception\TransformerNotFoundException;

/**
 * Entity transformer factory.
 */
class TransformerFactory
{

    /**
     * @param $entity
     * @return TransformerInterface
     */
    public static function getEntityTransformer($entity)
    {
        $name = static::getTransformerName($entity);

        if (0 === preg_match('/Transformer$/', $name) || false === class_exists($name)) {
            throw new TransformerNotFoundException;
        }

        return app()->make($name);
    }

    /**
     * @param $entity
     * @return string
     */
    protected static function getTransformerName($entity)
    {
        $fqn = preg_replace('/Domain/', 'WebPlugin', get_class($entity));
        $fqn = preg_replace('/Entity/', 'Transformer', $fqn);

        return $fqn . 'Transformer';
    }
}
