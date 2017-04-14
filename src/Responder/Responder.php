<?php

namespace DeSmart\ADR\Responder;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use DeSmart\ADR\Collection\PaginatedCollection;
use DeSmart\ADR\Fractal\JsonApiTransformer;

/**
 * Generic action responder.
 */
class Responder
{

    /**
     * @var EntityInterface
     */
    protected $item;

    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var int
     */
    protected $statusCode = 200;

    /**
     * @var JsonApiTransformer
     */
    protected $transformer;

    /**
     * Result paginator.
     *
     * @var LengthAwarePaginator
     */
    protected $paginator;

    /**
     * @var Request
     */
    private $request;

    public function __construct(JsonApiTransformer $transformer, Request $request)
    {
        $this->transformer = $transformer;
        $this->request = $request;
    }

    /**
     * Transforms data into an array.
     *
     * @return array
     */
    protected function toArray(): array
    {
        if (null !== $this->item) {
            return $this->transformer->transformItem($this->item);
        }

        return $this->transformer->transformCollection($this->collection, $this->paginator);
    }

    /**
     * Build the response and return it.
     *
     * @return Response
     */
    public function respond(): Response
    {
        return new Response($this->toArray(), $this->statusCode, [
            'Content-type' => 'application/json',
        ]);
    }

    /**
     * Set the HTTP status code of the response.
     *
     * @param int $statusCode
     * @return $this
     */
    public function setStatusCode(int $statusCode)
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * Seta meta data in the transformer.
     *
     * @param array $meta
     * @return $this
     */
    public function setMetaData(array $meta)
    {
        $this->transformer->setMeta($meta);

        return $this;
    }

    /**
     * Payload setter.
     *
     * @param $payload
     * @param array $includes
     * @return $this
     */
    public function with($payload, array $includes = [])
    {
        if (false === empty($includes)) {
            $this->transformer->include($includes);
        }

        if (true === $payload instanceof PaginatedCollection) {
            $this->handlePaginatedCollection($payload);
        }
        else if (true === $payload instanceof Collection) {
            $this->collection = $payload->toArray();
        }
        else if (true === is_array($payload)) {
            $this->collection = $payload;
        }
        else {
            $this->item = $payload;
        }

        return $this;
    }

    /**
     * Set up a paginated collection.
     *
     * @param Collection $payload
     */
    protected function handlePaginatedCollection(Collection $payload)
    {
        $queryParams = array_diff_key($this->request->all(), array_flip(['page']));

        $this->collection = $payload->toArray();
        $this->paginator = $payload->getPaginator();
        $this->paginator->appends($queryParams);
    }
}
