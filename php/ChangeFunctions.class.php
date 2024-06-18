<?php
   class ChangeFunctions {
      private $playerFunctions;
      private $teamFunctions;
      
      public function __construct($playerFunctions, $teamFunctions){
         $this->playerFunctions = $playerFunctions;
         $this->teamFunctions = $teamFunctions;
      }

      private function GetRightId($id, $ids){
         if ($id > 0){
            return $id;
         }
         else {
            foreach ($ids as $oldId => $newId){
               if ($id == $oldId){
                  return intval($newId);
               }
            }

            throw new Exception("Unknown id: $id");
         }
      }

      public function SaveChanges($postObject){
         $changes = $postObject['changes'];

         $ids = [
            'playerIds' => [],
            'teamIds' => []
         ];

         foreach ($changes as $change){
            switch ($change->changeType){
               case "addTeam": 
                  $teamId = $this->teamFunctions->AddTeam($change->name, $change->type, $change->sequence);
                  $ids['teamIds'][$change->id] = $teamId;
                  break;
               case "updateTeam":
                  $teamId = $this->GetRightId($change->id, $ids['teamIds']);
                  $this->teamFunctions->UpdateTeam($teamId, $change->sequence);
                  break;
               case "deleteTeam":
                  $teamId = $this->GetRightId($change->id, $ids['teamIds']);
                  $this->teamFunctions->DeleteTeam($teamId);
                  break;
               case "addPlayer":
                  $teamId = $this->GetRightId($change->teamId, $ids['teamIds']);
                  $trainingId = $this->GetRightId($change->trainingId, $ids['teamIds']);
                  $playerId = $this->playerFunctions->AddPlayer($change->name, $teamId, $trainingId, $change->typeId);
                  $ids['playerIds'][$change->id] = $playerId;
                  break;
               case "updatePlayer":
                  $playerId = $this->GetRightId($change->id, $ids['playerIds']);
                  $teamId = $this->GetRightId($change->teamId, $ids['teamIds']);
                  $trainingId = $this->GetRightId($change->trainingId, $ids['teamIds']);
                  $this->playerFunctions->UpdatePlayer($playerId, $teamId, $trainingId, $change->typeId, $change->interesseId);
                  break;
               case "deletePlayer":
                  $playerId = $this->GetRightId($change->id, $ids['teamIds']);
                  $this->playerFunctions->DeletePlayer($playerId);
                  break;
               default:
                  throw new Exception("Unknown changeType: " . $change->changeType);
            }
         }

         return ['message' => 'Opgeslagen', 'ids' => $ids];
      }
   }