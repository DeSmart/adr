<?php

namespace tests\DeSmart\ADR\Models {

    use Foo\Bar\Baz\Domain\Users\Entity\Fake;
    use Foo\Bar\Baz\WebPlugin\Users\Model\FakeModel;

    class HydratesEntityTraitTest extends \PHPUnit_Framework_TestCase
    {

        /**
         * @test
         */
        public function it_hydrates_model_to_entity()
        {
            $model = new FakeModel();

            $entity = $model->toEntity();

            $this->assertInstanceOf(Fake::class, $entity);
        }
    }
}

namespace Foo\Bar\Baz\WebPlugin\Users\Model {

    use Illuminate\Database\Eloquent\Model;
    use DeSmart\ADR\Models\HydratesEntityTrait;

    class FakeModel extends Model
    {
        use HydratesEntityTrait;
    }
}

namespace Foo\Bar\Baz\Domain\Users\Entity {
    class Fake
    {
    }
}
