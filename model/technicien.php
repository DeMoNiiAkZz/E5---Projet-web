<?php

class technicien
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


    public function getTechniciens()
    {
        $sql = "SELECT utilisateur.*, 
        fichier.chemin AS pdp
        FROM utilisateur 
        INNER JOIN fichier ON fichier.id_fichiers = utilisateur.id_fichier
        WHERE utilisateur.id_type = 2
        AND fichier.chemin LIKE 'pieces_jointe/technicien/%'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getlesdocumentsbyid($id)
    {
        $sql = "SELECT id_fichiers,chemin, nom_affichage FROM fichier
        WHERE id_user = :id_user";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id_user', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function searchPeople($searchValue)
    {
        if (empty($searchValue)) {
            $sql = "SELECT id_utilisateur, email FROM utilisateur";
            $stmt = $this->pdo->prepare($sql);
        } else {
            $sql = "SELECT id_utilisateur, email FROM utilisateur WHERE email LIKE :searchValue AND id_type != 2";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':searchValue', "%$searchValue%", PDO::PARAM_STR);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }


    public function getPdpTechnicien($id)
    {
        $sql = "SELECT id_fichier FROM utilisateur WHERE id_utilisateur = :id_utilisateur";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id_utilisateur', $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $id_fichier = $result['id_fichier'];

        $getchemin = "SELECT chemin FROM fichier WHERE id_fichiers = :id_fichier";
        $stmt_getchemin = $this->pdo->prepare($getchemin);
        $stmt_getchemin->bindParam(':id_fichier', $id_fichier, PDO::PARAM_INT);
        $stmt_getchemin->execute();
        $result_getchemin = $stmt_getchemin->fetch(PDO::FETCH_ASSOC);
        $cheminsql = $result_getchemin['chemin'];

        $nom_fichier = basename($cheminsql);

        return $nom_fichier;
    }

    public function emailExisteDeja($email)
    {
        $sql = "SELECT count(*) FROM utilisateur WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $count = $stmt->fetchColumn();
        if ($count >= 1) {
            return true;
        } else {
            return false;
        }
    }
    public function getTechnicienInfos($id)
    {
        $sql = "SELECT utilisateur.*, fichier.chemin AS pdp
        FROM utilisateur 
        INNER JOIN fichier ON fichier.id_fichiers = utilisateur.id_fichier
        WHERE utilisateur.id_utilisateur = :id
        AND fichier.chemin LIKE 'pieces_jointe/technicien/%'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function getCompetencesTechnicien($id)
    {
        $sql = "SELECT * FROM qualification WHERE id_technicien = :id_technicien";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id_technicien', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function updateCompetenceTechnicien($id, $competence)
    {
        $select_sql = "SELECT * FROM qualification WHERE id_technicien = :id_technicien";
        $select_stmt = $this->pdo->prepare($select_sql);
        $select_stmt->bindParam(':id_technicien', $id);
        $select_stmt->execute();

        $row_count = $select_stmt->rowCount();

        if ($row_count > 0) {
            $update_sql = "UPDATE qualification SET competence = :competence WHERE id_technicien = :id_technicien";
            $update_stmt = $this->pdo->prepare($update_sql);
            $update_stmt->bindParam(':id_technicien', $id);
            $update_stmt->bindParam(':competence', $competence);
            if ($update_stmt->execute()) {
                return true;
            }
        } else {
            $insert_sql = "INSERT INTO qualification (competence, id_technicien) VALUES (:competence, :id_technicien)";
            $insert_stmt = $this->pdo->prepare($insert_sql);
            $insert_stmt->bindParam(':id_technicien', $id);
            $insert_stmt->bindParam(':competence', $competence);
            if ($insert_stmt->execute()) {
                return true;
            }
        }

        return false;
    }



    public function getInterventionTechnicienAnnee($id)
    {
        $sql = "SELECT DISTINCT YEAR(date_intervention) AS intervention_year
                FROM intervention i
                INNER JOIN intervention_technicien it ON it.id_intervention = i.id_intervention
                WHERE it.id_technicien = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }


    public function countTechnicienInterventionTotal($id, $selectedMonth = null, $selectedYear = null)
    {
        $sql = "SELECT COUNT(*) AS total_interventions_terminees_jour 
            FROM intervention_technicien it
            INNER JOIN intervention i ON it.id_intervention = i.id_intervention
            WHERE it.id_technicien = :id";

        if ($selectedMonth !== null && $selectedMonth !== "") {
            $sql .= " AND MONTH(i.date_intervention) = :month";
        }
        if ($selectedYear !== null && $selectedYear !== "") {
            $sql .= " AND YEAR(i.date_intervention) = :year";
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($selectedMonth !== null && $selectedMonth !== "") {
            $stmt->bindParam(':month', $selectedMonth, PDO::PARAM_INT);
        }
        if ($selectedYear !== null && $selectedYear !== "") {
            $stmt->bindParam(':year', $selectedYear, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function countTechnicienInterventionsFinis($id, $selectedMonth = null, $selectedYear = null)
    {
        $sql = "SELECT COUNT(*) AS total_interventions_terminees_jour 
                FROM intervention_technicien it
                INNER JOIN intervention i ON it.id_intervention = i.id_intervention
                WHERE it.id_technicien = :id
                AND i.statut = 'Terminée'";

        if ($selectedMonth !== null && $selectedMonth !== "") {
            $sql .= " AND MONTH(i.date_intervention) = :month";
        }

        if ($selectedYear !== null && $selectedYear !== "") {
            $sql .= " AND YEAR(i.date_intervention) = :year";
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($selectedMonth !== null && $selectedMonth !== "") {
            $stmt->bindParam(':month', $selectedMonth, PDO::PARAM_INT);
        }

        if ($selectedYear !== null && $selectedYear !== "") {
            $stmt->bindParam(':year', $selectedYear, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function countTechnicienInterventionsValidee($id, $selectedMonth = null, $selectedYear = null)
    {
        $sql = "SELECT COUNT(*) AS total_interventions_terminees_jour 
                FROM intervention_technicien it
                INNER JOIN intervention i ON it.id_intervention = i.id_intervention
                WHERE it.id_technicien = :id
                AND i.statut = 'Validée'";

        if ($selectedMonth !== null && $selectedMonth !== "") {
            $sql .= " AND MONTH(i.date_intervention) = :month";
        }

        if ($selectedYear !== null && $selectedYear !== "") {
            $sql .= " AND YEAR(i.date_intervention) = :year";
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($selectedMonth !== null && $selectedMonth !== "") {
            $stmt->bindParam(':month', $selectedMonth, PDO::PARAM_INT);
        }

        if ($selectedYear !== null && $selectedYear !== "") {
            $stmt->bindParam(':year', $selectedYear, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function countTechnicienInterventionsRefusee($id, $selectedMonth = null, $selectedYear = null)
    {
        $sql = "SELECT COUNT(*) AS total_interventions_terminees_jour 
                FROM intervention_technicien it
                INNER JOIN intervention i ON it.id_intervention = i.id_intervention
                WHERE it.id_technicien = :id
                AND i.statut = 'Refusée'";

        if ($selectedMonth !== null && $selectedMonth !== "") {
            $sql .= " AND MONTH(i.date_intervention) = :month";
        }

        if ($selectedYear !== null && $selectedYear !== "") {
            $sql .= " AND YEAR(i.date_intervention) = :year";
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($selectedMonth !== null && $selectedMonth !== "") {
            $stmt->bindParam(':month', $selectedMonth, PDO::PARAM_INT);
        }

        if ($selectedYear !== null && $selectedYear !== "") {
            $stmt->bindParam(':year', $selectedYear, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function countTechnicienInterventionEnCours($id, $selectedMonth = null, $selectedYear = null)
    {
        $sql = "SELECT COUNT(*) AS total_interventions_en_cours 
                FROM intervention_technicien it
                INNER JOIN intervention i ON it.id_intervention = i.id_intervention
                WHERE it.id_technicien = :id
                AND i.statut = 'En cours'";

        if ($selectedMonth !== null && $selectedMonth !== "") {
            $sql .= " AND MONTH(i.date_intervention) = :month";
        }

        if ($selectedYear !== null && $selectedYear !== "") {
            $sql .= " AND YEAR(i.date_intervention) = :year";
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($selectedMonth !== null && $selectedMonth !== "") {
            $stmt->bindParam(':month', $selectedMonth, PDO::PARAM_INT);
        }

        if ($selectedYear !== null && $selectedYear !== "") {
            $stmt->bindParam(':year', $selectedYear, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function countTechnicienInterventionAfaire($id, $selectedMonth = null, $selectedYear = null)
    {
        $sql = "SELECT COUNT(*) AS total_interventions_a_faire 
                FROM intervention_technicien it
                INNER JOIN intervention i ON it.id_intervention = i.id_intervention
                WHERE it.id_technicien = :id
                AND i.statut = 'A faire'";

        if ($selectedMonth !== null && $selectedMonth !== "") {
            $sql .= " AND MONTH(i.date_intervention) = :month";
        }

        if ($selectedYear !== null && $selectedYear !== "") {
            $sql .= " AND YEAR(i.date_intervention) = :year";
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($selectedMonth !== null && $selectedMonth !== "") {
            $stmt->bindParam(':month', $selectedMonth, PDO::PARAM_INT);
        }

        if ($selectedYear !== null && $selectedYear !== "") {
            $stmt->bindParam(':year', $selectedYear, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function countTechnicienInterventionReportee($id, $selectedMonth = null, $selectedYear = null)
    {
        $sql = "SELECT COUNT(*) AS total_interventions_terminees_jour 
                FROM intervention_technicien it
                INNER JOIN intervention i ON it.id_intervention = i.id_intervention
                WHERE it.id_technicien = :id
                AND i.statut = 'Reportée'";

        if ($selectedMonth !== null && $selectedMonth !== "") {
            $sql .= " AND MONTH(i.date_intervention) = :month";
        }

        if ($selectedYear !== null && $selectedYear !== "") {
            $sql .= " AND YEAR(i.date_intervention) = :year";
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($selectedMonth !== null && $selectedMonth !== "") {
            $stmt->bindParam(':month', $selectedMonth, PDO::PARAM_INT);
        }

        if ($selectedYear !== null && $selectedYear !== "") {
            $stmt->bindParam(':year', $selectedYear, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchColumn();
    }


    public function createTechnicien($photo, $nom, $prenom, $email, $password, $telephone, $adresse, $cp, $ville)
    {
        $chemin1 = $this->urlchemin();
        $chemin2 = "pieces_jointe/technicien/";
        $target_file = $chemin1 . $chemin2 . basename($photo["name"]);

        $nom = htmlspecialchars($nom);
        $prenom = htmlspecialchars($prenom);
        $email = htmlspecialchars($email);
        $password =  password_hash($password, PASSWORD_BCRYPT);
        $telephone = htmlspecialchars($telephone);
        $adresse = htmlspecialchars($adresse);
        $cp = htmlspecialchars($cp);
        $ville = htmlspecialchars($ville);

        if (move_uploaded_file($photo["tmp_name"], $target_file)) {

            $sql = "INSERT INTO utilisateur (nom,prenom,email,password,cp,ville,adresse,telephone, id_type) VALUES (:nom,:prenom,:email,:password,:cp,:ville,:adresse,:telephone,2)";
            $stmt_tech = $this->pdo->prepare($sql);
            $stmt_tech->bindParam(':nom', $nom);
            $stmt_tech->bindParam(':prenom', $prenom);
            $stmt_tech->bindParam(':email', $email);
            $stmt_tech->bindParam(':password', $password);
            $stmt_tech->bindParam(':telephone', $telephone);
            $stmt_tech->bindParam(':adresse', $adresse);
            $stmt_tech->bindParam(':cp', $cp);
            $stmt_tech->bindParam(':ville', $ville);
            $result_user_insertion = $stmt_tech->execute();

            if ($result_user_insertion) {
                $last_user_id = $this->pdo->lastInsertId();

                $image_bdd = $chemin2 . basename($photo["name"]);
                $sql = "INSERT INTO fichier (chemin) VALUES (:chemin)";
                $stmt = $this->pdo->prepare($sql);
                $stmt->bindParam(':chemin', $image_bdd);
                $result_file_insertion = $stmt->execute();

                if ($result_file_insertion) {
                    $last_file_id = $this->pdo->lastInsertId();

                    $sql_update_user = "UPDATE utilisateur SET id_fichier = :id_fichier WHERE id_utilisateur = :id_user";
                    $stmt_update_user = $this->pdo->prepare($sql_update_user);
                    $stmt_update_user->bindParam(':id_fichier', $last_file_id);
                    $stmt_update_user->bindParam(':id_user', $last_user_id);
                    $result_user_update = $stmt_update_user->execute();

                    return $result_user_update;
                }
                return false;
            }
            return false;
        }
        return false;
    }

    public function updateTechnicien($id, $photo, $nom, $prenom, $email, $telephone, $adresse, $cp, $ville)
    {
        $chemin1 = $this->urlchemin();
        $chemin2 = "pieces_jointe/technicien/";
        $nom = htmlspecialchars($nom);
        $prenom = htmlspecialchars($prenom);
        $email = htmlspecialchars($email);
        $telephone = htmlspecialchars($telephone);
        $adresse = htmlspecialchars($adresse);
        $cp = htmlspecialchars($cp);
        $ville = htmlspecialchars($ville);

        if ($photo !== null && $photo['error'] !== UPLOAD_ERR_NO_FILE) {
            $target_file = $chemin1 . $chemin2 . basename($photo["name"]);


            if (move_uploaded_file($photo["tmp_name"], $target_file)) {

                $sql = "UPDATE utilisateur SET nom = :nom, prenom = :prenom, email = :email, 
                telephone = :telephone, adresse = :adresse, cp = :cp, ville = :ville
                WHERE id_utilisateur = :id_utilisateur";
                $stmt_tech = $this->pdo->prepare($sql);
                $stmt_tech->bindParam(':id_utilisateur', $id);
                $stmt_tech->bindParam(':nom', $nom);
                $stmt_tech->bindParam(':prenom', $prenom);
                $stmt_tech->bindParam(':email', $email);
                $stmt_tech->bindParam(':telephone', $telephone);
                $stmt_tech->bindParam(':adresse', $adresse);
                $stmt_tech->bindParam(':cp', $cp);
                $stmt_tech->bindParam(':ville', $ville);
                if ($stmt_tech->execute()) {
                    $get_id_fichier = "SELECT id_fichier FROM utilisateur WHERE id_utilisateur = :id_utilisateur";
                    $stmt_get = $this->pdo->prepare($get_id_fichier);
                    $stmt_get->bindParam(':id_utilisateur', $id);
                    if ($stmt_get->execute()) {
                        $id_fichier_row = $stmt_get->fetch(PDO::FETCH_ASSOC);
                        $id_fichier = $id_fichier_row['id_fichier'];

                        $image_bdd = $chemin2 . basename($photo["name"]);
                        $sql = "UPDATE fichier SET chemin = :fichier WHERE id_fichiers = :id_fichier AND fichier.chemin LIKE 'pieces_jointe/technicien/%'";
                        $stmt = $this->pdo->prepare($sql);
                        $stmt->bindParam(':fichier', $image_bdd);
                        $stmt->bindParam(':id_fichier', $id_fichier);
                        if ($stmt->execute()) {
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
        } else {
            $sql = "UPDATE utilisateur SET nom = :nom, prenom = :prenom, email = :email, 
                telephone = :telephone, adresse = :adresse, cp = :cp, ville = :ville
                WHERE id_utilisateur = :id_utilisateur";
            $stmt_tech = $this->pdo->prepare($sql);
            $stmt_tech->bindParam(':id_utilisateur', $id);
            $stmt_tech->bindParam(':nom', $nom);
            $stmt_tech->bindParam(':prenom', $prenom);
            $stmt_tech->bindParam(':email', $email);
            $stmt_tech->bindParam(':telephone', $telephone);
            $stmt_tech->bindParam(':adresse', $adresse);
            $stmt_tech->bindParam(':cp', $cp);
            $stmt_tech->bindParam(':ville', $ville);
            if ($stmt_tech->execute()) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function deleteRoleTechnicien($id)
    {
        $id_type = 3;
        $sql = 'UPDATE utilisateur SET id_type = :id_type WHERE id_utilisateur = :id_utilisateur';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam('id_type', $id_type, PDO::PARAM_INT);
        $stmt->bindParam(':id_utilisateur', $id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function deleteTechnicien($id)
    {
        $get_photo = "SELECT id_fichier FROM utilisateur WHERE id_utilisateur = :id_user";
        $stmt = $this->pdo->prepare($get_photo);
        $stmt->bindParam(':id_user', $id);
        $stmt->execute();
        $id_fichier_row = $stmt->fetch(PDO::FETCH_ASSOC);
        $id_fichier = $id_fichier_row['id_fichier'];

        $sql = "DELETE FROM fichier WHERE id_fichiers = :id_fichier";
        $stmt_delete = $this->pdo->prepare($sql);
        $stmt_delete->bindParam(':id_fichier', $id_fichier);
        $stmt_delete->execute();

        $sql_delete_tech = "DELETE FROM utilisateur WHERE id_utilisateur = :id_user";
        $stmt_delete_tech = $this->pdo->prepare($sql_delete_tech);
        $stmt_delete_tech->bindParam(':id_user', $id);
        if ($stmt_delete_tech->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function attributeTechnicienRole($id, $email)
    {
        $id_type = 2;
        $sql = "UPDATE utilisateur SET id_type = :id_type WHERE id_utilisateur = :id AND email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id_type', $id_type);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':email', $email);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function interventionsTechnicien($id, $weekStart, $weekEnd)
    {
        $sql = "SELECT intervention.*,
        utilisateur.id_utilisateur as id_client, 
        utilisateur.nom as nom_client, 
        utilisateur.prenom as prenom_client,
        utilisateur.email as email_client, 
        utilisateur.cp as cp_client, 
        utilisateur.ville as ville_client, 
        utilisateur.adresse as adresse_client,
        utilisateur.telephone as telephone_client,
        intervention_technicien.id_technicien as id_technicien
        FROM intervention_technicien 
        INNER JOIN intervention ON intervention_technicien.id_intervention = intervention.id_intervention
        INNER JOIN utilisateur ON intervention.id_client = utilisateur.id_utilisateur
        WHERE intervention_technicien.id_technicien = :id_technicien
        AND DATE(intervention.date_intervention) BETWEEN :week_start AND :week_end
        ORDER BY intervention.date_intervention ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id_technicien', $id, PDO::PARAM_INT);
        $stmt->bindParam(':week_start', $weekStart, PDO::PARAM_STR);
        $stmt->bindParam(':week_end', $weekEnd, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addDocumentForTechnicien($id, $fichier, $nom_affichage)
    {
        $chemin = $this->urlchemin();
        $chemin2 = "pieces_jointe/" . $id . "_technicien/";
        $chemin_technicien = $chemin . $chemin2;

        if (!file_exists($chemin_technicien)) {
            if (!mkdir($chemin_technicien, 0777, true)) {
                return false;
            }
        }

        $identifiant_unique = uniqid();
        $extension = pathinfo($fichier['name'], PATHINFO_EXTENSION);
        $nom_fichier_unique = $identifiant_unique . "." . $extension;
        $destination = $chemin_technicien . $nom_fichier_unique;

        $fichier_bdd = $chemin2 . $nom_fichier_unique;
        if (!move_uploaded_file($fichier['tmp_name'], $destination)) {
            return false;
        }

        $sql = "INSERT INTO fichier (chemin, nom_affichage, id_user) VALUES (:chemin, :nom, :id_user)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':nom', $nom_affichage);
        $stmt->bindParam(':chemin', $fichier_bdd);
        $stmt->bindParam(':id_user', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
    public function deleteDocuments($id_docs, $id_technicien)
    {
        foreach ($id_docs as $id) {
            $sql = "DELETE FROM fichier WHERE id_fichiers = :id AND id_user = :id_technicien AND chemin LIKE 
            CONCAT('%', :id_technicien, '_technicien', '%')";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':id_technicien', $id_technicien, PDO::PARAM_INT);
            if (!$stmt->execute()) {
                return false; 
            }
        }
        return true;
    }
}
