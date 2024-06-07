<?php
class FetchUpdate
{
    private $database;
    private $listActions;

    public function __construct($database, $listActions)
    {
        $this->database = $database;
        $this->listActions = $listActions;
    }

    private function fetchUpdate()
    {
        $this->database->query();





    }
}

