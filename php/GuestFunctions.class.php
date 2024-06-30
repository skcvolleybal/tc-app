<?php

class GuestActions
{

   private $lists;

   public function __construct($lists)
   {
      $this->lists = $lists;
   }

   public function Login($postObject)
   {
      $credentials = [
         'user_login' => $postObject['username'],
         'user_password' => $postObject['password'],
         'rememberme' => true
      ];

      $result = wp_signon($credentials, true); // true - use HTTP only cookie

      if ($result instanceof WP_Error) {
         $errorMessage = $result->get_error_message();
         throw new Exception($errorMessage);
      } else {
         $user_nicename = $result->data->user_nicename;

         // SimpleLogger: https://wordpress.org/plugins/simple-history/
         if (function_exists("SimpleLogger")) {
            SimpleLogger()->info("User $user_nicename logged into TC-app");
         }
         return ['message' => "Welkom bij TC-app $user_nicename", 'dataLists' => $dataLists];
      }

   }

   public function CheckIfuserIsLoggedIn()
   {
      $wpuser = wp_get_current_user();
      $dataLists = $this->lists->GetLists();

      return ['userIsloggedIn' => $wpuser->ID != 0, 'dataLists' => $dataLists];
   }
}
