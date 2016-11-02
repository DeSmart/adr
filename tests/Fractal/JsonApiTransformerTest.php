<?php

namespace tests\DeSmart\ADR\Fractal {

    use Illuminate\Http\Request;
    use League\Fractal\Manager;
    use League\Fractal\Resource\ResourceAbstract;
    use League\Fractal\Scope;
    use Signapps\Domain\Foo\Entity\BarUser;
    use DeSmart\ADR\Fractal\JsonApiTransformer;
    use DeSmart\ADR\Fractal\ResourceFactory;

    class JsonApiTransformerTest extends \PHPUnit_Framework_TestCase
    {

        /**
         * @test
         */
        public function it_transforms_item()
        {
            $manager = $this->prophesize(Manager::class);
            $factory = $this->prophesize(ResourceFactory::class);

            $factory->createItem($item = new BarUser)->willReturn($resource = $this->prophesize(ResourceAbstract::class));
            $manager->createData($resource)->willReturn($scope = $this->prophesize(Scope::class));
            $scope->toArray()->willReturn($expected = ['user']);

            $jsonApiTransformer = new JsonApiTransformer($manager->reveal(), $factory->reveal(), new Request);
            $this->assertEquals($expected, $jsonApiTransformer->transformItem($item));
        }

        /**
         * @test
         */
        public function it_transforms_collection()
        {
            $collection = [new BarUser, new BarUser];
            $manager = $this->prophesize(Manager::class);
            $factory = $this->prophesize(ResourceFactory::class);

            $factory->createCollection($collection)->willReturn($resource = $this->prophesize(ResourceAbstract::class));
            $manager->createData($resource)->willReturn($scope = $this->prophesize(Scope::class));
            $scope->toArray()->willReturn($expected = ['user', 'user']);

            $jsonApiTransformer = new JsonApiTransformer($manager->reveal(), $factory->reveal(), new Request);
            $this->assertEquals($expected, $jsonApiTransformer->transformCollection($collection));
        }

        /**
         * @test
         */
        public function it_sets_meta_data()
        {
            $manager = $this->prophesize(Manager::class);
            $factory = $this->prophesize(ResourceFactory::class);

            $factory->createItem($item = new BarUser)->willReturn($resource = $this->prophesize(ResourceAbstract::class));
            $resource->setMeta($meta = ['foo' => 'bar'])->shouldBeCalled();
            $manager->createData($resource)->willReturn($scope = $this->prophesize(Scope::class));
            $scope->toArray()->willReturn($expected = ['user']);

            $jsonApiTransformer = new JsonApiTransformer($manager->reveal(), $factory->reveal(), new Request);
            $jsonApiTransformer->setMeta($meta);

            $this->assertEquals($expected, $jsonApiTransformer->transformItem($item));
        }
    }
}

namespace Signapps\Domain\Foo\Entity {
    class BarUser {
    }
}