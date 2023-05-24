<?php
   class GuestActions{
      private $lists;

      public function __construct($lists) {
         $this->lists = $lists;
      }

      public function Login($postObject){
         // $credentials = [
         //    'username' => $postObject['username'],
         //    'password' => $postObject['password']
         // ];

         $result = wp_authenticate($postObject['username'], $postObject['password']);

         if ($result instanceof WP_Error) {
            throw new Exception("Kan niet inloggen in WordPress");
         }
         else {
            $user_email = $result->data->user_email;
            return ['message' => "Welkom bij WordPress $user_email", 'dataLists' => $dataLists];
         }
         

         die();


         // $joomlaApp = JFactory::getApplication('site');
         
         // $db = JFactory::getDbo();
         // $query = $db->getQuery(true)
         //    ->select('id, password')
         //    ->from('#__users')
         //    ->where('username=' . $db->quote($credentials['username']));
      
         // $db->setQuery($query);
         // $result = $db->loadObject();
         // if ($result) {
         //    $match = JUserHelper::verifyPassword($credentials['password'], $result->password, $result->id);
         //    if ($match === true) {
         //       $joomlaApp->login($credentials);
         //       $dataLists = $this->lists->GetLists();
         //       return ['message' => "Opnieuw ingelogd", 'dataLists' => $dataLists];
         //    }
         //    else {      
         //       throw new Exception('Fout wachtwoord, probeer het nog eens');
         //    }
         // } else {
         //    throw new Exception("Gebruiker '" . $credentials['username'] . "' bestaat niet");
         // }
      }

      public function CheckIfuserIsLoggedIn(){
         $wpuser = wp_get_current_user();
         // $user = JFactory::getUser(); Joomla code
         $dataLists = $this->lists->GetLists();
         
         // return ['userIsloggedIn' => $user->id != 0, 'dataLists' => $dataLists]; Joomla code

         return ['userIsloggedIn' => $wpuser->ID != 0, 'dataLists' => $dataLists];
      }
   }