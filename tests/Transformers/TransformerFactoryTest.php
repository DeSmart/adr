<?php

namespace tests\DeSmart\ADR\Transformers {

    use Foo\Bar\Domain\Baz\Entity\MadeUpName;
    use Foo\Bar\Domain\Baz\Entity\NoTransformer;
    use Foo\Bar\WebPlugin\Baz\Transformer\MadeUpNameTransformer;
    use DeSmart\ADR\Transformers\Exception\TransformerNotFoundException;
    use DeSmart\ADR\Transformers\TransformerFactory;

    class TransformerFactoryTest extends \PHPUnit_Framework_TestCase
    {

        /**
         * @test
         */
        public function it_creates_proper_transformer()
        {
            $entity = new MadeUpName();

            $this->assertInstanceOf(MadeUpNameTransformer::class, TransformerFactory::getEntityTransformer($entity));
        }

        /**
         * @test
         */
        public function it_throws_exception_if_transformer_not_found()
        {
            $this->expectException(TransformerNotFoundException::class);

            TransformerFactory::getEntityTransformer(new NoTransformer());
        }
    }
}

namespace Foo\Bar\Domain\Baz\Entity {
    class MadeUpName {
    }

    class NoTransformer {
    }
}

namespace Foo\Bar\WebPlugin\Baz\Transformer {
    class MadeUpNameTransformer {
    }
}
