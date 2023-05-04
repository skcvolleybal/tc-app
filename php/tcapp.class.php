<?php
require '../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

class TcApp
{

   public function InitJoomla()
   {

      define('_JEXEC', 1);

      define('JPATH_BASE', realpath(__DIR__ . '/../..'));

      require_once(JPATH_BASE . '/includes/defines.php');
      require_once(JPATH_BASE . '/includes/framework.php');

      $mainframe = JFactory::getApplication('site');

      \Sentry\init(['dsn' => 'https://087b634fd12e49fd80fdb70d4d272f3e@o4504883122143232.ingest.sentry.io/4504884671676416',
      'environment' => $_ENV['ENVIRONMENT'] ]);

      $mainframe->initialise();
   }

   public function GetUser()
   {
      $session = JFactory::getSession();
      $user = JFactory::getUser();
      if ($user->guest) {
         throw new AuthenticationException("Je bent niet (meer) ingelogd!");
      }
      return $user;
   }

   public function CheckForTcRights($user)
   {
      if (empty($user)) {
         throw new Exception("Gebruiker is null...");
      }
      // only allow TC members
      if (!array_key_exists(46, $user->{'groups'})) {
         throw new Exception("Je hebt niet de benodigde rechten, " . $user->name);
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
