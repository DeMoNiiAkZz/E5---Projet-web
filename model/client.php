<?php

class client
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

    public function getAllClients()
    {
        $sql = "SELECT * FROM utilisateur WHERE id_type = 3";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    public function rechercherClients($query)
    {
        $sql = "SELECT * FROM utilisateur WHERE id_type = 3 AND (email = :query OR telephone = :query)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':query', $query, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
