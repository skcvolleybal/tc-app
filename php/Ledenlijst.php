<?php
// error_reporting(E_ALL);
// ini_set("display_errors", 0);

require_once("database.class.php");
require_once("tcapp.class.php");


class LedenLijst
{
   private $database;


   public function __construct($database)
   {
      $this->database = $database;
   }

   public function GetLedenlijst()
   {

      ?>

      <html>
         <head>
         <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous"> 

         <link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
         <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.2/css/buttons.dataTables.min.css">

         <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
         <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
         <script src="https://cdn.datatables.net/buttons/2.3.2/js/dataTables.buttons.min.js"></script>
         <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
         <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
         <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
         <script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.html5.min.js"></script>
         <script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.print.min.js"></script>
         <script>
            $(document).ready(function() {
    $('#ledenlijst').DataTable( {
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ],
        "lengthMenu": [1000]
    } );
} );
         </script>
        
         </head>
         <body>

         <div class="container">

         <div class="col-md-12">

        

      <table id="ledenlijst" class="display nowrap" style="width:100%">
         <thead>
            <tr>
               <td>
                  #
               </td>
               <td>
                  Naam
               </td>
               <td>
                  Team
               </td>
               <td>
                  Trainingsgroep
               </td>
               <td>
                  Positie
               </td>
            </tr>
         </thead>
         <tbody>
            

      <?php

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
      $counter = 0; 

      foreach ($result as $row) {
         $counter++;
         ?>

         <tr>    

         <td>
         <?php echo $counter; ?>
         </td>
         <td>
            <?php echo $row['name']; ?>
         </td>
         <td>
         <?php echo $row['team']; ?>
         </td>
         <td>
         <?php echo $row['training']; ?>
         </td>
         <td>
         <?php echo $row['type']; ?>
         </td>
         


         </tr>
         <?php 
      }
      ?>

</div>
         </div>

      </tbody>
      </table>
      </body>
      </html>

 
      <?php
   }
}

$database = new Database();

$tcApp = new TcApp();
$tcApp->InitWordpress();
$user = $tcApp->GetUser();
$tcApp->CheckForTcRights($user);

$excelExport = new LedenLijst($database);

$excelExport->GetLedenlijst();

