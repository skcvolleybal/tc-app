<?php
   class ListActions {
      public function __construct($database) {
         $this->database = $database;
      }

      public function GetLists(){
         $query = "select id, name, type, sequence from tcapp_teams where type = 'training' order by sequence";
         $trainingsgroepen = $this->database->executeQuery($query);

         $query = "select id, name, type, sequence from tcapp_teams where type = 'team' order by sequence";
         $teams = $this->database->executeQuery($query);

         $query = "select * from tcapp_player_types order by id";
         $playerTypes = $this->database->executeQuery($query);

         return ['trainingGroups' => $trainingsgroepen, 'teams' => $teams, 'playerTypes' => $playerTypes];
      }
   }