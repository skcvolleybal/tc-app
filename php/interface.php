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
      
      // InitWordpress loads Wordpress classes, necessary for authentication and authorization
      $tcApp->InitWordpress();

      $postObject = $tcApp->getPostedJsonValues();
   
      $action = $postObject['action'];
      if (isset($action['action']) || empty($postObject['action'])){
         $tcApp->returnError("'action' not filled out");
      }

      $ListActions = new ListActions($database);

      // GuestActions class is contained in the GuestFunctions.php file
      // This class contains the CheckIfUserIsloggedin function. If the user is not logged in, the login popup is shown. 
      $guestActions = new GuestActions($ListActions);
      if (method_exists($guestActions, $action)){
         $result = $guestActions->{$action}($postObject);
         $tcApp->returnSuccess($result);
      }

      // At this point, the user should be logged in. We check this and also if the user has TC rights. 
      // If this fails, an AuthenticationException is thrown (as can be seen further below)
      $user = $tcApp->getUser();
      $tcApp->CheckForTcRights($user);
   
      // From here, we assume the user is logged in and has proper TC rights. 
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