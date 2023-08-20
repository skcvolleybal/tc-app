<?php
// error_reporting(E_ALL);
// ini_set("display_errors", 1);

require_once("database.class.php");
require_once("tcapp.class.php");

require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExcelExport
{
   private $database;
   private $objPHPSpreadsheet;

   private $playerTypes = [
      "Spelverdeler" =>  ['fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['argb' => 'd9534f']], 'font' => ['color' => ['argb' => 'FFFFFF']]],
      "Midden" =>        ['fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['argb' => '5cb85c']], 'font' => ['color' => ['argb' => 'FFFFFF']]],
      "Passer-loper" =>  ['fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['argb' => 'f0ad4e']], 'font' => ['color' => ['argb' => 'FFFFFF']]],
      "Diagonaal" =>     ['fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['argb' => '5bc0de']], 'font' => ['color' => ['argb' => 'FFFFFF']]],
      "Libero" =>        ['fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['argb' => '337ab7']], 'font' => ['color' => ['argb' => 'FFFFFF']]],
      "Trainingslid" =>  ['fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['argb' => 'f5f5f5']], 'font' => ['color' => ['argb' => '808080']]],
      "Interesse"    =>  ['fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['argb' => 'FFFFFF']], 'font' => ['color' => ['argb' => '5e5e5e'], 'italic' => true]],
      "Nog Niets" =>     ['fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['argb' => '777777']], 'font' => ['color' => ['argb' => 'FFFFFF']]],
      "Uitgeschreven" => ['fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['argb' => '555555']], 'font' => ['color' => ['argb' => 'eeeeee'], 'italic' => true]],
      "Interesse-setter" => ['fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['argb' => 'f45258']], 'font' => ['color' => ['argb' => 'eeeeee'], 'italic' => true]],
      "Interesse-midden" => ['fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['argb' => '87dcab']], 'font' => ['color' => ['argb' => 'eeeeee'], 'italic' => true]],
      "Interesse-passer-loper" => ['fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['argb' => 'e9f05f']], 'font' => ['color' => ['argb' => 'eeeeee'], 'italic' => true]],
      "Interesse-diagonaal" => ['fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['argb' => '8bd6f1']], 'font' => ['color' => ['argb' => 'eeeeee'], 'italic' => true]],
      "Interesse-libero" => ['fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['argb' => '598ef7']], 'font' => ['color' => ['argb' => 'eeeeee'], 'italic' => true]]
   ];

   public function __construct($database)
   {
      $this->database = $database;
   }

   public function CreateDocument()
   {
      $this->objPHPSpreadsheet = new Spreadsheet();
   }

   function SetBorder($cells)
   {
      $styleArray = [
         'borders' => [
            'left' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
            'right' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
            'bottom' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
            'top' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]
         ]
      ];

      $this->objPHPSpreadsheet->getActiveSheet()->getStyle($cells)->applyFromArray($styleArray);
   }

   public function SetPlayer($column, $row, $name, $playerType)
   {
      $cell = $this->GetCellName($column, $row);
      $this->SetCell($cell, $name);
      $this->PaintCell($cell, $playerType);
   }

   public function GetCellName($column, $row)
   {
      $alphabet = "ABCDEFGHIJKLMONPQRSTUVWXYZ";
      return $alphabet[$column] . ($row + 1);
   }

   private function SetCell($cell, $value)
   {
      $this->objPHPSpreadsheet->getActiveSheet()->setCellValue($cell, $value);
   }

   private function SetCellBold($cell, $value)
   {
      $this->objPHPSpreadsheet->getActiveSheet()->setCellValue($cell, $value);
      $this->objPHPSpreadsheet->getActiveSheet()->getStyle($cell)->getFont()->setBold(true);
   }

   private function SetCellItalic($cell, $value)
   {
      $this->objPHPSpreadsheet->getActiveSheet()->setCellValue($cell, $value);
      $this->objPHPSpreadsheet->getActiveSheet()->getStyle($cell)->getFont()->setItalic(true);
   }

   private function SetFontSize($cell, $size)
   {
      $this->objPHPSpreadsheet->getActiveSheet()->getStyle($cell)->getFont()->setSize($size);
   }

   private function PaintCell($cell, $type)
   {
      $this->objPHPSpreadsheet->getActiveSheet()->getStyle($cell)->applyFromArray($this->playerTypes[$type]);
   }

   private function DrawLegenda()
   {
      $this->SetCell("A1", "Legenda");
      $this->objPHPSpreadsheet->getActiveSheet()->getStyle('A1')->getFont()->setBold(true)->setItalic(true)->setUnderline(true);

      $this->SetCell("A2", "Spelverdeler");
      $this->PaintCell("A2", "Spelverdeler");

      $this->SetCell("A3", "Midden");
      $this->PaintCell("A3", "Midden");

      $this->SetCell("A4", "Passer-loper");
      $this->PaintCell("A4", "Passer-loper");

      $this->SetCell("A5", "Diagonaal");
      $this->PaintCell("A5", "Diagonaal");

      $this->SetCell("A6", "Libero");
      $this->PaintCell("A6", "Libero");

      $this->SetCell("A7", "Nog Niets");
      $this->PaintCell("A7", "Nog Niets");

      $this->SetCell("A8", "Trainingslid");
      $this->PaintCell("A8", "Trainingslid");

      $this->SetCell("A9", "Interesse");
      $this->PaintCell("A9", "Interesse");

      $this->SetCell("A10", "Uitgeschreven");
      $this->PaintCell("A10", "Uitgeschreven");

      $this->SetCell("A11", "Interesse-setter");
      $this->PaintCell("A11", "Interesse-setter");

      $this->SetCell("A12", "Interesse-midden");
      $this->PaintCell("A12", "Interesse-midden");
      

      $this->SetCell("A13", "Interesse-passer-loper");
      $this->PaintCell("A13", "Interesse-passer-loper");

      $this->SetCell("A14", "Interesse-diagonaal");
      $this->PaintCell("A14", "Interesse-diagonaal");

      $this->SetCell("A15", "Interesse-libero");
      $this->PaintCell("A15", "Interesse-libero");

      $this->SetBorder("A2:A15");
   }

   public function GetTeams($query)
   {
      $result = $this->database->executeQuery($query);

      $currentTeamId = -1;

      $teams = [];
      $counter = -1;
      foreach ($result as $player) {
         $teamId = $player['teamId'];
         if ($teamId != $currentTeamId) {
            $currentTeamId = $teamId;
            $counter++;
            $teams[] = [
               'id' => $player['teamId'],
               'name' => $player['teamName'],
               'trainingInfo' => $player['trainingInfo'],
               'players' => []
            ];
         }
         $teams[$counter]['players'][] = [
            "name" => $player['name'],
            "type" => $player['type']
         ];
      }

      return $teams;
   }

   private function DrawPlayers($teams)
   {
      $this->DrawLegenda();

      $baseRow = count($this->playerTypes) + 2;
      $baseColumn = 0;
      $maxRows = 0;

      foreach ($teams as $team) {
         $currentRow = $baseRow;

         $cell = $this->GetCellName($baseColumn, $currentRow++);
         $teamTitle = $team['name'] . " (" . count($team['players']) . ")";
         $this->SetCellBold($cell, $teamTitle);

         $trainingInfo = $team['trainingInfo'];
         $cell = $this->GetCellName($baseColumn, $currentRow++);
         $this->setCell($cell, $trainingInfo);
         $this->SetFontSize($cell, 8);

         foreach ($team['players'] as $player) {
            $this->SetPlayer($baseColumn, $currentRow++, $player['name'], $player['type']);

            if ($maxRows < $currentRow) {
               $maxRows = $currentRow;
            }
         }

         $cellTop = $this->GetCellName($baseColumn, $baseRow + 2);
         $cellBottom = $this->GetCellName($baseColumn, $currentRow - 1);

         $this->SetBorder($cellTop . ":" . $cellBottom);

         $baseColumn++;

         if ($baseColumn > 2) {
            $baseColumn = 0;
            $baseRow = $maxRows + 2;
            $maxRows = 0;
         }
      }

      foreach (range('A', 'C') as $columnID) {
         $this->objPHPSpreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
      }
   }

   public function GetExcelExport()
   {

      $this->CreateDocument();

      $this->objPHPSpreadsheet->getProperties()
         ->setCreator("Technische Commissie SKC")
         ->setTitle("TC indeling")
         ->setDescription("Teamindeling voor het aankomende volleybal seizoen.")
         ->setKeywords("TC teamindeling SKC volleybal");

      $query = "select 
                      T.id as teamId,
                      T.name as teamName,
                      T.training_info as trainingInfo,
                      P.name as name,
                      PT.name as type
                   from tcapp_players P
                   left join tcapp_teams T on T.id = P.team_id
                   left join tcapp_player_types PT on P.type_id = PT.id
                   where T.type = 'team'
                   order by sequence, PT.id, P.name";

      $this->SetSheetName("Teamindeling");
      $teams = $this->GetTeams($query);
      $this->DrawPlayers($teams);

      $query = "select 
                      T.id as teamId,
                      T.name as teamName,
                      P.name as name,
                      T.training_info as trainingInfo,
                      PT.name as type
                   from tcapp_players P
                   left join tcapp_teams T on T.id = P.training_id
                   left join tcapp_player_types PT on P.type_id = PT.id
                   where T.type = 'training'
                   order by sequence, PT.id, P.name";
      $this->CreateNewSheet();
      $this->SetActiveSheet(1);
      $this->SetSheetName("Trainingsgroepen");
      $teams = $this->GetTeams($query);
      $this->DrawPlayers($teams);

      $this->objPHPSpreadsheet->setActiveSheetIndex(0)->setSelectedCell("A1");
   }

   private function SetSheetName($name)
   {
      $this->objPHPSpreadsheet->getActiveSheet()->setTitle($name);
   }

   private function CreateNewSheet()
   {
      $this->objPHPSpreadsheet->createSheet();
   }

   private function SetActiveSheet($index)
   {
      $this->objPHPSpreadsheet->setActiveSheetIndex($index);
   }

   public function returnExcelExport()
   {

      $writer = new Xlsx($this->objPHPSpreadsheet);
      ob_end_clean();
      header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
      header('Content-Disposition: attachment; filename="TC-indeling '  . date("d-m-Y") .   '.xlsx"');
      $writer->save('php://output');

   }
}

$database = new Database();

$tcApp = new TcApp();
$tcApp->InitJoomla();
$user = $tcApp->GetUser();
$tcApp->CheckForTcRights($user);

$excelExport = new ExcelExport($database);

$excelExport->GetExcelExport();
$excelExport->returnExcelExport();
