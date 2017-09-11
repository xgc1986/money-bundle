<?php
declare(strict_types=1);

namespace Xgc\MoneyBundle\Doctrine\DBAL\Type;

use Brick\Math\Exception\ArithmeticException;
use DateTime;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use InvalidArgumentException;
use Xgc\Money\Currency;
use Xgc\Money\Exception\InvalidCurrencyException;
use Xgc\Money\Money;

/**
 * Class MoneyType
 * @package Xgc\XgcMoneyBundle\Type
 */
class MoneyType extends Type
{

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'money';
    }

    /**
     * @param array            $fieldDeclaration
     * @param AbstractPlatform $platform
     *
     * @return string
     *
     * @throws DBALException
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return $platform->getDateTimeTypeDeclarationSQL($fieldDeclaration);
    }

    /**
     * @param null|Money|mixed $value
     * @param AbstractPlatform  $platform
     *
     * @return mixed|string
     *
     * @throws ConversionException
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return $value;
        }

        if ($value instanceof Money) {
            return $value->getAmount()->__toString();
        }

        throw ConversionException::conversionFailedInvalidType($value, $this->getName(), ['null', 'Money']);
    }

    /**
     * @param mixed|string     $value
     * @param AbstractPlatform $platform
     *
     * @return mixed|string|Money ?Money
     *
     * @throws InvalidCurrencyException
     * @throws InvalidArgumentException
     * @throws ConversionException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null || $value instanceof Money) {
            return $value;
        }

        try {
            $money = new Money(Currency::get('EUR'), $value);
        } catch (ArithmeticException $e) {
            throw ConversionException::conversionFailedFormat(
                $value,
                $this->getName(),
                'decimal',
                $e
            );
        }

        return $money;
    }
}
