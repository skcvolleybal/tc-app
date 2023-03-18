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
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Hello World !');
        
        $writer = new Xlsx($spreadsheet);
        ob_end_clean();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. urlencode($fileName).'"');
        $writer->save('php://output');
    }

}

$exportxlsx = new ExportXLSX;