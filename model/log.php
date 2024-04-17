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

    public function addLog($message, $id){
        $sql = "INSERT INTO log (message, dateheure,id_utilisateur) VALUES (:message,NOW(),:id_utilisateur)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id_utilisateur', $id, PDO::PARAM_INT);
        $stmt->bindParam(':message', $message);
        $stmt->execute();
    }
}