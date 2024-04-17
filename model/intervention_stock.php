<?php

class intervention_stock
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

    public function add_stock($interventionId, $selectedProductsData)
    {
        foreach ($selectedProductsData as $product) {
            $productId = $product['id'];
            $quantity = $product['quantity'];

            $insertSql = "INSERT INTO intervention_stock (quantite, id_intervention, id_stock) 
                          VALUES (:quantite, :id_intervention, :id_produit)";

            $stmt = $this->pdo->prepare($insertSql);
            $stmt->bindParam(':id_intervention', $interventionId, PDO::PARAM_INT);
            $stmt->bindParam(':id_produit', $productId, PDO::PARAM_INT);
            $stmt->bindParam(':quantite', $quantity, PDO::PARAM_INT);

            try {
                $stmt->execute();
            } catch (PDOException $e) {
                if ($e->getCode() === '45000') {
                    return "Erreur lors de l'ajout du stock : La quantité saisie est supérieure à la quantité totale disponible.";
                } else {
                    throw $e;
                }
            }
            $updateStockSql = "CALL UpdateStock(:productId, :quantity)";
            $updateStmt = $this->pdo->prepare($updateStockSql);
            $updateStmt->bindParam(':productId', $productId, PDO::PARAM_INT);
            $updateStmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);

            try {
                $updateStmt->execute();
            } catch (PDOException $e) {
                return $e->getMessage();
            }
        }

        return true;
    }

    public function updateStockForIntervention($interventionId, $productId, $quantity)
    {
        $sql_check = "SELECT COUNT(*) AS count FROM intervention_stock WHERE id_intervention = :interventionId AND id_stock = :productId";
        $stmt_check = $this->pdo->prepare($sql_check);
        $stmt_check->bindParam(':interventionId', $interventionId, PDO::PARAM_INT);
        $stmt_check->bindParam(':productId', $productId, PDO::PARAM_INT);
        $stmt_check->execute();
        $result = $stmt_check->fetch(PDO::FETCH_ASSOC);
        $count = $result['count'];

        if ($count > 0) {
            $sql_update = "UPDATE intervention_stock SET quantite = quantite + :quantity WHERE id_intervention = :interventionId AND id_stock = :productId";
            $stmt_update = $this->pdo->prepare($sql_update);
            $stmt_update->bindParam(':interventionId', $interventionId, PDO::PARAM_INT);
            $stmt_update->bindParam(':productId', $productId, PDO::PARAM_INT);
            $stmt_update->bindParam(':quantity', $quantity, PDO::PARAM_INT);
            $update_success = $stmt_update->execute();
            if (!$update_success) {
                return false;
            }
        } else {
            $sql_insert = "INSERT INTO intervention_stock (quantite, id_intervention, id_stock) VALUES (:quantity, :id_intervention, :id_stock)";
            $stmt_insert = $this->pdo->prepare($sql_insert);
            $stmt_insert->bindParam(':id_intervention', $interventionId, PDO::PARAM_INT);
            $stmt_insert->bindParam(':id_stock', $productId, PDO::PARAM_INT);
            $stmt_insert->bindParam(':quantity', $quantity, PDO::PARAM_INT);
            $insert_success = $stmt_insert->execute();
            if (!$insert_success) {
                return false;
            }
        }

        $sql_procedure = "CALL UpdateStock(:productId, :quantity)";
        $stmt_procedure = $this->pdo->prepare($sql_procedure);
        $stmt_procedure->bindParam(':productId', $productId, PDO::PARAM_INT);
        $stmt_procedure->bindParam(':quantity', $quantity, PDO::PARAM_INT);
        $procedure_success = $stmt_procedure->execute();
        if (!$procedure_success) {
            return false;
        }

        return true;
    }
}
