<?php

namespace DeSmart\ADR\Fractal;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class JsonApiTransformer
{

    const RESOURCE_ITEM = 'item';
    const RESOURCE_COLLECTION = 'collection';

    /**
     * @var Manager
     */
    private $manager;

    /**
     * @var ResourceFactory
     */
    private $resourceFactory;

    /**
     * @var array
     */
    private $meta = [];

    public function __construct(Manager $manager, ResourceFactory $resourceFactory, Request $request)
    {
        $this->manager = $manager;
        $this->resourceFactory = $resourceFactory;

        if (true === $request->has('include')) {
            $this->manager->parseIncludes(
                $request->get('include')
            );
        }
    }

    /**
     * @param $item
     * @return array
     */
    public function transformItem($item): array
    {
        $resource = $this->createResource(static::RESOURCE_ITEM, $item);

        return $this->manager->createData($resource)
            ->toArray();
    }

    /**
     * @param array $collection
     * @param LengthAwarePaginator $paginator
     * @return array
     */
    public function transformCollection(array $collection, LengthAwarePaginator $paginator = null): array
    {
        $resource = $this->createResource(static::RESOURCE_COLLECTION, $collection);

        if (false === is_null($paginator) && 0 < $paginator->total()) {
            $resource->setPaginator(new IlluminatePaginatorAdapter($paginator));
        }

        return $this->manager->createData($resource)
            ->toArray();
    }

    /**
     * @param string $type
     * @param $source
     * @return mixed
     */
    protected function createResource(string $type, $source)
    {
        $resourceCommand = 'create' . ucfirst($type);

        $resource = $this->resourceFactory->$resourceCommand($source);

        if (false === empty($this->meta)) {
            $resource->setMeta($this->meta);
        }

        return $resource;
    }

    /**
     * @param array $meta
     * @return $this
     */
    public function setMeta(array $meta)
    {
        $this->meta = $meta;

        return $this;
    }
}
