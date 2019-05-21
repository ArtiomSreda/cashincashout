<?php

namespace Cashincashout\Models;

interface UserServiceInterface
{
    public function setUser($userId);

    public function getUser($userId);

    public function addUserWeekCashOut($userId, $value, $currencyRate);

    public function getUserWeekCashOut($userId);

    public function setUserWeekCashOut($userId, $value, $currencyRate);

    public function addUserWeekCashOutCount($userId);

    public function getUserWeekCashOutCount($userId);

    public function setUserWeekCashOutCount($userId);

    public function setUserLastOperationDate($userId, $userLastOperationDate);

    public function getUserLastOperationDate($userId);
}


class UserOperationModel implements UserServiceInterface
{

    public $usersCollection = [];


    /**
     * @param $userId
     */
    public function setUser($userId)
    {
        if (!array_key_exists($userId, $this->usersCollection)) {
            $this->usersCollection[$userId] = [];
        }
    }

    /**
     * @param $userId
     * @return mixed
     */
    public function getUser($userId)
    {
        return $this->usersCollection[$userId];
    }


    /**
     * @param $userId
     * @param $value
     * @param $currencyRate
     */
    public function addUserWeekCashOut($userId, $value, $currencyRate)
    {
        if (!array_key_exists('total_week_cash_out_EUR', $this->usersCollection[$userId])) {
            $this->usersCollection[$userId]['total_week_cash_out_EUR'] = 0;
        }
        $this->usersCollection[$userId]['total_week_cash_out_EUR'] += $value / $currencyRate;
    }

    /**
     * @param $userId
     * @return mixed
     */
    public function getUserWeekCashOut($userId)
    {
        return $this->usersCollection[$userId]['total_week_cash_out_EUR'];
    }

    /**
     * @param $userId
     * @param $value
     * @param $currencyRate
     */
    public function setUserWeekCashOut($userId, $value, $currencyRate)
    {
        $this->usersCollection[$userId]['total_week_cash_out_EUR'] = $value / $currencyRate;
    }


    /**
     * @param $userId
     */
    public function addUserWeekCashOutCount($userId)
    {
        if (!array_key_exists('total_week_cash_out_count', $this->usersCollection[$userId])) {
            $this->usersCollection[$userId]['total_week_cash_out_count'] = 0;
        }
        $this->usersCollection[$userId]['total_week_cash_out_count'] += 1;
    }

    /**
     * @param $userId
     * @return mixed
     */
    public function getUserWeekCashOutCount($userId)
    {
        return $this->usersCollection[$userId]['total_week_cash_out_count'];
    }

    /**
     * @param $userId
     */
    public function setUserWeekCashOutCount($userId)
    {
        $this->usersCollection[$userId]['total_week_cash_out_count'] = 1;
    }


    /**
     * @param $userId
     * @param $userLastOperationDate
     */
    public function setUserLastOperationDate($userId, $userLastOperationDate)
    {
        $this->usersCollection[$userId]['last_operation_date'] = $userLastOperationDate;
    }

    /**
     * @param $userId
     * @return mixed
     */
    public function getUserLastOperationDate($userId)
    {
        if (!array_key_exists('last_operation_date', $this->usersCollection[$userId])) {
            $this->usersCollection[$userId]['last_operation_date'] = '0000-00-00';
        }

        return $this->usersCollection[$userId]['last_operation_date'];
    }

}