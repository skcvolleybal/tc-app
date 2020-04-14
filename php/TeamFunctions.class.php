<?php
   class TeamFunctions{
      private $dbc;

      public function __construct($database) {
         $this->database = $database;
      }

      public function AddTeam($name, $type, $sequence){
         if (empty($name)){
            throw new Exception("Geen goede teamnaam");
         }

         if (empty($type)){
            throw new Exception("Geen goede team type");
         }

         if (is_int($sequence)){
            throw new Exception("Geen goede sequence");
         }

         $query = "insert into tcapp_teams (type, name, sequence) values (:type, :name, :sequence)";
         $params = [
            new Param(":type", $type, PDO::PARAM_STR),
            new Param(":name", $name, PDO::PARAM_STR),
            new Param(":sequence", $sequence, PDO::PARAM_INT)
         ];
         $teamId = $this->database->executeQuery($query, $params);

         return $teamId;
      }

      public function DeleteTeam($teamId){
         $query = "delete from tcapp_teams 
                   where id = :teamId and 
                         name != 'Geen Trainingsgroep' and 
                         name != 'Geen Team'";
         $params = [
            new Param(":teamId", $teamId, PDO::PARAM_INT)
         ];
         $this->database->executeQuery($query, $params);
      }

      public function UpdateTeam($teamId, $sequence){
         if (empty($sequence)){
            throw new Exception("Geen goede sequence: '$sequence'");
         }

         $query = "update tcapp_teams set sequence = :sequence where id = :teamId";
         $params = [
            new Param(":sequence", $sequence, PDO::PARAM_INT),
            new Param(":teamId", $teamId, PDO::PARAM_INT)
         ];
         $this->database->executeQuery($query, $params);
      }

      public function GetTrainingInfo($postObject){
         $teamType = $postObject['teamType'];
         $query = "select id, name, training_info as trainingInfo 
                   from tcapp_teams 
                   where type = :teamType and 
                         name != 'Geen Team' and 
                         name != 'Geen Trainingsgroep' 
                   order by sequence";
         $params = [
            new Param(":teamType", $teamType, PDO::PARAM_STR)
         ];
         $result = $this->database->executeQuery($query, $params);

         return ['teams' => $result];
      }

      public function SaveTrainingInfo($postObject){
         $teams = $postObject['teams'];

         foreach ($teams as $team){
            $query = "update tcapp_teams 
                      set training_info = :trainingInfo
                      where id = :id";
            $params = [
               new Param(":id", $team->id, PDO::PARAM_INT),
               new Param(":trainingInfo", $team->trainingInfo, PDO::PARAM_STR)
            ];

            $this->database->executeQuery($query, $params);
         }

         return ['message' => 'Opgeslagen'];
      }
   }