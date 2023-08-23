<?php
   require_once("param.class.php");

   require '../vendor/autoload.php';

   use Dotenv\Dotenv;
   use Dotenv\Exception\InvalidPathException;


   class Database {
      private $dbc = null;

      public function executeQuery($query, $params = array())
      {
         if (empty($query))
         {
            throw new Exception("Query is empty");
         }

         $stmt = $this->getDbc()->prepare($query);
         foreach ($params as $param)
         {
            $stmt->bindValue($param->getName(), $param->getValue(), $param->getType());
         }

         if (!$stmt->execute())
         {
            $message = "query:\n" . print_r($query, true) . "\n\nparams:\n" . print_r($params, true);
            throw new Exception("Fout bij het uitvoeren van query (" . $message . ") " . print_r($stmt->errorInfo(), true) . "  om " . date('H:i:s:(u) d-m-Y'));
         }

         if (0 === strpos(strtoupper($query), 'INSERT INTO')) {
            return $this->dbc->lastInsertId();
         }
         return $stmt->fetchAll();
      }

      public function getDbc(){
         if ($this->dbc != null){
            return $this->dbc;
         }

         // require_once(JPATH_BASE . "/configuration.php");
         // $config = new JConfig();
         $host = $_ENV['DB_HOST'];
         $db = $_ENV['DB_NAME'];
         $user = $_ENV['DB_USER'];
         $password = $_ENV['DB_PWD'];
         $this->dbc = new PDO("mysql:host=$host;dbname=$db", $user, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
         return $this->dbc;
      }
   }
   