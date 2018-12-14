<?php
declare(strict_types=1);

namespace IrRegular\Tests\Hopper\Ds\Vector;

use IrRegular\Hopper\Ds\Vector\Lazy as LazyVector;
use function IrRegular\Hopper\first;
use function IrRegular\Hopper\get;
use function IrRegular\Hopper\lmap;
use function IrRegular\Hopper\second;
use function IrRegular\Hopper\to_array;
use function IrRegular\Hopper\vector;
use IrRegular\Tests\Hopper\CollectionSetUpTrait;
use PHPUnit\Framework\TestCase;

class LazyTest extends TestCase
{
    use CollectionSetUpTrait;

    public function increment(int $x): int
    {
        return $x + 1;
    }

    public function testLazyVectorRetrievesValuesFromGenerator()
    {
        $vector = vector($this->generator(self::$array));

        $this->assertEquals(self::$array, to_array($vector));
    }

    public function testVectorCreatesLazyVectorFromGenerator()
    {
        $vector = vector($this->generator());

        $this->assertInstanceOf(LazyVector::class, $vector);
    }

    public function testMapIsLazy()
    {
        $g = $this->generator([1, 2]);
        $vector = vector($g);
        $result = $vector->lMap([$this, 'increment']);

        $this->assertEquals(2, first($result));
        $this->assertEquals(3, second($result));

        $this->assertTrue($g->valid());
    }

    public function testMapWorksOnRealisedLazyVector()
    {
        $vector = vector($this->generator([1, 2, 3]));
        $vector->count();
        $result = $vector->map([$this, 'increment']);
        $this->assertEquals([2, 3, 4], to_array($result));
    }

    public function testChecksGeneratorForMoreElements()
    {
        $vector = vector($this->generator([1, 2, 3]));
        $this->assertFalse($vector->isEmpty());

        $vector = vector($this->generator());
        $this->assertTrue($vector->isEmpty());
    }

    public function testGetRealisesOnlyUpToRequiredIndex()
    {
        $g = $this->generator([1, 2, 3]);
        $vector = vector($g);
        $this->assertEquals(2, $vector->get(1));
        $this->assertTrue($g->valid());
    }

    public function testGetCountRealisesFullGenerator()
    {
        $g = $this->generator();
        $vector = vector($g);
        $vector->count();
        $this->assertFalse($g->valid());
    }

    public function testIndexOutOfBoundsHandledCorrectly()
    {
        $indexOutOfBounds = 1;
        $defaultValue = 'default';
        $vector = vector($this->generator());

        $this->assertFalse($vector->isKey($indexOutOfBounds));
        $this->assertEquals($defaultValue, $vector->get($indexOutOfBounds, $defaultValue));
    }

    public function testCachesGeneratorResults()
    {
        $vector = vector($this->generator([1, 2]));

        // we can advance generator to the last value...
        $this->assertEquals(2, $vector->get(1));
        // ...and still be able to access the previous value
        $this->assertEquals(1, $vector->get(0));
    }

    public function testRestReturnsLazyVector()
    {
        // ...and also that vector has its own, independent generator

        $vector = vector($this->generator([1, 2]));
        $rest = $vector->rest();

        $this->assertInstanceOf(LazyVector::class, $rest);
        $this->assertEquals([2], to_array($rest));
        $this->assertEquals([1, 2], to_array($vector));
    }

    public function testDeeplyNestedVectorsHaveNoSkew()
    {
        /** @var \Generator $generator */
        $generator = (function () {
            yield from [1, 2, 3, 4];
        })();

        /** @var LazyVector $v1 */
        $v1 = lmap('\IrRegular\Hopper\identity', $generator);
        /** @var LazyVector $v2 */
        $v2 = lmap('\IrRegular\Hopper\identity', $v1);
        /** @var LazyVector $v3 */
        $v3 = lmap('\IrRegular\Hopper\identity', $v2);

        $this->assertEquals(1, get($v3, 0));
        $this->assertEquals(1, $generator->current());
        $this->assertEquals(1, $v1->getGenerator()->current());
        $this->assertEquals(1, $v2->getGenerator()->current());
        $this->assertEquals(1, $v3->getGenerator()->current());

        $this->assertEquals(2, get($v3, 1));
        $this->assertEquals(3, get($v3, 2));
        $this->assertEquals(4, get($v3, 3));

        $this->assertEquals(4, $generator->current());
        $this->assertEquals('default', get($v3, 4, 'default'));
    }
}
