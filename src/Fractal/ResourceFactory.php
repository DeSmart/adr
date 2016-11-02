<?php

namespace DeSmart\ADR\Fractal;

use DeSmart\ADR\Transformers\TransformerFactory;
use DeSmart\ADR\Transformers\TransformerInterface;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\NullResource;
use League\Fractal\Resource\ResourceAbstract;

/**
 * Factory responsible for creating Fractal resources for provided data.
 */
class ResourceFactory
{

    /**
     * Creates a resource collection from array.
     *
     * @param array $entities
     * @return ResourceAbstract
     */
    public function createCollection(array $entities): ResourceAbstract
    {
        if (true === empty($entities)) {
            return new NullResource;
        }

        return new Collection(
            $entities,
            $this->getTransformer($entities[0]),
            class_basename($entities[0])
        );
    }

    /**
     * Creates a resource item from entity.
     *
     * @param $entity
     * @return ResourceAbstract
     */
    public function createItem($entity = null): ResourceAbstract
    {
        if (null === $entity) {
            return new NullResource;
        }

        return new Item(
            $entity,
            $this->getTransformer($entity),
            class_basename($entity)
        );
    }

    /**
     * Creates a transformer for given entity.
     *
     * @param $entity
     * @return TransformerInterface
     */
    protected function getTransformer($entity)
    {
        return TransformerFactory::getEntityTransformer($entity);
    }
}