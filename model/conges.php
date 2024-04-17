<?php

class conges
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

    public function getAllConges()
    {
        $sql = "SELECT * FROM conges_payes";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getCongesPayesNonValide()
    {
        $traiter = 0;
        $sql = "SELECT conges_payes.*, utilisateur.nom, utilisateur.prenom 
        FROM conges_payes 
        INNER JOIN utilisateur ON utilisateur.id_utilisateur = conges_payes.id_technicien
        WHERE traiter = :traiter";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':traiter', $traiter, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function updateConge($id_conges)
    {
        $traiter = 1;
        foreach ($id_conges as $id_conge) {
            $sql = "UPDATE conges_payes SET traiter = :traiter WHERE id_conges_payes = :id_conge";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':traiter', $traiter, PDO::PARAM_INT);
            $stmt->bindParam(':id_conge', $id_conge, PDO::PARAM_INT);
            if (!$stmt->execute()) {
                return false; 
            }
        }
        return true; 
    }

    public function countDemandeConges(){
        $sql = "SELECT count(*) FROM conges_payes WHERE traiter = 0";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->rowCount();
    }
}
