<?php

class signalement
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

    public function getSignalementNonValide()
    {
        $sql = "SELECT *, utilisateur.nom, utilisateur.prenom
        FROM signalement 
        INNER JOIN utilisateur ON utilisateur.id_utilisateur = signalement.id_technicien
        WHERE traiter = 0";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    public function updateSignalement($id_signalement)
    {
        $traiter = 1;
        foreach ($id_signalement as $id) {
            $sql = "UPDATE signalement SET traiter = :traiter WHERE id_signalement = :id_signalement";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':traiter', $traiter, PDO::PARAM_INT);
            $stmt->bindParam(':id_signalement', $id, PDO::PARAM_INT);
            if (!$stmt->execute()) {
                return false;
            }
        }
        return true;
    }
}
