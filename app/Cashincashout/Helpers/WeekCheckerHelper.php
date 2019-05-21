<?php

namespace Cashincashout\Helpers;

interface WeekCheckerServiceInterface
{

    public function checkWeekInterval(string $checkableDate, string $lastUserOperationDate);
}


class WeekCheckerHelper implements WeekCheckerServiceInterface
{

    /**
     * @param string $checkableDate
     * @param string $lastUserOperationDate
     * @return bool
     */
    public function checkWeekInterval(string $checkableDate, string $lastUserOperationDate)
    {

        WeekCheckerHelper::isValidDateString($checkableDate);
        WeekCheckerHelper::isValidDateString($lastUserOperationDate);

        $mondayDate = date('Y-m-d', strtotime('monday', strtotime($checkableDate)));
        if ($checkableDate != $mondayDate) {
            $mondayDate = date('Y-m-d', strtotime('last monday', strtotime($checkableDate)));
        }
        $sundayDate = date('Y-m-d', strtotime('sunday', strtotime($checkableDate)));

        if ($lastUserOperationDate > $mondayDate && $lastUserOperationDate < $sundayDate) {
            return true;

        } else {
            return false;
        }
    }


    /**
     * @param string $value
     */
    private function isValidDateString(string $value): void
    {
        if (!$value) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Date value "%s" not set or was cleared.', $value
                )
            );
        } else {
            try {
                new \DateTime($value);
            } catch (\Exception $e) {
                throw new \InvalidArgumentException(
                    sprintf(
                        'Incorrect date string "%s".', $value
                    )
                );
            }
        }
    }

}