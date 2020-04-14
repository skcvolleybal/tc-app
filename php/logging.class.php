<?php
   class Logging {
      public function __construct($dbc){
         $this->dbc = $dbc;
      }

      // Return the full call stack of the error
      private function debugStringBacktrace()
      {
         ob_start();
         debug_print_backtrace();
         $trace = ob_get_contents();
         ob_end_clean();
         $trace = preg_replace ('/^#0\s+' . __FUNCTION__ . "[^\n]*\n/", '', $trace, 1);
         $trace = preg_replace ('/^#(\d+)/me', '\'#\' . ($1 - 1)', $trace);
         return $trace;
      }

      // Log the error with the stacktrace
      private function logError($errorMessage)
      {
         $callStack = $this->debugStringBacktrace();
         $query = "INSERT INTO log (application, error_message, call_stack) VALUES ('BeackCompetitie', :errorMessage, :callStack)";
         $this->executeQuery($query, array(new Param(":errorMessage", $errorMessage, PDO::PARAM_STR), new Param(":callStack", $callStack, PDO::PARAM_STR)));
      }
   }