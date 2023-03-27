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

    private $database;
    private $spreadsheetobject;

    public function __construct($database)
    {
        $this->database = $database;
    }

    public function getExcelExport()
    {
        $spreadsheet = new Spreadsheet();
        $this->spreadsheetobject = $spreadsheet;
        $sheet1 = new Worksheet($spreadsheet, 'Teams');
        $spreadsheet->addSheet($sheet1, 0);
    
        $sheet2 = new Worksheet($spreadsheet, 'Trainingsgroepen');
        $spreadsheet->addSheet($sheet2, 1);

        //gooi data in teams
        $spreadsheet->setActiveSheetIndex(0);

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

        //$teams = $this->GetTeams($query);
        //$this->DrawPlayers($teams);

        //gooi data in trainingsgroepen
        $spreadsheet->setActiveSheetIndex(1);
       
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

        //$teams = $this->GetTeams($query);
        //$this->DrawPlayers($teams);

        //gooi originele worksheet eruit
        $spreadsheet->setActiveSheetIndex(2);
        $sheetIndex = $spreadsheet->getActiveSheetIndex();
        $spreadsheet->removeSheetByIndex($sheetIndex);

        //spring terug naar de Teams tab als beginpunt
        $spreadsheet->setActiveSheetIndex(0)->setSelectedCell("A1");

        $writer = new Xlsx($spreadsheet);
        ob_end_clean();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="TC-indeling.xlsx"');
        $writer->save('php://output');
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

   public function drawLegenda () {
      //  Banda 
   }

   public function DrawPlayers($teams)
   {
      // Bas

   }

   public function drawTrainingsgroepen ($trainingsgroepen) {
      // Koen
 
   }




}

$database = new Database();

$tcApp = new TcApp();
$tcApp->InitJoomla();

// Only logged in users can access this page
$user = $tcApp->GetUser();

// Only TC members can download this Excel file 
$tcApp->CheckForTcRights($user);


$exportxlsx = new ExportXLSX($database);
$exportxlsx->getExcelExport();