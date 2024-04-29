<?php

class intervention
{

    private $pdo;

    private function urlchemin()
    {
        $config = parse_ini_file("config.ini");
        return $config['path'];
    }

    public function __construct()
    {
        $config = parse_ini_file("config.ini");

        try {
            $this->pdo = new \PDO("mysql:host=" . $config["host"] . ";dbname=" . $config["db"] . ";charset=utf8", $config["login"], $config["password"]);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function countAllInterventions()
    {
        $sql = "SELECT count(*) FROM intervention";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function countFinishedInterventions()
    {
        $sql = "SELECT count(*) AS total_interventions_finies FROM intervention WHERE intervention.statut = 'Terminée'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchColumn();
    }
    public function countValiderInterventions()
    {
        $sql = "SELECT count(*) AS total_interventions_finies FROM intervention WHERE intervention.statut = 'Validée'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function countAFaireInterventions()
    {
        $sql = "SELECT count(*) AS total_interventions_a_faire FROM intervention WHERE intervention.statut = 'A faire'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function countEnCoursInterventions()
    {
        $sql = "SELECT count(*) AS total_interventions_en_cours FROM intervention WHERE intervention.statut = 'En cours'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function countReportInterventions()
    {
        $sql = "SELECT count(*) AS total_interventions_en_cours FROM intervention WHERE intervention.statut = 'Reportée'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function getInfosTechnicienIntervention($id)
    {
        $sql = "SELECT id_utilisateur,nom,prenom,email,telephone,cp,adresse,ville FROM utilisateur 
        INNER JOIN intervention_technicien ON utilisateur.id_utilisateur = intervention_technicien.id_technicien
        WHERE id_intervention = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getInfosInterventions($id)
    {
        $sql = "SELECT * FROM intervention WHERE id_intervention = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getInfosUserIntervention($id)
    {
        $sql = "SELECT id_utilisateur,nom,prenom,email,telephone,cp,adresse,ville FROM utilisateur 
        INNER JOIN intervention ON intervention.id_client = utilisateur.id_utilisateur
        WHERE id_intervention = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getDocumentTechnique($id)
    {
        $sql = "SELECT * FROM plan WHERE id_intervention = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getFacture($id)
    {
        $sql = "SELECT * FROM facture WHERE id_intervention = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDevis($id)
    {
        $sql = "SELECT * FROM devis WHERE id_intervention = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getSignatures($id)
    {
        $sql = "SELECT * FROM signature WHERE id_cri = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addDocument($file, $nom_affichage, $type, $id_admin, $id_intervention)
    {
        $nom_affichage = htmlspecialchars($nom_affichage);
        $chemin1 = $this->urlchemin();
        $chemin2 = "pieces_jointe/$type/";
        $uniqueFileName = pathinfo($file["name"], PATHINFO_FILENAME) . '_' . uniqid() . '.' . pathinfo($file["name"], PATHINFO_EXTENSION);
        $target_file = $chemin1 . $chemin2 . $uniqueFileName;

        if (move_uploaded_file($file["tmp_name"], $target_file)) {

            $image_bdd = $chemin2 . $uniqueFileName;

            switch ($type) {
                case 'facture':
                    $table = 'facture';
                    break;
                case 'plan':
                    $table = 'plan';
                    break;
                case 'devis':
                    $table = 'devis';
                    break;
                default:
                    return false;
            }

            $sql = "INSERT INTO $table (nom, chemin, id_utilisateur, id_intervention) VALUES (:nom, :chemin, :id_admin, :id_intervention)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':nom', $nom_affichage);
            $stmt->bindParam(':chemin', $image_bdd);
            $stmt->bindParam(':id_admin', $id_admin, PDO::PARAM_INT);
            $stmt->bindParam(':id_intervention', $id_intervention, PDO::PARAM_INT);
            $stmt->execute();

            return true;
        } else {
            return false;
        }
    }


    public function addIntervention($type, $technicien, $client, $categorie, $description, $date, $duree)
    {
        $statut = "A faire";
        $sql = "INSERT INTO intervention (type, date_intervention, statut, duree_intervention, description, categorie, id_client) 
            VALUES (:type, :date_intervention, :statut, :duree, :description, :categorie, :id_client)";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':type', $type, PDO::PARAM_STR);
            $stmt->bindParam(':date_intervention', $date, PDO::PARAM_STR);
            $stmt->bindParam(':statut', $statut, PDO::PARAM_STR);
            $stmt->bindParam(':description', $description, PDO::PARAM_STR);
            $stmt->bindParam(':duree', $duree, PDO::PARAM_STR);
            $stmt->bindParam(':categorie', $categorie, PDO::PARAM_STR);
            $stmt->bindParam(':id_client', $client, PDO::PARAM_INT);
            $stmt->execute();

            $id_intervention = $this->pdo->lastInsertId();
            $sql_inter_tech = "INSERT INTO intervention_technicien (id_intervention, id_technicien) 
                            VALUES (:id_intervention, :id_technicien)";
            $stmt_inter_tech = $this->pdo->prepare($sql_inter_tech);
            $stmt_inter_tech->bindParam(':id_intervention', $id_intervention, PDO::PARAM_INT);
            $stmt_inter_tech->bindParam(':id_technicien', $technicien, PDO::PARAM_INT);
            if ($stmt_inter_tech->execute()) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            if ($e->getCode() == '45000') {
                return false;
            } else {
                throw $e;
            }
        }
    }


    public function deleteIntervention($id)
    {
        $sql_select_facture = "SELECT chemin FROM facture WHERE id_intervention = :id";
        $stmt_select_facture = $this->pdo->prepare($sql_select_facture);
        $stmt_select_facture->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt_select_facture->execute();
        $facture_paths = $stmt_select_facture->fetchAll(PDO::FETCH_COLUMN);

        $sql_select_plan = "SELECT chemin FROM plan WHERE id_intervention = :id";
        $stmt_select_plan = $this->pdo->prepare($sql_select_plan);
        $stmt_select_plan->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt_select_plan->execute();
        $plan_paths = $stmt_select_plan->fetchAll(PDO::FETCH_COLUMN);

        $sql_select_devis = "SELECT chemin FROM devis WHERE id_intervention = :id";
        $stmt_select_devis = $this->pdo->prepare($sql_select_devis);
        $stmt_select_devis->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt_select_devis->execute();
        $devis_paths = $stmt_select_devis->fetchAll(PDO::FETCH_COLUMN);

        foreach ($facture_paths as $facture_path) {
            $full_path = $this->urlchemin() . $facture_path;
            if (file_exists($full_path)) {
                unlink($full_path);
            }
        }

        foreach ($plan_paths as $plan_path) {
            $full_path = $this->urlchemin() . $plan_path;
            if (file_exists($full_path)) {
                unlink($full_path);
            }
        }

        foreach ($devis_paths as $devis_path) {
            $full_path = $this->urlchemin() . $devis_path;
            if (file_exists($full_path)) {
                unlink($full_path);
            }
        }

        $delete_facture = "DELETE FROM facture WHERE id_intervention = :id";
        $stmt_facture = $this->pdo->prepare($delete_facture);
        $stmt_facture->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt_facture->execute();

        $delete_plan = "DELETE FROM plan WHERE id_intervention = :id";
        $stmt_plan = $this->pdo->prepare($delete_plan);
        $stmt_plan->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt_plan->execute();

        $delete_devis = "DELETE FROM devis WHERE id_intervention = :id";
        $stmt_devis = $this->pdo->prepare($delete_devis);
        $stmt_devis->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt_devis->execute();

        $delete_interv = "DELETE FROM intervention WHERE id_intervention = :id";
        $stmt = $this->pdo->prepare($delete_interv);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            $delete_interv_tech = "DELETE FROM intervention_technicien WHERE id_intervention = :id";
            $stmt_interv_tech = $this->pdo->prepare($delete_interv_tech);
            $stmt_interv_tech->bindParam(':id', $id, PDO::PARAM_INT);
            if ($stmt_interv_tech->execute()) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function getLastInsertedInterventionId()
    {
        $sql = "SELECT MAX(id_intervention) AS last_id FROM intervention"; 
        $stmt = $this->pdo->query($sql);
        $lastInsertedId = $stmt->fetchColumn();

        return $lastInsertedId;
    }
}
