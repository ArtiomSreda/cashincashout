<?php

namespace Cashincashout;

use Cashincashout\Models\CSVServiceInterface;
use Cashincashout\Models\UserServiceInterface;
use Cashincashout\Models\FeeServiceInterface;
use Cashincashout\Models\CurrencyServiceInterface;
use Cashincashout\Helpers\WeekCheckerServiceInterface;


class BaseController
{
    private $csvServiceInterface;
    private $userServiceInterface;
    private $feeServiceInterface;
    private $currencyServiceInterface;
    private $weekCheckerServiceInterface;

    private $csvArray = [];

    public function __construct(CSVServiceInterface $csvServiceInterface, UserServiceInterface $userServiceInterface, FeeServiceInterface $feeServiceInterface, CurrencyServiceInterface $currencyServiceInterface, WeekCheckerServiceInterface $weekCheckerServiceInterface)
    {
        $this->csvServiceInterface = $csvServiceInterface;
        $this->userServiceInterface = $userServiceInterface;
        $this->feeServiceInterface = $feeServiceInterface;
        $this->currencyServiceInterface = $currencyServiceInterface;
        $this->weekCheckerServiceInterface = $weekCheckerServiceInterface;
    }

    public function setCSVArray(array $csvArray)
    {
        $this->csvArray = $csvArray;
    }


    public function operationsDataPost(array $csvArray = [])
    {

        if (!empty($csvArray)) {
            $this->setCSVArray($csvArray);
        }

        foreach ($this->csvArray as $key => $operationArray) {

            $operationType = $operationArray['operation_type'];
            $operationUserId = $operationArray['user_id'];
            $operationValue = $operationArray['value'];
            $operationDate = $operationArray['operation_date'];
            $operationCurrency = $operationArray['currency'];
            $operationUserType = $operationArray['user_type'];
            $operationValueEUR = $operationArray['value'] / $this->currencyServiceInterface->getCurrencyRateByKey($operationCurrency);

            if ($operationType == 'cash_in') {
                $operationArray['fee'] = $this->feeServiceInterface->getCashInFee($operationValue, $operationCurrency);
            }

            if ($operationType == 'cash_out' && $operationUserType == 'legal') {
                $operationArray['fee'] = $this->feeServiceInterface->getCashOutLegalFee($operationValue, $operationCurrency);
            }

            $this->userServiceInterface->setUser($operationUserId);

            if ($operationType == 'cash_out' && $operationUserType == 'natural') {

                //NOTE: Attention - here check or natural cash_out is on same week, else set new week first cash_out value
                if ($this->weekCheckerServiceInterface->checkWeekInterval($operationDate, $this->userServiceInterface->getUserLastOperationDate($operationUserId))) {
                    $this->userServiceInterface->addUserWeekCashOut($operationUserId, $operationValue, $this->currencyServiceInterface->getCurrencyRateByKey($operationCurrency));
                    $this->userServiceInterface->addUserWeekCashOutCount($operationUserId);
                } else {
                    $this->userServiceInterface->setUserWeekCashOut($operationUserId, $operationValue, $this->currencyServiceInterface->getCurrencyRateByKey($operationCurrency));
                    $this->userServiceInterface->setUserWeekCashOutCount($operationUserId);
                }

                if ($this->userServiceInterface->getUserWeekCashOut($operationUserId) <= 1000) {
                    if ($this->userServiceInterface->getUserWeekCashOutCount($operationUserId) <= 3) {
                        $operationArray['fee'] = 0;
                    } elseif ($this->userServiceInterface->getUserWeekCashOutCount($operationUserId) > 3) {
                        $operationArray['fee'] = $this->feeServiceInterface->getCashOutNaturalFee($operationValue, $operationCurrency);
                    }
                } elseif ($this->userServiceInterface->getUserWeekCashOut($operationUserId) > 1000) { //NOTE: if user exceeded the cash out weekly discount limit
                    if ($this->userServiceInterface->getUserWeekCashOutCount($operationUserId) <= 3
                        && $this->userServiceInterface->getUserWeekCashOut($operationUserId) - $operationValueEUR <= 1000) {
                        //TODO: attention $taxableAmount here in EUR
                        //$taxableAmount = $this->userServiceInterface->getUserWeekCashOut($operationUserId) - 1000;
                        $taxableAmount = ($this->userServiceInterface->getUserWeekCashOut($operationUserId) - 1000) * $this->currencyServiceInterface->getCurrencyRateByKey($operationCurrency);
                        $operationArray['fee'] = $this->feeServiceInterface->getCashOutNaturalFee($taxableAmount, $operationCurrency);
                    } else { //NOTE: if user exceeded the cash out count weekly limit
                        $operationArray['fee'] = $this->feeServiceInterface->getCashOutNaturalFee($operationValue, $operationCurrency);
                    }
                }

            }

            $this->userServiceInterface->setUserLastOperationDate($operationUserId, $operationDate);
            print_r($this->currencyServiceInterface->customRound($operationArray['fee'], $operationCurrency) . "\n");

        }

    }

}