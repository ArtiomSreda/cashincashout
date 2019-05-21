<?php

namespace Cashincashout\Models;

interface CurrencyServiceInterface
{

    public function getCurrencyRateByKey($key);

    public function getCurrenciesRates(): array;

    public function customRound($value, $currency);

}


class CurrencyModel implements CurrencyServiceInterface
{
    /**
     * NOTE: in root/config directory is configured currencies conversion rates values
     * __construct() parse json
     */
    public $currenciesConversionRates = [
        'EUR' => 1.0,
        'USD' => 1.1497,
        'JPY' => 129.53
    ];

    private $pathToCurrenciesConversionRatesJsonFile = __DIR__ . "/../../../config/currenciesConversionRates.json";

    public function __construct()
    {
        self::isValidPathToConversionRatesFile($this->pathToCurrenciesConversionRatesJsonFile);
        $this->currenciesConversionRates = json_decode(file_get_contents($this->pathToCurrenciesConversionRatesJsonFile), true);
    }

    /**
     * TODO: in xampp need add and enabled: extension=php_intl.dll
     * $fmt = new \NumberFormatter('lt_LT', \NumberFormatter::CURRENCY);
     * $value = $fmt->formatCurrency($value,$currency);
     * NOTE: need ask about correct rounding methods
     */
    public function customRound($value, $currency)
    {
        if ($currency == 'EUR' || $currency == 'USD') {
            $value = ceil(round($value, 3) * 100) / 100;
            $value = number_format($value, 2, '.', '');

        } elseif ($currency == 'JPY') {
            $value = ceil($value);
        }
        return $value;
    }

    public function getCurrencyRateByKey($key)
    {
        return $this->currenciesConversionRates[$key];
    }

    public function getCurrenciesRates(): array
    {
        return $this->currenciesConversionRates;
    }

    private function isValidPathToConversionRatesFile(string $value): void
    {
        if (!file_exists($value) && !strstr($value, ".json")) {
            throw new \InvalidArgumentException(
                sprintf(
                    'CSV file "%s" not found.', $value
                )
            );
        } else {
            try {
                $file = file_get_contents($value);
            } catch (\Exception $e) {
                throw new \InvalidArgumentException(
                    sprintf(
                        'Can\'t get file content "%s". ' . $e, $value
                    )
                );
            }
        }

    }


}