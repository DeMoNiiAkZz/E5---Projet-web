<?php

class stock
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

    public function getAllStocks()
    {
        $sql = "SELECT stock.*, fichier.chemin 
        FROM stock 
        LEFT JOIN fichier ON stock.id_stock = fichier.id_stock;
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getProductInfos($id)
    {
        $sql = "SELECT stock.*, fichier.chemin AS chemin_photo FROM stock
                LEFT JOIN fichier ON stock.id_stock = fichier.id_stock 
                WHERE stock.id_stock = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }


    public function addStock($nom, $description, $reference, $quantite, $photos)
    {
        $sql = "INSERT INTO stock (reference,nom, description,quantite)
        VALUES (:reference,:nom,:description,:quantite)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':reference', $reference);
        $stmt->bindParam(':quantite', $quantite);
        if ($stmt->execute()) {
            $last_id = $this->pdo->lastInsertId();
            $chemin1 = $this->urlchemin();
            $chemin2 = "pieces_jointe/stock/";
            foreach ($photos['name'] as $key => $photoName) {

                $filePath = pathinfo($photos["name"][$key], PATHINFO_FILENAME) . '_' . uniqid() . '.' . pathinfo($photos["name"][$key], PATHINFO_EXTENSION);
                $target_file = $chemin1 . $chemin2 . $filePath;
                $image_bdd = $chemin2 . $filePath;
                if (move_uploaded_file($photos['tmp_name'][$key], $target_file)) {
                    $sql_photo = "INSERT INTO fichier (chemin, id_stock)
                VALUES (:chemin, :id_stock)";
                    $stmt_photo = $this->pdo->prepare($sql_photo);
                    $stmt_photo->bindParam(':chemin', $image_bdd);
                    $stmt_photo->bindParam(':id_stock', $last_id);
                    if ($stmt_photo->execute()) {
                        return true;
                    } else {
                        return false;
                    }
                } else {

                    return false;
                }
            }
        } else {
            return false;
        }
    }

    public function deleteProduct($id)
    {
        $sql_select = "SELECT chemin FROM fichier WHERE id_stock = :id";
        $stmt_select = $this->pdo->prepare($sql_select);
        $stmt_select->bindParam(':id', $id, PDO::PARAM_INT);
        if ($stmt_select->execute()) {
            $file_paths = $stmt_select->fetchAll(PDO::FETCH_COLUMN);

            foreach ($file_paths as $file_path) {
                $full_path = $this->urlchemin() . $file_path;
                if (file_exists($full_path)) {
                    unlink($full_path);
                }
            }

            $sql_delete = "DELETE FROM fichier WHERE id_stock = :id";
            $stmt_delete = $this->pdo->prepare($sql_delete);
            $stmt_delete->bindParam(':id', $id, PDO::PARAM_INT);
            if ($stmt_delete->execute()) {
                $sql_delete_product = "DELETE FROM stock WHERE id_stock = :id";
                $stmt_delete_product = $this->pdo->prepare($sql_delete_product);
                $stmt_delete_product->bindParam(':id', $id, PDO::PARAM_INT);
                if ($stmt_delete_product->execute()) {
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

    public function update_stock($id, $nom, $description, $reference, $quantite)
    {
        $sql = "UPDATE stock SET nom = :nom, description = :description, reference = :reference, quantite = :quantite WHERE id_stock = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':reference', $reference);
        $stmt->bindParam(':quantite', $quantite, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function verifquantiteproduit($selectedProductsData)
    {
        $allTrue = true;

        foreach ($selectedProductsData as $product) {
            $productId = $product['id'];
            $quantity = $product['quantity'];

            $sql = "CALL before_update_stock(:productId, :quantity)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':productId', $productId, PDO::PARAM_INT);
            $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$result || $result['result'] === 'false') {
                $allTrue = false;
            }
        }
        return $allTrue;
    }

    public function getStockForIntervention($id) {
        $sql = "SELECT stock.*, fichier.chemin AS chemin, intervention_stock.quantite AS quantite_utilisee
                FROM intervention_stock
                INNER JOIN stock  ON intervention_stock.id_stock = stock.id_stock
                LEFT JOIN fichier ON stock.id_stock = fichier.id_stock 
                WHERE intervention_stock.id_intervention = :id";
    
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    
}
