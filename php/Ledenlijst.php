<?php
   error_reporting(E_ALL);
   ini_set("display_errors", 1);

   require_once("database.class.php");
   require_once("tcapp.class.php");
   require_once("./../libs/PHPExcel/Classes/PHPExcel.php");

   class LedenLijst {
      private $database;
      private $objPHPExcel;
   
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

      public function ReturnExcelExport(){
         $objWriter = PHPExcel_IOFactory::createWriter($this->objPHPExcel, 'Excel2007');
         header('Content-Type: application/vnd.ms-excel');
         header('Content-Disposition: attachment;filename="TC-ledenlijst.xlsx"');
         header('Cache-Control: max-age=0');
         $objWriter->save('php://output');
      }

      private function AddFilters($range){
         $this->objPHPExcel->getActiveSheet()->setAutoFilter($range);
      }

      public function GetLedenlijst(){
         $this->CreateDocument();

         $this->SetCellBold("A1", "Naam");
         $this->SetCellBold("B1", "Team");
         $this->SetCellBold("C1", "Trainingsgroep");
         $this->SetCellBold("D1", "Positie");

         $query = "select 
                     P.name, 
                     T1.name as team, 
                     T2.name as training, 
                     PT.name as type  
                   from tcapp_players P 
                   left join tcapp_teams T1 on P.team_id = T1.id
                   left join tcapp_teams T2 on P.training_id = T2.id
                   left join tcapp_player_types PT on P.type_id = PT.id
                   order by T1.sequence, P.name";
         $result = $this->database->executeQuery($query);
         $counter = 1;
         foreach ($result as $row){
            $this->SetCell($this->GetCellName(0, $counter), $row['name']);
            $this->SetCell($this->GetCellName(1, $counter), $row['team']);
            $this->SetCell($this->GetCellName(2, $counter), $row['training']);
            $this->SetCell($this->GetCellName(3, $counter), $row['type']);

            $counter++;            
         }

         $this->AddFilters('A1:D1');
         
         foreach(range('A','D') as $columnID) {
            $this->objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
         }
      }
   }

   $database = new Database();

   $tcApp = new TcApp();
   $tcApp->InitJoomla();
   $user = $tcApp->GetUser();
   $tcApp->CheckForTcRights($user);

   $excelExport = new LedenLijst($database);
   
   $excelExport->GetLedenlijst();
   $excelExport->ReturnExcelExport();