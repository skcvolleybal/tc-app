<?php
// error_reporting(E_ALL);
// ini_set("display_errors", 1);

require_once("database.class.php");
require_once("tcapp.class.php");

?>


 
<?php
class ExcelExport
{
   private $database;
 
   public function __construct($database)
   {
      $this->database = $database;

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

   public function makeExports()
   {
 

      $teamquery = "select 
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

      
      $teamindeling = $this->GetTeams($teamquery); 

         $trainingsgroepquery = "select 
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

      $trainingsgroepen = $this->GetTeams($trainingsgroepquery);



      $this->generateHTML($trainingsgroepen, $teamindeling);
 
   }

   private function generateLegenda () {
      ?>
      <table class="table table-condensed">
      <thead>
         <tr>
            <td class="title">
            Legenda
            </td>
         
         </tr>
      </thead>
      <tbody>
            <tr><td class="speler Spelverdeler">Spelverdeler</td></tr>
            <tr><td class="speler Midden">Midden</td></tr>
            <tr><td class="speler Passer-loper">Passer-loper</td></tr>
            <tr><td class="speler Diagonaal">Diagonaal</td></tr>
            <tr><td class="speler Libero">Libero</td></tr>
            <tr><td class="speler Nog">Geen positie</td></tr>
            <tr><td class="speler Trainingslid">Trainingslid</td></tr>
      </tbody>
      </table>
      <?php
   }


   private function generateHTML ($trainingsgroepen, $teamindeling) {

      ?>

<html>

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous"> 
<!-- Latest compiled and minified JavaScript -->
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

<style>
   
 td.Spelverdeler {
   background-color: #d9534f!important;
 } 
 td.Midden {
   background-color: #5cb85c!important;
 } 
 td.Passer-loper {
   background-color: #f0ad4e!important;
 } 
 td.Diagonaal {
   background-color: #5bc0de!important;
 } 
 td.Libero {
   background-color: #337ab7!important;
 } 
 td.Trainingslid {
   background-color: #f5f5f5!important;
 } 
 td.Interesse {
   background-color: #FFFFFF!important;
 } 
 td.nog {
   background-color: #777777!important;
   color: white;
 } 
 td.Uitgeschreven {
   background-color: #555555!important;
   color: white;
 } 

 td.speler {
   padding: 5px;
   margin-top: 0px;
   margin-bottom: 0px; 
   font-size: 12px;
   border-top: 0px!important;
 }
 .teamrow {
   margin-bottom: 10px;
 }

 html {
   overflow-x: hidden;
 }

 body {
  -webkit-print-color-adjust:exact !important;
  print-color-adjust:exact !important;
}

td.title {
   padding-left: 0px!important;
}

@media print {
    .pagebreak { page-break-before: always; } /* page-break-after works, as well */
}
@media print {
   .no-print {
      display:none!important;
   }
}
</style>

<script>
   window.onload = (event) => {
      // window.alert(" " );
      // window.print();
      // print();
   };

</script>

<body>

<!-- Modal -->
<div class="modal fade no-print" id="myModal" role="dialog">
    <div class="modal-dialog no-print">

      <!-- Modal content-->
      <div class="modal-content no-print">
        <div class="modal-header no-print">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title"><i class="fa fa-exclamation-circle"></i>&nbsp; Exporteren</h4>
        </div>
        <div class="modal-body no-print">
         <h4>Er zijn twee opties om de teamindeling en traininsgroepen te exporteren:</h4>
          <ol>
            <li>
               Bewaar (delen van) deze pagina als PDF. (Windows: Ctrl+P) (Mac: Cmd+P). 
            </li>
            <li>
               Screenshot delen van deze pagina en combineer ze in bijv. een Word-document. 
            </li>
          </ol>
          <p>Vanwege veroudering is de Excel-print optie helaas niet meer beschikbaar. </p>
          <p><b>Vragen? Contacteer webcie@skcvolleybal.nl</b></p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Annuleren</button>
        </div>
      </div>

    </div>
</div>
<script type="text/javascript">
$(window).on('load',function(){
  $('#myModal').modal('show');
});

</script>


<div class='container'>

<?php $i = 0;
foreach ($teamindeling as $team) {

   if ($team['name'] == "Dames 1") {
      $i = 0; // 
      ?> 
   <h3>Teamindeling dames</h3>
   <div class="row">
      <div class="col-xs-4">
      <?php echo $this->generateLegenda(); ?>

      </div>
      </div>
 
      <?php
   } // Eind Damesteams legenda en titel


   if ($team['name'] == "Heren 1") {
      $i = 0; // 
      ?> 
   
   <div class="row pagebreak">

      <div class="col-xs-4">
      <h3>Teamindeling heren</h3>
      <?php echo $this->generateLegenda(); ?>
      </div>
      </div>

      <?php
   } // Eind Herenteams legenda en titel

   ?>


   <?php



   $i++;



   if ($i == 1) {
      ?>
   <div class="row">
      <?php
   }
   
   if ($i == 3) {
      ?>

      <?php
   }
   ?>

<div class="col-xs-4">

      <table class="table table-condensed teamindeling">
      <thead>
         <tr>
            <td class="title">
            <?php 
               print_r($team['name']); print_r(' (' . count($team['players']) . ')');
         ?>
            </td>
        
         </tr>
      </thead>
      <tbody>
      <?php
               foreach ($team['players'] as $speler) {
                  ?>
                  <tr>
                     <td  class="speler <?php echo $speler['type']; ?>"> <?php echo $speler['name']; ?> </td>
                     </td>
                  </tr>
               
                  <?php
               }
               ?>
      </tbody>
      </table>

      </div> 

      <?php 

      if ($i == 3) {
         $i = 0;
         ?>
   </div> <!-- Close row -->
   
         <?php
      }

} //endforeach
      ?>


<!-- Traininsgroepen! -->


<?php $i = 0;
foreach ($trainingsgroepen as $trainingsgroep) {

   if ($trainingsgroep['name'] == "Dames A") {
      $i = 0; // 
      ?> 
   <div class="row pagebreak">
      
   
      <div class="col-xs-4">
      <h3>Trainingsgroepen dames</h3>
      <?php echo $this->generateLegenda(); ?>

      </div>
      </div>
 
      <?php
   } // Eind Dames traininsgroepen legenda en titel


   if ($trainingsgroep['name'] == "Heren A") {
      $i = 0; // 
      ?> 
   </div>
   <div class="row pagebreak">
   

      <div class="col-xs-4">
      <h3> Trainingsgroepen heren</h3>
      <?php echo $this->generateLegenda(); ?>
      </div>
      </div>

      <?php
   } // Eind Heren traininsgroepen legenda en titel

   ?>


   <?php



   $i++;



   if ($i == 1) {
      ?>
   <div class="row">
      <?php
   }
   
   if ($i == 3) {
      ?>

      <?php
   }
   ?>

<div class="col-xs-4">

      <table class="table table-condensed">
      <thead>
         <tr>
            <td class="title">
            <?php 
               echo($trainingsgroep['name']); 
               echo(' (' . count($trainingsgroep['players']) . ')');
         ?>
            </td>
         
         </tr>
      </thead>
      <tbody>
      <?php
               foreach ($trainingsgroep['players'] as $speler) {
                  ?>
                  <tr>
                     <td  class="speler <?php echo $speler['type']; ?>"> <?php echo $speler['name']; ?> </td>
                     </td>
                  </tr>
               
                  <?php
               }
               ?>
      </tbody>
      </table>

      </div> 

      <?php 

      if ($i == 3) {
         $i = 0;
         ?>
   </div> <!-- Close row -->
   
         <?php
      }

} //endforeach
      ?>



<!-- Modal stuff from here -->



        
</body>
</html>
   <?php
      } // Close generatehtml
   
 


}



$database = new Database();

$tcApp = new TcApp();
$tcApp->InitJoomla();
$user = $tcApp->GetUser();
$tcApp->CheckForTcRights($user);

$excelExport = new ExcelExport($database);

$excelExport->makeExports();
 
