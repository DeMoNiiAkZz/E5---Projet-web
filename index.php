<?php
session_start();

$config = parse_ini_file("config.ini"); //permet d'intégrer le fichier ini pour la connexion de la base

try {
    $pdo = new \PDO("mysql:host=" . $config["host"] . ";dbname=" . $config["db"] . ";charset=utf8", $config["login"], $config["password"]);
} catch (Exception $e) {
    echo "<h1>Une erreur de connexion à la base est survenue</h1>";
    echo $e->getMessage();
    exit;
}


require("controller/controller.php");
require("view/view.php");
require("model/technicien.php");
require("model/admin.php");
require("model/intervention.php");
require("model/client.php");
require("model/devis.php");
require("model/plan.php");
require("model/facture.php");
require("model/stock.php");
require("model/intervention_stock.php");
require("model/citations.php");
require("model/conversation.php");
require("model/cri.php");
require("model/signalement.php");
require("model/entretien.php");
require("model/conges.php");


//routes
if (isset($_GET["action"])) {
    switch ($_GET["action"]) {

        case "addAdmin":
            (new controller)->addAdmin();
            break;

        case "lcs_admin_login":
            (new controller)->lcs_admin_login();
            break;

        case "monprofil":
            (new controller)->monprofil();
            break;
        
        case "logout":
            (new controller)->logout();
            break;
            //planning
        case "planning":
            (new controller)->planningTab();
            break;
        case "rechercheClient":
            (new controller)->rechercheClient();
            break;

        case "getInterventionsByTechnicienId":
            (new controller)->getInterventionsByTechnicienId();
            break;
        
        case "details_intervention":
            (new controller)->detailsIntervention();
            break;
        case "pieces_jointes":
            (new controller)->pieces_jointes();
            break;
        case "compte_rendu":
            (new controller)->compte_rendu();
            break;


            // technicien
        case "technicien":
            (new controller)->technicienTab();
            break;

        case "technicien_details":
            (new controller)->technicien_details();
            break;

        case "ajouter_technicien":
            (new controller)->ajouter_technicien();
            break;
        case "modifier_technicien":
            (new controller)->modifier_technicien();
            break;
        case "rechercher_technicien":
            (new controller)->rechercher_technicien();
            break;





            //client
        case "client":
            (new controller)->clientTab();
            break;

        //stock 
        case "stock":
            (new controller)->stockTab();
            break;

        case "add_product":
            (new controller)->add_product();
            break;

        case "info_product":
            (new controller)->info_product();
            break;

        case "update_product":
            (new controller)->update_product();
            break;


        //communication
        case "communication":
            (new controller)->communicationTab();
            break;
        case "conversation":
            (new controller)->conversation();
            break;
        


        //maintenance
        case "maintenance":
            (new controller)->maintenanceTab();
            break;
        case "check-list":
            (new controller)->checklistTab();
            break;

        case "politique_confidentialite":
            (new controller)->politique_confidentialite();
            break;

        case "error404":
            (new controller)->error404();
            break;

        default:
            (new controller)->error404();
            break;
    }
} else {
    (new controller)->planningTab();
}
