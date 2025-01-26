<?php

class Database
{
    private static $instance = null;
    private $connection;
    private function __construct()
    {
        $host = '193.203.166.123';
        $dbname = 'u498377835_tomas_nalezy';
        $username = 'u498377835_tomas_nalezy';
        $password = 'TomasRizekRolexStul9.';

        // Create the MySQL connection
        $this->connection = new mysqli($host, $username, $password, $dbname);

        // Check for connection errors
        if ($this->connection->connect_error) {
            die("Connection failed: " . $this->connection->connect_error);
        }
    }

    // Get the singleton instance
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    // Function to execute a query
    public function query($sql)
    {
        return $this->connection->query($sql);
    }

    // Escape strings to prevent SQL injection
    public function escape($string)
    {
        return $this->connection->real_escape_string($string);
    }

    // Close the connection
    public function close()
    {
        $this->connection->close();
    }
}

// Login function
function get_nalezy()
{
    $db = Database::getInstance();

    $sql = "SELECT * FROM nalezy";
    $result = $db->query($sql);

    $nalezy = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $nalezy[] = $row;
        }
    }

    return $nalezy;
}

function get_nalez_by_id($id) {
    $db = Database::getInstance();

    $sql = "SELECT * FROM nalezy WHERE id = $id LIMIT 1";
    $result = $db->query($sql);

    if ($result->num_rows > 0) {
        return  $result->fetch_assoc();
    }

    return null;
}

function add_nalez($nazev, $popis, $poloha, $typ, $material, $datum, $foto_url)
{
    $db = Database::getInstance();

    // Query the database
    $sql = "INSERT INTO nalezy (nazev, popis, poloha, typ, material, datum, foto_url) values ('$nazev', '$popis', '$poloha', '$typ', '$material', '$datum', '$foto_url')";
    $db->query($sql);
}

function delete_nalez($id)
{
    $db = Database::getInstance();

    // Query the database
    $sql = "DELETE FROM nalezy WHERE id = $id";
    $db->query($sql);
}
?>