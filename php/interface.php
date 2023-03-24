<?php
   // error_reporting(E_ALL);
   // ini_set("display_errors", 1);



   require_once("tcapp.class.php");
   require_once("database.class.php");
   require_once("logging.class.php");

   require_once("GuestFunctions.class.php");
   require_once("TeamFunctions.class.php");
   require_once("PlayerFunctions.class.php");
   require_once("ListActions.class.php");
   require_once("ChangeFunctions.class.php");

   try {
      
      
      $database = new Database();
      
      $tcApp = new TcApp($database);
      
      $tcApp->InitJoomla();

      $postObject = $tcApp->getPostedJsonValues();
   
      $action = $postObject['action'];
      if (isset($action['action']) || empty($postObject['action'])){
         $tcApp->returnError("'action' not filled out");
      }

      $ListActions = new ListActions($database);

      $guestActions = new GuestActions($ListActions);
      if (method_exists($guestActions, $action)){
         $result = $guestActions->{$action}($postObject);
         $tcApp->returnSuccess($result);
      }

      $user = $tcApp->getUser();
      $tcApp->CheckForTcRights($user);
   
      $teamFunctions = new TeamFunctions($database);
      if (method_exists($teamFunctions, $action)){
         $result = $teamFunctions->{$action}($postObject);
         $tcApp->returnSuccess($result);
      }

      $playerFunctions = new PlayerFunctions($database, $ListActions);
      if (method_exists($playerFunctions, $action)){
         $result = $playerFunctions->{$action}($postObject);
         $tcApp->returnSuccess($result);
      }

      $changeFunctions = new ChangeFunctions($playerFunctions, $teamFunctions);
      if (method_exists($changeFunctions, $action)){
         $result = $changeFunctions->{$action}($postObject);
         $tcApp->returnSuccess($result);
      }
   }
   catch(AuthenticationException $e){
      $tcApp->returnAuthenticationError();
   }
   catch(Exception $e){
      $tcApp->returnError($e->getMessage());
   }

   $tcApp->returnError("Unknown Action '$action'");
   
?>