<?php

class cri
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

    public function getInfosCRI($id)
    {
        $sql = "SELECT cri.*, signature.signature_client, signature.signature_technicien FROM cri 
        LEFT JOIN signature ON signature.id_cri = cri.id_cri
        WHERE id_intervention = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function validation_cri($id_intervention, $id_cri, $validation, $commentaire)
    {
        $sql = "INSERT INTO cri_validation (validation, commentaire, id_cri)
            VALUES (:validation, :commentaire, :id_cri)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id_cri', $id_cri, PDO::PARAM_INT);
        $stmt->bindParam(':validation', $validation);
        $stmt->bindParam(':commentaire', $commentaire);
        if ($stmt->execute()) {
            if ($validation == 'valider') {
                $sql_update = "UPDATE intervention SET statut = 'Validée'
                           WHERE id_intervention = :id_intervention"; 
                $stmt_update = $this->pdo->prepare($sql_update);
                $stmt_update->bindParam(':id_intervention', $id_intervention, PDO::PARAM_INT);
                if ($stmt_update->execute()) {
                    return true;
                } else {
                    return false;
                }
            } elseif ($validation == 'refuser') {
                $sql_update = "UPDATE intervention SET statut = 'Refusée'
                           WHERE id_intervention = :id_intervention";
                $stmt_update = $this->pdo->prepare($sql_update);
                $stmt_update->bindParam(':id_intervention', $id_intervention, PDO::PARAM_INT);
                if ($stmt_update->execute()) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false; 
            }
        } else {
            return false;
        }
    }
}
