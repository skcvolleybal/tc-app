<?php
   // error_reporting(E_ALL);
   // ini_set("display_errors", 1);

   require_once("database.class.php");
   require_once("tcapp.class.php");
   require_once("./../libs/PHPExcel/Classes/PHPExcel.php");

   class ExcelExport {
      private $database;
      private $objPHPExcel;
      private $currentSheet;

      private $playerTypes = [
         "Spelverdeler" => ['fill' => ['type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => ['rgb' => 'd9534f']], 'font' => ['color' => ['rgb'=>'FFFFFF']]],
         "Midden" =>       ['fill' => ['type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => ['rgb' => '5cb85c']], 'font' => ['color' => ['rgb'=>'FFFFFF']]],
         "Passer-loper" => ['fill' => ['type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => ['rgb' => 'f0ad4e']], 'font' => ['color' => ['rgb'=>'FFFFFF']]],
         "Diagonaal" =>    ['fill' => ['type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => ['rgb' => '5bc0de']], 'font' => ['color' => ['rgb'=>'FFFFFF']]],
         "Libero" =>       ['fill' => ['type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => ['rgb' => '337ab7']], 'font' => ['color' => ['rgb'=>'FFFFFF']]],
         "Trainingslid" => ['fill' => ['type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => ['rgb' => 'f5f5f5']], 'font' => ['color' => ['rgb'=>'808080']]],
         "Interesse"    => ['fill' => ['type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => ['rgb' => 'FFFFFF']], 'font' => ['color' => ['rgb'=>'5e5e5e'], 'italic' => true]],
         "Nog Niets" =>    ['fill' => ['type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => ['rgb' => '777777']], 'font' => ['color' => ['rgb'=>'FFFFFF']]]
      ];

      public function __construct($database){
         $this->database = $database;
      }

      public function CreateDocument(){
         $this->objPHPExcel = new PHPExcel();
      }

      function SetBorder($cells){
         $styleArray = [
            'borders' => [
               'left' => ['style' => PHPExcel_Style_Border::BORDER_THIN],
               'right' => ['style' => PHPExcel_Style_Border::BORDER_THIN],
               'bottom' => ['style' => PHPExcel_Style_Border::BORDER_THIN],
               'top' => ['style' => PHPExcel_Style_Border::BORDER_THIN]
            ]
         ];

         $this->objPHPExcel->getActiveSheet()->getStyle($cells)->applyFromArray($styleArray);
      }

      public function SetPlayer($column, $row, $name, $playerType){
         $cell = $this->GetCellName($column, $row);
         $this->SetCell($cell, $name);
         $this->PaintCell($cell, $playerType);
      }

      public function GetCellName($column, $row){
         $alphabet = "ABCDEFGHIJKLMONPQRSTUVWXYZ";
         return $alphabet[$column] . ($row + 1);
      }

      private function SetCell($cell, $value){
         $this->objPHPExcel->getActiveSheet()->setCellValue($cell, $value);
      }

      private function SetCellBold($cell, $value){
         $this->objPHPExcel->getActiveSheet()->setCellValue($cell, $value);
         $this->objPHPExcel->getActiveSheet()->getStyle($cell)->getFont()->setBold(true);
      }

      private function SetCellItalic($cell, $value){
         $this->objPHPExcel->getActiveSheet()->setCellValue($cell, $value);
         $this->objPHPExcel->getActiveSheet()->getStyle($cell)->getFont()->setItalic(true);
      }

      private function SetFontSize($cell, $size){
         $this->objPHPExcel->getActiveSheet()->getStyle($cell)->getFont()->setSize($size);
      }
      
      private function PaintCell($cell, $type){
         $this->objPHPExcel->getActiveSheet()->getStyle($cell)->applyFromArray( $this->playerTypes[$type] );
      }

      private function DrawLegenda(){
         $this->SetCell("A1", "Legenda");
         $this->objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true)->setItalic(true)->setUnderline(true);

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
         
         $this->SetBorder("A2:A9");
      }

      public function GetTeams($query){
         $result = $this->database->executeQuery($query);

         $currentTeamId = -1;

         $teams = [];
         $counter = -1;
         foreach ($result as $player){
            $teamId = $player['teamId'];
            if ($teamId != $currentTeamId){
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

      private function DrawPlayers($teams){
         $this->DrawLegenda();
         
         $baseRow = 10;
         $baseColumn = 0;
         $maxRows = 0;

         foreach ($teams as $team){
            $currentRow = $baseRow;
            
            $cell = $this->GetCellName($baseColumn, $currentRow++);
            $teamTitle = $team['name'] . " (" . count($team['players']) . ")";
            $this->SetCellBold($cell, $teamTitle);
            
            $trainingInfo = $team['trainingInfo'];
            $cell = $this->GetCellName($baseColumn, $currentRow++);
            $this->setCell($cell, $trainingInfo);
            $this->SetFontSize($cell, 8);
            
            foreach($team['players'] as $player){
               $this->SetPlayer($baseColumn, $currentRow++, $player['name'], $player['type']);
               
               if ($maxRows < $currentRow){
                  $maxRows = $currentRow;
               }
            }

            $cellTop = $this->GetCellName($baseColumn, $baseRow + 2);
            $cellBottom = $this->GetCellName($baseColumn, $currentRow - 1);

            $this->SetBorder($cellTop . ":" . $cellBottom);
               
            $baseColumn++;

            if ($baseColumn > 2){
               $baseColumn = 0;
               $baseRow = $maxRows + 2;
               $maxRows = 0;
            }
         }

         foreach(range('A','C') as $columnID) {
            $this->objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
         }
      }

      public function GetExcelExport(){
         require_once dirname(__FILE__) . '/../libs/PHPExcel/Classes/PHPExcel.php';
         
         $this->CreateDocument();         
         
         $this->objPHPExcel->getProperties()
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

         $this->objPHPExcel->setActiveSheetIndex(0)->setSelectedCell("A1");
      }

      private function SetSheetName($name){
         $this->objPHPExcel->getActiveSheet()->setTitle($name);
      }

      private function CreateNewSheet(){
         $this->objPHPExcel->createSheet();
      }

      private function SetActiveSheet($index){
         $this->objPHPExcel->setActiveSheetIndex($index);
      }

      public function returnExcelExport(){
         $objWriter = PHPExcel_IOFactory::createWriter($this->objPHPExcel, 'Excel2007');
         header('Content-Type: application/vnd.ms-excel');
         header('Content-Disposition: attachment;filename="TC-indeling.xlsx"');
         header('Cache-Control: max-age=0');
         $objWriter->save('php://output');
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