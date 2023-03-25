<?php
// error_reporting(E_ALL);
// ini_set("display_errors", 1);

require_once("database.class.php");
require_once("tcapp.class.php");

require '../vendor/autoload.php';


use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
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
        $myWorkSheet = new Worksheet($spreadsheet, 'Teams');
        $spreadsheet->addSheet($myWorkSheet, 0);
    
        $myWorkSheet2 = new Worksheet($spreadsheet, 'Trainingsgroepen');
        $spreadsheet->addSheet($myWorkSheet2, 1);

        //gooi data in teams
        $spreadsheet->setActiveSheetIndex(0);
        $myWorkSheet = $spreadsheet->getActiveSheet();
        $myWorkSheet->getCell('A1')->setValue('Teams');


        //gooi data in trainingsgroepen
        $spreadsheet->setActiveSheetIndex(1);
        $myWorkSheet2 = $spreadsheet->getActiveSheet();
        $myWorkSheet2->getCell('A1')->setValue('Trainingsgroepen');

        //gooi originele worksheet eruit
        $spreadsheet->setActiveSheetIndex(2);
        $sheetIndex = $spreadsheet->getActiveSheetIndex();
        $spreadsheet->removeSheetByIndex($sheetIndex);

        $writer = new Xlsx($spreadsheet);
        ob_end_clean();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. urlencode($fileName).'"');
        $writer->save('php://output');

        
    }

}

$exportxlsx = new ExportXLSX;