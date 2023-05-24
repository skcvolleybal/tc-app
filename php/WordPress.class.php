<?php

require '../vendor/autoload.php';

use Dotenv\Dotenv;
use Dotenv\Exception\InvalidPathException;


class WordPressInterface
{

    private string $token;
    protected $wordpressPath;


    public function __construct()
    {
        try {
            $dotenv = Dotenv::createImmutable(dirname(__DIR__));
            $dotenv->load();
        } catch (InvalidPathException $e) {
            $this->returnError("Could not find the .env file!");
        }

        $this->wordpressPath = $_ENV['WORDPRESS_PATH'];
        require_once $this->wordpressPath . '/wp-load.php';

        $this->token = $this->getToken();

    }

    private function getToken() : string 
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $_ENV['WORDPRESS_URL'] . '/wp-json/jwt-auth/v1/token',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'username=' . $_ENV['WORDPRESS_REST_API_USERNAME'] . '&password=' . $_ENV['WORDPRESS_REST_API_PASSWORD'] . ' ' ,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($response);

        $JWTWebtoken = (object) array(
            'token' => $response->token,
            'user_email' => $response->user_email,
            'user_nicename' => $response->user_nicename,
            'user_display_name' => $response->user_display_name
        );

        return $JWTWebtoken->token;
    }

    

    public function checkUserLoggedIn() {
        // Check if the user is logged in
        if (is_user_logged_in()) {
            // User is logged in
            return true;
        } else {
            // User is not logged in
            return false;
        }
    }

    public function getCurrentUser() {
        $user = wp_get_current_user();
        return $user;
    }

    

}




$wpInterface = new WordPressInterface();
var_dump($wpInterface->getCurrentUser());
