<?php

namespace Cashincashout\Models;

interface FeeServiceInterface
{

    public function __construct(array $currenciesConversionRates);

    public function getCashInFee($userOperationValue, $currency): float;

    public function getCashOutLegalFee($userOperationValue, $currency): float;

    public function getCashOutNaturalFee($userOperationValue, $currency): float;

}


class FeeModel implements FeeServiceInterface
{
    private $currenciesConversionRates = [];

    public function __construct($currenciesConversionRates)
    {
        $this->currenciesConversionRates = $currenciesConversionRates;
    }

    public function getCashInFee($userOperationValue, $currency): float
    {
        //Commission fee - 0.03% from total amount, but no more than 5.00 EUR.
        $_feeFromAmount = $userOperationValue * 0.03 / 100;
        $_feeFromAmountEUR = $_feeFromAmount * $this->currenciesConversionRates[$currency];
        $_feeFinal = $_feeFromAmountEUR > 5 ? 5 * $this->currenciesConversionRates[$currency] : $_feeFromAmount;
        return $_feeFinal;
    }

    public function getCashOutLegalFee($userOperationValue, $currency): float
    {
        //Commission fee - 0.3% from amount, but not less than 0.50 EUR for operation.
        $_feeFromAmount = $userOperationValue * 0.3 / 100;
        $_feeFromAmountEUR = $_feeFromAmount * $this->currenciesConversionRates[$currency];
        $_feeFinal = $_feeFromAmountEUR < 0.5 ? 0.5 * $this->currenciesConversionRates[$currency] : $_feeFromAmount;
        return $_feeFinal;
    }

    public function getCashOutNaturalFee($userOperationValue, $currency): float
    {
        $_feeFromAmount = $userOperationValue * 0.3 / 100;
        $_feeFinal = $_feeFromAmount;
        return $_feeFinal;
    }

}