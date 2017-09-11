<?php
declare(strict_types=1);

namespace Tests\Unit\Doctrine\DBAL\Type;

use Doctrine\DBAL\Types\Type;
use Tests\Mock\MockPlatform;
use Xgc\Money\Currency\BTC;
use Xgc\Money\Money;
use Xgc\MoneyBundle\Doctrine\DBAL\Type\MoneyType;
use Xgc\MoneyBundle\Test\TestCase;

/**
 * Class MoneyTypeTest
 *
 * @package Tests\Unit\Doctrine\DBAL\Type
 */
class MoneyTypeTest extends TestCase
{
    /**
     * @var MockPlatform
     */
    protected $platform;

    /**
     * @var Type
     */
    protected $type;

    protected function setUp(): void
    {
        if (!Type::hasType('money')) {
            Type::addType('money', MoneyType::class);
        }

        $this->platform = new MockPlatform();
        $this->type     = Type::getType('money');
    }

    public function testConvertToPHPValue()
    {
        self::assertInstanceOf(Money::class, $this->type->convertToPHPValue('1.337', $this->platform));
    }

    public function testNullConversion()
    {
        self::assertNull($this->type->convertToPHPValue(null, $this->platform));
        self::assertNull($this->type->convertToDatabaseValue(null, $this->platform));
    }

    /**
     * @expectedException \Doctrine\DBAL\Types\ConversionException
     */
    public function testConversionToPHPException()
    {
        $this->type->convertToPHPValue('', $this->platform);
    }

    public function testConvertToDatabaseValue()
    {
        $expected = new BTC(1.337);

        self::assertSame(
            $expected->getAmount()->__toString(),
            $this->type->convertToDatabaseValue($expected, $this->platform)
        );
    }

    /**
     * @expectedException \Doctrine\DBAL\Types\ConversionException
     */
    public function testConversionToDatabaseException()
    {
        $this->type->convertToDatabaseValue('2', $this->platform);
    }

    /**
     * @expectedException \Doctrine\DBAL\DBALException
     */
    public function testGetSQLDeclaration()
    {
        $this->type->getSQLDeclaration([], $this->platform);
    }
}
