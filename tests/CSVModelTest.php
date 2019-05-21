<?php

use PHPUnit\Framework\TestCase;

final class CSVModelTest extends TestCase
{
    protected $csvModel;

    protected function setUp(): void
    {
        $this->csvModel = new \Cashincashout\Models\CSVModel('app/data/input.csv');
    }

    public function testOrCSVFilePathSetCorrectlyAndReturnString(): string
    {
        $csvFilePath = $this->csvModel->getCSVPath();
        $this->assertIsString($csvFilePath);
        return $csvFilePath;
    }

    /**
     * @depends testOrCSVFilePathSetCorrectlyAndReturnString
     */
    public function testOrCSVFilePathNotEmpty(string $csvFilePath): string
    {
        $this->assertNotEmpty($csvFilePath);
        return $csvFilePath;
    }

    /**
     * @depends testOrCSVFilePathNotEmpty
     */
    public function testOrCSVFileExists(string $csvFilePath)
    {
        $this->assertFileExists($csvFilePath);
    }

    /**
     * @depends testOrCSVFilePathSetCorrectlyAndReturnString
     */
    public function testOrParsingCSVDoesReturnArray(string $csvFilePath): void
    {
        $this->assertIsArray($this->csvModel->parseCSV($csvFilePath));
    }

    public function testOrWorkingWrongCSVPathException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->csvModel->setCSVPath('invalid_path/app/data/input.csv');
    }

    protected function tearDown(): void
    {
        $this->csvModel = null;
    }


}