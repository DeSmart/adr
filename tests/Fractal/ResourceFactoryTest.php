<?php

namespace tests\DeSmart\ADR\Fractal {

    use Signapps\Domain\Foo\Entity\Foo;
    use DeSmart\ADR\Fractal\ResourceFactory;
    use League\Fractal\Resource\Collection;
    use League\Fractal\Resource\Item;
    use League\Fractal\Resource\NullResource;

    class ResourceFactoryTest extends \PHPUnit_Framework_TestCase
    {
        protected function create_factory()
        {
            return new ResourceFactory;
        }

        /**
         * @test
         */
        public function it_creates_collection()
        {
            $factory = $this->create_factory();

            $result = $factory->createCollection([
                new Foo,
                new Foo,
            ]);

            $this->assertInstanceOf(Collection::class, $result);
            $this->assertInstanceOf(Foo::class, $result->getData()[0]);
            $this->assertCount(2, $result->getData());
        }

        /**
         * @test
         */
        public function it_returns_null_resource_on_empty_array()
        {
            $factory = $this->create_factory();

            $result = $factory->createCollection([]);

            $this->assertInstanceOf(NullResource::class, $result);
        }

        /**
         * @test
         */
        public function it_returns_item()
        {
            $factory = $this->create_factory();

            $result = $factory->createItem(new Foo);

            $this->assertInstanceOf(Item::class, $result);
            $this->assertInstanceOf(Foo::class, $result->getData());
        }

        /**
         * @test
         */
        public function it_returns_null_resource_on_null_entity()
        {
            $factory = $this->create_factory();

            $result = $factory->createItem(null);

            $this->assertInstanceOf(NullResource::class, $result);
        }
    }
}

namespace Signapps\Domain\Foo\Entity {
    class Foo
    {
    }
}

namespace Signapps\WebPlugin\Foo\Transformer {

    use League\Fractal\TransformerAbstract;

    class FooTransformer extends TransformerAbstract
    {
    }
}
