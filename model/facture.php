<?php

class facture
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

    public function getFactureById($id)
    {
        $sql = "SELECT * FROM facture WHERE id_intervention = :id";
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

    public function deleteFactureById($id)
    {
        $sql = "DELETE FROM facture WHERE id_intervention = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }
}
