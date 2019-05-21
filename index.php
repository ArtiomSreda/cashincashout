<?php
declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';


class Index
{
    private $terminalTitle64 = 'CiAgIF9fX19fICAgICAgICAgIF8gICAgIF9fX19fICAgICAgICAgIF9fX19fICAgICAgICAgIF8gICAgICBfX19fICAgICAgICBfICAgCiAgLyBfX19ffCAgICAgICAgfCB8ICAgfF8gICBffCAgICAgICAgLyBfX19ffCAgICAgICAgfCB8ICAgIC8gX18gXCAgICAgIHwgfCAgCiB8IHwgICAgIF9fIF8gX19ffCB8X18gICB8IHwgIF8gX18gICB8IHwgICAgIF9fIF8gX19ffCB8X18gfCB8ICB8IHxfICAgX3wgfF8gCiB8IHwgICAgLyBfYCAvIF9ffCAnXyBcICB8IHwgfCAnXyBcICB8IHwgICAgLyBfYCAvIF9ffCAnXyBcfCB8ICB8IHwgfCB8IHwgX198CiB8IHxfX198IChffCBcX18gXCB8IHwgfF98IHxffCB8IHwgfCB8IHxfX198IChffCBcX18gXCB8IHwgfCB8X198IHwgfF98IHwgfF8gCiAgXF9fX19fXF9fLF98X19fL198IHxffF9fX19ffF98IHxffCAgXF9fX19fXF9fLF98X19fL198IHxffFxfX19fLyBcX18sX3xcX198CiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCg==';
    private $message = 'Example CSV file path is "app\data\input.csv"' . "\n" . 'Enter the path to the CSV file or type "cancel" to cancel:';
    private $splitter = '--------------------------------------------------------------------------------';
    private $pathToCSVFile;

    public function __construct()
    {
        if (empty($_SERVER['HTTP_USER_AGENT'])) {
            echo $this->splitter . "\n";
            echo base64_decode($this->terminalTitle64);
            echo $this->splitter . "\n";
            $this->argsHandler($this->message);
        } else {
            echo '<b>Please load script with terminal</b><br/>';
            echo $this->splitter . "<br/>";
            echo '<b>README.md</b><br/>';
            $readMe = str_replace("\n", "<br/>", file_get_contents('README.md'));
            echo '<code>' . $readMe . '</code>';
        }
    }


    public function argsHandler(String $message)
    {
        if (empty($message)) {
            echo 'Message "arg" is empty' . "\n";
        }
        echo $message;
        $handle = fopen("php://stdin", "r");
        $line = fgets($handle);
        if (!$line)
            exit();
        $line = preg_replace('/\s+/', '', trim($line));
        fclose($handle);

        if (trim($line) == 'cancel')
            exit();

        if (!strstr($line, ":\\")) {
            $line = __DIR__ . "/" . $line;
        }

        if (file_exists($line) && strstr($line, ".csv")) {
            echo $this->splitter . "\n";
            echo 'File exists. Path to input CSV file:' . "\n" . $line . "\n";
            echo $this->splitter . "\n";
            echo 'Result:' . "\n";
            $this->pathToCSVFile = $line;
            $this->init();
        } else {
            echo $this->splitter . "\n";
            echo 'File not found or not exists' . "\n";
            echo 'Formed path to input CSV file:' . "\n" . $line . "\n";
            $this->argsHandler($message);
        }

    }


    public function init()
    {
        $CSVModelObj = new \Cashincashout\Models\CSVModel($this->pathToCSVFile);
        $UserOperationModelObj = new \Cashincashout\Models\UserOperationModel();
        $CurrencyModelObj = new \Cashincashout\Models\CurrencyModel();
        $FeeModelObj = new \Cashincashout\Models\FeeModel($CurrencyModelObj->getCurrenciesRates());
        $WeekCheckerHelperObj = new \Cashincashout\Helpers\WeekCheckerHelper();
        $BaseController = new \Cashincashout\BaseController($CSVModelObj, $UserOperationModelObj, $FeeModelObj, $CurrencyModelObj, $WeekCheckerHelperObj);
        $csvArray = $CSVModelObj->parseCSV($CSVModelObj->getCSVPath());
        $BaseController->setCSVArray($csvArray);
        $result = $BaseController->operationsDataPost();
        print_r($result);
    }

}

/**
 * NOTE: Check how the user launches the app: terminal php, terminal composer, browser
 */
if (!empty($_SERVER['argv'])) {

    foreach ($_SERVER['argv'] as $item) {
        if (!strpos($item, 'composer', 0)) {
            $composer = false;
        } else {
            $composer = true;
            break;
        }
    }

    if (!$composer) {
        $run = new Index();
    }
} else {
    $run = new Index();
}