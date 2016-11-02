<?php

namespace DeSmart\ADR\Transformers;

interface TransformerInterface
{

    /**
     * @param $entity
     * @return array
     */
    public function transform($entity);
}
