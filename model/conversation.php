<?php

class conversation
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

    public function getConversationByid($id)
    {
        $id_admin = $_SESSION['admin'];
        $sql = "SELECT * FROM conversation WHERE id_utilisateur1 = :id_admin AND id_utilisateur2 = :id ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id_admin', $id_admin, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $rowCount = $stmt->rowCount();

        return $rowCount;
    }

    public function addConversation($id)
    {
        $id_admin = $_SESSION['admin'];
        $sql = "INSERT INTO conversation (id_utilisateur1, id_utilisateur2) VALUES (:id_admin, :id)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id_admin', $id_admin, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function getTechniciensConversation()
    {
        $id_admin = $_SESSION['admin'];
        $sql = "SELECT utilisateur.*, 
            fichier.chemin AS pdp,
            conversation.id_conversation
            FROM utilisateur 
            INNER JOIN fichier ON fichier.id_fichiers = utilisateur.id_fichier
            INNER JOIN conversation ON utilisateur.id_utilisateur = conversation.id_utilisateur2
            WHERE utilisateur.id_type = 2
            AND fichier.chemin LIKE 'pieces_jointe/technicien/%'
            AND conversation.id_utilisateur1 = :id_admin";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id_admin', $id_admin, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getConversationAndUsersInfo($conversation_id)
    {
        $sql = "SELECT c.*, 
        u1.id_utilisateur AS id_utilisateur1, 
        u1.nom AS nom_utilisateur1, 
        u1.prenom AS prenom_utilisateur1, 
        u1.email AS email_utilisateur1, 
        u2.id_utilisateur AS id_utilisateur2, 
        u2.nom AS nom_utilisateur2, 
        u2.prenom AS prenom_utilisateur2, 
        u2.email AS email_utilisateur2
        FROM conversation c
        LEFT JOIN utilisateur u1 ON c.id_utilisateur1 = u1.id_utilisateur
        LEFT JOIN utilisateur u2 ON c.id_utilisateur2 = u2.id_utilisateur
        WHERE c.id_conversation = :conversation_id
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':conversation_id', $conversation_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addMessage($conversation_id, $message)
    {
        $id_admin = $_SESSION['admin'];
        $type_message = "Message";
        $sql = "INSERT INTO communication (id_envoyeur, type_message, message, datetime, id_conversation) 
        VALUES (:id_envoyeur, :type_message, :message, NOW(), :id_conversation)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id_envoyeur', $id_admin, PDO::PARAM_INT);
        $stmt->bindParam(':type_message', $type_message);
        $stmt->bindParam(':id_conversation', $conversation_id, PDO::PARAM_INT);
        $stmt->bindParam(':message', $message, PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function getMessagesConversationById($conversation_id)
    {
        $sql = "SELECT comm.*, fichier.chemin AS pdp, u.nom AS nom_utilisateur, u.prenom AS prenom_utilisateur
            FROM communication comm
            LEFT JOIN utilisateur u ON comm.id_envoyeur = u.id_utilisateur
            LEFT JOIN fichier ON fichier.id_fichiers = u.id_fichier
            WHERE comm.id_conversation = :id_conversation 
            ORDER BY comm.datetime ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id_conversation', $conversation_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    public function addFichier($conversation_id, $fichier)
    {
        $chemin1 = $this->urlchemin();
        $chemin2 = "pieces_jointe/conversation/";

        $extension = strtolower(pathinfo($fichier["name"], PATHINFO_EXTENSION));

        if ($extension != "pdf" && !in_array($extension, array("jpg", "jpeg", "png", "gif"))) {
  
            return false;
        }

        $file_type = '';
        if (in_array($extension, array("jpg", "jpeg", "png", "gif"))) {
            $file_type = exif_imagetype($fichier["tmp_name"]);
        } elseif ($extension == "pdf") {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $file_type = finfo_file($finfo, $fichier["tmp_name"]);
            finfo_close($finfo);
        }

        if (!in_array($file_type, array("image/jpeg", "image/png", "image/gif", "application/pdf"))) {
            return false;
        }

        $uniqueFileName = pathinfo($fichier["name"], PATHINFO_FILENAME) . '_' . uniqid() . '.' . $extension;
        $target_file = $chemin1 . $chemin2 . $uniqueFileName;

        $fichier_bdd = $chemin2 . $uniqueFileName;

        $type = "Fichier";
        if (move_uploaded_file($fichier["tmp_name"], $target_file)) {
            $sql = "INSERT INTO communication (id_envoyeur, type_message, message, datetime, id_conversation) 
        VALUES (:id_envoyeur, :type_message, :message, NOW(), :id_conversation)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id_envoyeur', $_SESSION['admin'], PDO::PARAM_INT);
            $stmt->bindParam(':type_message', $type);
            $stmt->bindParam(':message', $fichier_bdd);
            $stmt->bindParam(':id_conversation', $conversation_id, PDO::PARAM_INT);
            return $stmt->execute();
        } else {
            return false;
        }
    }
}
