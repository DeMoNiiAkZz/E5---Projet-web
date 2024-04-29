<?php

class citations
{

    private $pdo;

    public function __construct()
    {
        $config = parse_ini_file("config.ini");

        try {
            $this->pdo = new \PDO("mysql:host=" . $config["host"] . ";dbname=" . $config["db"] . ";charset=utf8", $config["login"], $config["password"]);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    private function urlchemin()
    {
        $config = parse_ini_file("config.ini");
        return $config['path'];
    }
    public function getCitations()
    {
        $sql = "SELECT * FROM citations";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function addCitation($citation)
    {
        $sql = "INSERT INTO citations (citation) VALUES (:citation)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':citation', $citation, PDO::PARAM_STR);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    
}
