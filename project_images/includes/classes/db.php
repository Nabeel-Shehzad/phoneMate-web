<?php
// Database class

class DB {
    public $conn;
    // private const SERVER = "localhost";
    // private const USER = "root";
    // private const PASSWORD = "";
    // private const DB = "wire_project";
    private const SERVER = "localhost";
    private const USER = "u650672385_PhoneMate";
    private const PASSWORD = "PhoneMate123!@#";
    private const DB = "u650672385_PhoneMate";

    public function __construct()
    {
        $this->connect();
    }

    // connecting to the database
    private function connect () {
        $this->conn = new mysqli(self::SERVER, self::USER, self::PASSWORD, self::DB);
        if ($this->conn->connect_errno){
            throw new Exception("Error connecting to the database: " . $this->conn->connect_error);
        }
    }

    // passing query to the database
    public function query ($query) {
        $result = $this->conn->query($query);
        if (!$result) {
            throw new Exception("Problem with query: ". $this->conn->error);
        }else{
            return $result;
        }
    }

    // escaping data
    public function escape ($string) {
        $escaped_string = $this->conn->escape_string($string);
        return $escaped_string;
    }

    // getting the last insert/update id of executed query
    public function last_id () {
        return $this->conn->insert_id;
    }

    // closing the db connection
    // public function __destruct() {
    //     if ($this->conn) {
    //         $this->conn->close();
    //     }
    // }
}

$db = new DB();