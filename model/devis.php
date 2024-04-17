<?php

class devis
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
        return "C:/wamp64/www/LCS_Dash/";
    }

    public function getDevisById($id)
    {
        $sql = "SELECT * FROM devis WHERE id_intervention = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $rowCount = $stmt->rowCount();

        if ($rowCount > 1) {
            return true; 
        } else {
            return false; 
        }
    }

    public function deleteDevisbyId($id)
    {
        $sql = "DELETE FROM devis WHERE id_intervention = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }
}
