<?php
declare(strict_types=1);

namespace Tests\Unit\Request\ParamConverter;

use Faker\Factory;
use Symfony\Component\HttpFoundation\Request;
use Tests\Mock\MockParamConverter;
use Xgc\Money\Money;
use Xgc\MoneyBundle\Request\ParamConverter\MoneyParamConverter;
use Xgc\MoneyBundle\Test\TestCase;

/**
 * Class MoneyParamConverterTest
 *
 * @package Tests\Unit\Request\ParamConverter
 */
class MoneyParamConverterTest extends TestCase
{
    public function testSupportsSuccess()
    {
        $paramConverter      = new MockParamConverter([]);
        $moneyParamConverter = new MoneyParamConverter();
        $paramConverter->setClass(Money::class);

        self::assertTrue($moneyParamConverter->supports($paramConverter));
    }

    public function testSupportsFails()
    {
        $paramConverter      = new MockParamConverter([]);
        $moneyParamConverter = new MoneyParamConverter();
        $paramConverter->setClass(Money::class);

        $paramConverter->setClass(Factory::create()->word);
        self::assertFalse($moneyParamConverter->supports($paramConverter));

        $paramConverter->setClass(null);
        self::assertFalse($moneyParamConverter->supports($paramConverter));
    }

    public function testApplySuccess()
    {
        $paramConverter = new MockParamConverter([]);

        $moneyParamConverter = new MoneyParamConverter();
        $request             = new Request();
        $request->attributes->add([$paramConverter->getName() => '123.23']);
        $paramConverter->setOptions(['currency' => 'EUR']);

        self::assertTrue($moneyParamConverter->apply($request, $paramConverter));
    }

    public function testApplyFailsWithoutParams()
    {
        $request             = new Request();
        $paramConverter      = new MockParamConverter([]);
        $moneyParamConverter = new MoneyParamConverter();
        $paramConverter->setClass(Money::class);

        $paramConverter->setOptions(['format' => 'm']);

        self::assertFalse($moneyParamConverter->apply($request, $paramConverter));
    }

    public function testApplyFailsWithOptional()
    {
        $request             = new Request();
        $paramConverter      = new MockParamConverter([]);
        $moneyParamConverter = new MoneyParamConverter();
        $paramConverter->setClass(Money::class);

        $paramConverter->setOptions(['format' => 'm']);
        $paramConverter->setIsOptional(true);
        $request->attributes->add([$paramConverter->getName() => null]);

        self::assertFalse($moneyParamConverter->apply($request, $paramConverter));
    }

    /**
     * @expectedException \Brick\Math\Exception\NumberFormatException
     */
    public function testApplyFailsWithBadData()
    {
        $request             = new Request();
        $paramConverter      = new MockParamConverter([]);
        $moneyParamConverter = new MoneyParamConverter();
        $paramConverter->setClass(Money::class);
        $paramConverter->setOptions(['currency' => 'BTC']);

        $request->attributes->add([$paramConverter->getName() => '-o']);

        self::assertFalse($moneyParamConverter->apply($request, $paramConverter));
    }
}
