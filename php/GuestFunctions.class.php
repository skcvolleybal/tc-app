<?php

   class GuestActions{

      private $lists;

      public function __construct($lists) {
         $this->lists = $lists;   
      }

      public function Login($postObject){
         $credentials = [
            'user_login' => $postObject['username'],
            'user_password' => $postObject['password'],
            'rememberme' => true
         ];

         $result = wp_signon($credentials, true); // true - use HTTP only cookie

         if ($result instanceof WP_Error) {
            $errorMessage = $result->get_error_message();
            throw new Exception($errorMessage);
         }
         else {
            $user_email = $result->data->user_email;
            return ['message' => "Welkom bij TC-app $user_email", 'dataLists' => $dataLists];
         }
         

         die();


      }

      public function CheckIfuserIsLoggedIn(){
         $wpuser = wp_get_current_user();
         $dataLists = $this->lists->GetLists();

         return ['userIsloggedIn' => $wpuser->ID != 0, 'dataLists' => $dataLists];
      }
   }