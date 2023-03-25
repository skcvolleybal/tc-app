<?php
// error_reporting(E_ALL);
// ini_set("display_errors", 1);

require_once("database.class.php");
require_once("tcapp.class.php");

require '../vendor/autoload.php';


use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

?>

<?php
class ExportXLSX
{

    public function __construct()
    {
        $this->createExcel();
    }


    public static function createExcel( $fileName = 'data.xlsx')
    {
        $spreadsheet = new Spreadsheet();
        // Create a new worksheet called "My Data"
        $myWorkSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'My Data');

        // Attach the "My Data" worksheet as the first worksheet in the Spreadsheet object
        $spreadsheet->addSheet($myWorkSheet, 0);
        $myWorkSheet = $spreadsheet->getActiveSheet();
        $myWorkSheet->getCell('A1')->setValue('Hello world!');

        $writer = new Xlsx($spreadsheet);
        ob_end_clean();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. urlencode($fileName).'"');
        $writer->save('php://output');

        
    }

}

$exportxlsx = new ExportXLSX;