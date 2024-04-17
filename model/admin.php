<?php

class admin
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

    public function addAdmin($nom,$prenom,$email,$password, $cp, $ville, $adresse){
        $password = password_hash($password, PASSWORD_BCRYPT);
        $id_type = 1;
        $id_fichier = 16;
        $sql = "INSERT INTO utilisateur (nom,prenom,email,password,cp,ville,adresse,id_type, id_fichier)
        VALUES (:nom, :prenom, :email, :password, :cp, :ville, :adresse, :id_type, :id_fichier)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':prenom', $prenom);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':cp', $cp);
        $stmt->bindParam(':ville', $ville);
        $stmt->bindParam(':adresse', $adresse);
        $stmt->bindParam(':id_type', $id_type);
        $stmt->bindParam(':id_fichier', $id_fichier);


        $stmt->execute();
    }
    public function verfiLogin($email, $password)
    {
        $id_type = 1;
        $sql = "SELECT utilisateur.* FROM utilisateur 
        WHERE utilisateur.email = :email 
        AND utilisateur.id_type = :id_type";
        $stmt = $this->pdo->prepare($sql); 
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':id_type', $id_type);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);


        if ($user) {
            if (password_verify($password, $user['password'])) {
                $_SESSION['admin'] = $user['id_utilisateur'];
                $_SESSION['pdp'] = $this->getPdpAdmin($user['id_utilisateur']);
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function getPdpAdmin($id)
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

        $nom_fichier = $cheminsql;

        return $nom_fichier;
    }

    public function getAdminInfos($id)
    {
        $sql = "SELECT utilisateur.*, fichier.chemin AS pdp
        FROM utilisateur 
        INNER JOIN fichier ON fichier.id_fichiers = utilisateur.id_fichier
        WHERE utilisateur.id_utilisateur = :id
        AND fichier.chemin LIKE 'pieces_jointe/admin/%'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function signup($nom,$prenom,$email,$password,$cp, $ville, $adresse, $telephone){

        $password = password_hash($password, PASSWORD_BCRYPT);
        $sql = "INSERT INTO utilisateur (nom, prenom, email, password, cp, ville, adresse, telephone) VALUES (:nom, :prenom, :email, :password, :cp, :ville, :adresse, :telephone)";
        $stmt = $this->pdo->prepare($sql); 
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':prenom', $prenom);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':cp', $cp);
        $stmt->bindParam(':ville', $ville);
        $stmt->bindParam(':adresse', $adresse);
        $stmt->bindParam(':telephone', $telephone);
        $stmt->execute();

    }

    public function updateAdmin($id, $photo, $nom, $prenom, $email, $telephone, $adresse, $cp, $ville)
    {
        $chemin1 = $this->urlchemin();
        $chemin2 = "pieces_jointe/admin/";
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
                        $sql = "UPDATE fichier SET chemin = :fichier WHERE id_fichiers = :id_fichier AND fichier.chemin LIKE 'pieces_jointe/admin/%'";
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
}
