<?php

namespace DeSmart\ADR\Model;

use Illuminate\Support\Collection;

/**
 * Trait for hydrating entities from Eloquent models.
 */
trait HydratesEntityTrait
{

    /**
     * Hydrates an entity from a model.
     *
     * @return mixed
     */
    public function toEntity()
    {
        $className = $this->getEntityName();
        $entity = new $className;
        $reflection = new \ReflectionClass($className);

        $this->mapProperties($reflection, $entity);
        $this->mapRelations($reflection, $entity);

        return $entity;
    }

    /**
     * Maps model's properties to the entity.
     *
     * @param \ReflectionClass $reflection
     * @param $entity
     */
    protected function mapProperties(\ReflectionClass $reflection, $entity)
    {
        $properties = new Collection($reflection->getProperties());

        $properties->each(function (\ReflectionProperty $property) use ($entity) {
            $propertyName = $this->toSnakeCase($property->getName());
            $property->setAccessible(true);
            $property->setValue($entity, $this->{$propertyName});
        });
    }

    /**
     * Maps model's relations to the entity.
     *
     * @param \ReflectionClass $reflection
     * @param $entity
     */
    protected function mapRelations(\ReflectionClass $reflection, $entity)
    {
        $relations = $this->getRelations();

        foreach ($relations as $propertyName => $model) {
            // hasMany relations
            // I know it's ugly and bad - to be refactored
            if (true === $model instanceof Collection) {
                $reflection = new \ReflectionClass(get_class($entity));

                $property = $reflection->getProperty($propertyName);
                $property->setAccessible(true);
                $property->setValue($entity, []);

                $relationItems = [];

                foreach ($model as $item) {
                    $relationItems[] = $item->toEntity();
                }

                $property->setValue($entity, $relationItems);

                continue;
            }

            if ('pivot' === $propertyName) {
                continue;
            }

            // hasOne relations
            $this->{$propertyName} = $model->toEntity();

            $prop = $reflection->getProperty($propertyName);
            $prop->setAccessible(true);

            $prop->setValue($entity, $model->toEntity());
        }
    }

    /**
     * Maps related items.
     *
     * @param \ReflectionClass $reflection
     * @param Collection $collection
     * @param $entity
     */
    protected function mapRelationCollection(\ReflectionClass $reflection, Collection $collection, $entity)
    {
        $relationItems = [];
        $prop = $reflection->getProperty(
            $this->getCollectionName($collection)
        );
        $prop->setAccessible(true);

        foreach ($collection as $item) {
            $relationItems[] = $item->toEntity();
        }

        $prop->setValue($entity, $relationItems);
    }

    /**
     * Returns entity name based on model's name.
     *
     * @return string
     */
    protected function getEntityName()
    {
        $fqn = preg_replace('/WebPlugin/', 'Domain', get_class($this));
        $fqn = preg_replace('/Model/', 'Entity', $fqn);
        $fqn = preg_replace('/Entity$/', '', $fqn);

        return $fqn;
    }

    /**
     * Clears mapped relation property.
     *
     * @param $entity
     * @param Collection $collection
     */
    protected function emptyRelationProperty($entity, Collection $collection)
    {
        $reflection = new \ReflectionClass(get_class($entity));

        $property = $reflection->getProperty(
            $this->getCollectionName($collection)
        );
        $property->setAccessible(true);
        $property->setValue($entity, []);
    }

    /**
     * Returns related itmes' collection name.
     *
     * @param Collection $collection
     * @param string $suffix
     * @return string
     */
    protected function getCollectionName(Collection $collection, string $suffix = 's')
    {
        return strtolower(class_basename($collection->first())) . $suffix;
    }

    /**
     * Converts camel to snake case.
     *
     * @param string $text
     * @return string
     */
    protected function toSnakeCase(string $text): string
    {
        return strtolower(preg_replace('/([A-Z])/', "_\\1", $text));
    }
}
