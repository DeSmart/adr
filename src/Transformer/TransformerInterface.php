<?php

namespace DeSmart\ADR\Transformer;

interface TransformerInterface
{

    /**
     * @param $entity
     * @return array
     */
    public function transform($entity);
}
