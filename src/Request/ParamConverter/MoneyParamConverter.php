<?php
declare(strict_types=1);

namespace Xgc\MoneyBundle\Request\ParamConverter;

use Brick\Math\Exception\ArithmeticException;
use InvalidArgumentException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Xgc\Money\Currency;
use Xgc\Money\Exception\InvalidCurrencyException;
use Xgc\Money\Money;

/**
 * Class MoneyParamConverter
 *
 * @package Xgc\XgcMoneyBundle\Request
 */
class MoneyParamConverter implements ParamConverterInterface
{

    /**
     * Stores the object in the request.
     *
     * @param Request        $request       The request
     * @param ParamConverter $configuration Contains the name, class and options of the object
     *
     * @return bool True if the object has been successfully set, else false
     *
     * @throws ArithmeticException
     * @throws InvalidCurrencyException
     * @throws NotFoundHttpException
     * @throws InvalidArgumentException
     */
    public function apply(Request $request, ParamConverter $configuration): bool
    {
        if (!$this->isValidInput($request, $configuration)) {
            return false;
        }

        $param    = $configuration->getName();
        $options  = $configuration->getOptions();
        $value    = $request->attributes->get($param);
        $currency = $this->loadMoney($options, $value);

        $request->attributes->set($param, $currency);

        return true;
    }

    /**
     * Checks if the object is supported.
     *
     * @param ParamConverter $configuration Should be an instance of ParamConverter
     *
     * @return bool True if the object is supported, else false
     */
    public function supports(ParamConverter $configuration): bool
    {
        if ($configuration->getClass() === null) {
            return false;
        }

        return Money::class === $configuration->getClass();
    }

    /**
     * @param Request        $request
     * @param ParamConverter $configuration
     *
     * @return bool
     */
    private function isValidInput(Request $request, ParamConverter $configuration): bool
    {
        $param = $configuration->getName();

        return !(
            (!$request->attributes->has($param)) ||
            (!$request->attributes->get($param) && $configuration->isOptional())
        );
    }

    /**
     * @param array  $options
     *
     * @param string $value
     *
     * @return Money;
     *
     * @throws InvalidArgumentException
     * @throws ArithmeticException
     * @throws InvalidCurrencyException
     */
    private function loadMoney(array $options, $value): Money
    {
        return new Money(Currency::get($options['currency']), $value);
    }
}
