<?php

namespace tests\DeSmart\ADR\Responder {

    use Illuminate\Contracts\Pagination\LengthAwarePaginator;
    use Illuminate\Http\Request;
    use Illuminate\Http\Response;
    use Illuminate\Support\Collection;
    use Bar\Domain\Foo\Entity\FooUser;
    use DeSmart\ADR\Collections\PaginatedCollection;
    use DeSmart\ADR\Fractal\JsonApiTransformer;
    use DeSmart\ADR\Responders\Responder;

    class ResponderTest extends \PHPUnit_Framework_TestCase
    {

        /**
         * @var JsonApiTransformer
         */
        protected $transformer;

        /**
         * @var Request
         */
        protected $request;

        public function setUp()
        {
            $this->transformer = $this->prophesize(JsonApiTransformer::class);
            $this->request = $this->prophesize(Request::class);

            parent::setUp();
        }

        protected function create_responder()
        {
            return new Responder(
                $this->transformer->reveal(),
                $this->request->reveal()
            );
        }

        /**
         * @test
         */
        function it_creates_a_response_for_item()
        {
            $exampleData = new FooUser;

            $this->transformer->transformItem($exampleData)->willReturn($expected = ['user']);

            $responder = $this->create_responder()
                ->with($exampleData);

            $result = $responder->respond();

            $expectedResponse = new Response($expected, 200, [
                'Content-type' => 'application/json',
            ]);

            $this->assertEquals($expectedResponse, $result);
        }

        /**
         * @test
         */
        public function it_creates_a_response_for_collection()
        {
            $collection = [
                new FooUser,
                new FooUser,
            ];

            $this->transformer->transformCollection($collection, null)->willReturn($expected = ['user', 'user']);

            $responder = $this->create_responder()
                ->with($collection);

            $result = $responder->respond();

            $expectedResponse = new Response($expected, 200, [
                'Content-type' => 'application/json',
            ]);

            $this->assertEquals($expectedResponse, $result);
        }

        /**
         * @test
         */
        public function it_allows_collection_to_be_passed_instead_of_array()
        {
            $collection = new Collection([
                new FooUser,
                new FooUser,
            ]);

            $this->transformer->transformCollection($collection->toArray(), null)->willReturn($expected = ['user', 'user']);

            $responder = $this->create_responder()
                ->with($collection);

            $result = $responder->respond();

            $expectedResponse = new Response($expected, 200, [
                'Content-type' => 'application/json',
            ]);

            $this->assertEquals($expectedResponse, $result);
        }

        /**
         * @test
         */
        public function it_creates_a_response_for_paginated_collection()
        {

            /** @var LengthAwarePaginator $paginator */
            $paginator = $this->prophesize(LengthAwarePaginator::class);
            $paginator = $paginator->reveal();

            $collection = new PaginatedCollection([
                new FooUser,
                new FooUser,
            ]);
            $collection->setPaginator($paginator);

            $this->transformer->transformCollection($collection->toArray(), $paginator)
                ->willReturn($expected = ['user', 'user']);

            $this->request->all()
                ->willReturn([]);

            $responder = $this->create_responder()
                ->with($collection);

            $result = $responder->respond();

            $expectedResponse = new Response($expected, 200, [
                'Content-type' => 'application/json',
            ]);

            $this->assertEquals($expectedResponse, $result);
        }

        /**
         * @test
         */
        public function it_sets_meta_data()
        {
            $exampleData = new FooUser;

            $this->transformer->transformItem($exampleData)->willReturn($expected = ['user']);
            $this->transformer->setMeta($meta = ['foo' => 'bar'])->shouldBeCalled();

            $responder = $this->create_responder()
                ->with($exampleData)
                ->setMetaData($meta);

            $result = $responder->respond();

            $expectedResponse = new Response($expected, 200, [
                'Content-type' => 'application/json',
            ]);

            $this->assertEquals($expectedResponse, $result);
        }
    }
}

namespace Bar\Domain\Foo\Entity {
    class FooUser {
    }
}
