<?php
   class PlayerFunctions {
      private $database;
      private $listActions;

      public function __construct($database, $listActions){
         $this->database = $database;
         $this->listActions = $listActions;
      }

      private function GetAllPlayersUsingQuery($query, $teamColumn){
         $players = $this->database->executeQuery($query);

         $mapping = [];
         $currentTeam = null;
         foreach ($players as $player){
            $teamId = intval($player[$teamColumn]);
            if ($currentTeam != $player['teamName']){
               $currentTeam = $player['teamName'];
               $mapping[] = [
                  'id' => $teamId,
                  'name' => $currentTeam,
                  'sequence' => $player['sequence'],
                  'players' => []
               ];
               
            }

            if ($player['id'] != null){
               $mapping[count($mapping) - 1]['players'][] = [
                  'id' => $player['id'],
                  'name' => $player['name'],
                  'typeId' => $player['typeId'],
                  'interesseId' => $player['interesseId'],
                  'teamId' => $player['teamId'],
                  'trainingId' => $player['trainingId'],
                  'information' => $player['information']
               ];
            }
         }

         return $mapping;
      }

      public function GetAllPlayers(){
         $query = "select 
	                   T.name as teamName,
                      T.id as teamId,
                      T.sequence as sequence,
                      P.training_id as trainingId,
	                   P.id,
                      P.name,
                      P.information,
                      P.type_id as typeId,
                      P.interesse_id as interesseId
                   from tcapp_teams T
                   left join tcapp_players P on P.team_id = T.id
                   where T.type = 'team'
                   order by T.sequence, P.type_id, P.name";
         $teamMapping = $this->GetAllPlayersUsingQuery($query, "teamId");

         $query = "select 
                     T.name as teamName,
                     T.id as trainingId,
                     T.sequence as sequence,
                     P.team_id as teamId,
                     P.id,
                     P.name,
                     P.information,
                     P.type_id as typeId,
                     P.interesse_id as interesseId
                   from tcapp_teams T
                   left join tcapp_players P on P.training_id = T.id
                   where T.type = 'training'
                   order by T.sequence, P.type_id, P.name";
         $trainingGroupMapping = $this->GetAllPlayersUsingQuery($query, "trainingId");

         $dataLists = $this->listActions->GetLists();

         return [
            'teamMapping' => $teamMapping, 
            'trainingGroupMapping' => $trainingGroupMapping, 
            'trainingGroups' => $dataLists['trainingGroups'],
            'teams' => $dataLists['teams'],
            'playerTypes' => $dataLists['playerTypes']
         ];
      }

      public function GetPlayer($postObject){
         $playerId = $postObject['playerId'];

         $query = "select 
                     player.id, 
                     player.name, 
                     type.name as type,
                     interesse.interesse_name as interesse,
                     training.name as training,
                     team.name as team,
                     information
                   from tcapp_players player
                   left join tcapp_teams team on player.team_id = team.id
                   left join tcapp_teams training on player.training_id = training.id
                   left join tcapp_player_types type on player.type_id = type.id
                   left join tcapp_interesse_types interesse on player.interesse_id = interesse.id
                   where player.id = :playerId";
         $params = [new Param(":playerId", $playerId, PDO::PARAM_INT)];
         $result = $this->database->executeQuery($query, $params);

         if (count($result) == 1){
            $player = $result[0];
            return [
               "id" => $player['id'],
               "name" => $player['name'],
               "type" => $player['type'],
               "interesse" => $player['interesse'],
               "team" => $player['training'],
               "training" => $player['team'],
               "information" => $player['information']
            ];
         }
         else {
            throw new Exception("Speler bestaat niet");
         }
      } 

      public function FindPlayers($postObject){
         $searchString = $postObject['searchString'];

         $query = "select * from tcapp_players where name like :searchString";
         $params = [
            new Param(":searchString", ("%" . $searchString . "%"), PDO::PARAM_STR)
         ];

         $result = $this->database->executeQuery($query, $params);

         if (count($result) > 0){
            return ['playerList' => $result];
         }
         else {
            return ['message' => "Geen spelers met '$searchString' gevonden"];
         }
      }

      public function UpdatePlayerInformation($postObject){
         $id = $postObject['id'];
         $information = $postObject['information'];

         $information = empty($information) ? null : $information;

         $query = "update tcapp_players set information = :information where id = :id";
         $params = [
            new Param(":information", $information, PDO::PARAM_STR),
            new Param(":id", $id, PDO::PARAM_INT)
         ];

         $this->database->executeQuery($query, $params);

         return ['message' => "Opgeslagen"];
      }

      public function AddPlayer($name, $teamId, $trainingId, $typeId){
         if (empty($name)){
            throw new Exception("Vul de naam in...");
         }

         if (empty($teamId)){
            throw new Exception("Geen team gevonden");
         }

         if (empty($trainingId)){
            throw new Exception("Geen trainingsgroep gevonden");
         }

         if (empty($typeId)){
            throw new Exception("Geen spelers type gevonden");
         }

         // For now keep interesseId as geen, TODO
         $interesseId = 8;

         $query = "insert into tcapp_players (name, team_id, training_id, type_id, interesse_id) values (:name, :teamId, :trainingId, :typeId, :interesseId)";
         $params = [
            new Param(":name", $name, PDO::PARAM_STR),
            new Param(":teamId", $teamId, PDO::PARAM_INT),
            new Param(":trainingId", $trainingId, PDO::PARAM_INT),
            new Param(":typeId", $typeId, PDO::PARAM_INT),
            new Param(":interesseId", $interesseId, PDO::PARAM_INT)
         ];
         $playerId = $this->database->executeQuery($query, $params);

         return $playerId;
      }

      public function UpdatePlayer($id, $teamId, $trainingId, $typeId, $interesseId){
         $query = "update tcapp_players set team_id = :teamId, training_id = :trainingId, type_id = :typeId, interesse_id = :interesseId where id = :id";
         $params = [
            new Param(":teamId", $teamId, PDO::PARAM_INT),
            new Param(":trainingId", $trainingId, PDO::PARAM_INT),
            new Param(":typeId", $typeId, PDO::PARAM_INT),
            new Param(":interesseId", $interesseId, PDO::PARAM_INT),
            new Param(":id", $id, PDO::PARAM_INT)
         ];
         $this->database->executeQuery($query, $params);
      }

      public function GetUpdatedPlayers($postObject){
         $timestamp = $postObject['timestamp'];

         $query = "select * from tcapp_players where date_modified >= :timestamp";
         $params = [
            new Param(":timestamp", $timestamp, PDO::PARAM_STR)
         ];

         $changes = $this->database->executeQuery($query, $params);

         $result = [];
         foreach ($changes as $change){
           $result[] = [
             "id" => $change["id"],
             "name" => $change["name"],
             "teamId" => $change["team_id"],
             "trainingId" => $change["training_id"],
             "information" => $change["information"],
             "typeId" => $change["type_id"]
           ];
         }
         return ['changes' => $result];
      }

      public function DeletePlayer($playerId){
         if(empty($playerId)){
            throw new Exception("$playerId playerId not set");
         }

         $query = "delete from tcapp_players where id = :playerId";
         $params = [
            new Param(":playerId", $playerId, PDO::PARAM_INT)
         ];
         $this->database->executeQuery($query, $params);

         return ['message' => 'Succesvol verwijderd'];
      }
   }