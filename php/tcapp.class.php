<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require '../vendor/autoload.php';

use Dotenv\Dotenv;
use Dotenv\Exception\InvalidPathException;


class TcApp
{

   protected $wordpressPath;


   public function __construct()
   {
      try {
         $dotenv = Dotenv::createImmutable(dirname(__DIR__));
         $dotenv->load();
      } catch (InvalidPathException $e) {
         $this->returnError("Could not find the .env file!");
      }

      \Sentry\init([
         'dsn' => 'https://087b634fd12e49fd80fdb70d4d272f3e@o4504883122143232.ingest.sentry.io/4504884671676416',
         'environment' => $_ENV['ENVIRONMENT']
      ]);
   }

   public function InitWordpress()
   {
      $this->wordpressPath = $_ENV['WORDPRESS_PATH'];
      require_once $this->wordpressPath . '/wp-load.php';
   }

   public function GetUser()
   {
      $wploggedin = is_user_logged_in();
      $wpuser = wp_get_current_user();

      if (!$wploggedin) {
         throw new AuthenticationException("Je bent niet (meer) ingelogd!");
      }
      return $wpuser;
   }

   public function CheckForTcRights($user)
   {
      if (empty($user)) {
         throw new Exception("Gebruiker is null...");
      }

      // Check if user has the TC WordPress role
      if ($user->caps['tc'] == true) {
      } else {
         throw new Exception("Je hebt niet de benodigde rechten, " . $user->data->user_nicename);
      }
   }

   public function getPostedJsonValues()
   {
      $postData = file_get_contents("php://input");
      if (empty($postData)) {
         throw new Exception("Empty post data");
      }

      $result = [];
      $request = json_decode($postData);
      foreach ($request as $param_name => $param_val) {
         $result[$param_name] = is_string($param_val) ? trim($param_val) : $param_val;
      }

      return $result;
   }

   public function returnError($message)
   {
      header("HTTP/1.0 500 Internal Server Error");
      header('Content-Type: application/json');
      exit(json_encode(array("errorMessage" => $message, "POST" => print_r($_POST, true))));
   }

   public function returnSuccess($outputArray = [])
   {
      header("HTTP/1.0 200 OK");
      $outputArray['status'] = "success";
      exit(json_encode($outputArray));
   }

   public function returnAuthenticationError()
   {
      $message = "Je bent (automatisch) uitgelogd.<br \><br \>"
         . "Klik op het Login-icoontje links in het menu.<br \><br \>"
         . "Je wijzigingen zijn niet kwijt.<br \><br \>"
         . "Je kunt je wijzigingen daarna nog een keer opslaan.";
      $this->returnError($message);
   }
}

class AuthenticationException extends Exception
{
   public function __construct($message, $code = 0, Exception $previous = null)
   {
      parent::__construct($message, $code, $previous);
   }
}
