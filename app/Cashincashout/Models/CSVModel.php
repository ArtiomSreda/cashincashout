<?php

namespace Cashincashout\Models;

interface CSVServiceInterface
{
    public function __construct(string $csvFilePath);

    public function setCSVPath(string $csvFilePath);

    public function getCSVPath(): string;

    public function parseCSV(string $csvFilePath): array;
}


class CSVModel implements CSVServiceInterface
{
    private $csvArrayKeys = ['operation_date', 'user_id', 'user_type', 'operation_type', 'value', 'currency'];
    private $csvArray = [];
    private $csvFilePath;


    public function __construct(string $csvFilePath = null)
    {
        if (!$csvFilePath)
            return false;
        $this->setCSVPath($csvFilePath);
    }


    public function setCSVPath(string $csvFilePath)
    {
        self::isValidPathToCSVFile($csvFilePath);
        $this->csvFilePath = $csvFilePath;
    }


    public function getCSVPath(): string
    {
        return $this->csvFilePath;
    }


    public function parseCSV(string $csvFilePath = null): array
    {
        self::isValidPathToCSVFile($csvFilePath);

        if ($csvFilePath) {
            $this->setCSVPath($csvFilePath);
        }

        $row = 0;
        if (($handle = fopen($csvFilePath, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 0, "\n")) !== FALSE) {
                $num = count($data);
                for ($c = 0; $c < $num; $c++) {
                    $operationRowArray = str_getcsv($data[$c]);
                    $this->csvArray[$row] = array_combine($this->csvArrayKeys, $operationRowArray);
                }
                $row++;
            }
            fclose($handle);
        }
        return $this->csvArray;
    }


    private function isValidPathToCSVFile(string $value): void
    {
        if (!file_exists($value) && !strstr($value, ".csv")) {
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