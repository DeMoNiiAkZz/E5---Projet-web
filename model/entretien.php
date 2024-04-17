<?php

class entretien
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
        return "C:/xampp/htdocs/LCS_Dash/";
    }

    public function getEntretienNonValide(){
        $sql = "SELECT *, utilisateur.nom, utilisateur.prenom, vehicule.immatriculation 
        FROM entretien_panne_vehicule 
        INNER JOIN utilisateur ON utilisateur.id_utilisateur = entretien_panne_vehicule.id_technicien
        INNER JOIN vehicule ON vehicule.id_vehicule = entretien_panne_vehicule.id_vehicule
        WHERE traiter = 0";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function updateEntretien($id_entretien)
    {
        $traiter = 1;
        foreach ($id_entretien as $id) {
            $sql = "UPDATE entretien_panne_vehicule SET traiter = :traiter WHERE id_entretien = :id_entretien";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':traiter', $traiter, PDO::PARAM_INT);
            $stmt->bindParam(':id_entretien', $id, PDO::PARAM_INT);
            if (!$stmt->execute()) {
                return false; 
            }
        }
        return true; 
    }
}
