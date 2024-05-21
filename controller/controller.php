<?php

class controller
{
    private function checkLogin()
    {
        if (!isset($_SESSION['admin'])) {
            header('Location: index.php?action=lcs_admin_login');
        }
    }
    public function lcs_admin_login()
    {
        if (isset($_SESSION['admin'])) {
            header('Location:index.php?action=planning');
            exit();
        }

        if (isset($_POST['login_admin'])) {

            if (!empty($_POST['email'] && !empty($_POST['password']))) {
                $email = $_POST['email'];
                $password = $_POST['password'];
                if ((new admin)->verfiLogin($email, $password)) {
                    (new view)->lcs_admin_login(null, "Connexion réalisée avec succès.<script>
                    setTimeout(function(){
                        window.location.href = 'index.php?action=planning'; 
                    }, 1000); 
                </script>");
                } else {
                    (new view)->lcs_admin_login("Vos identifiants sont incorrects.", null);
                }
            } else {
                (new view)->lcs_admin_login("Tous les champs doivent être remplis", null);
            }
        } else {
            (new view)->lcs_admin_login(null, null);
        }
    }

    public function addAdmin()
    {
        (new admin)->addAdmin("nom", "prenom", "jesuisadmin@gmail.com", "3wa5Q?2La8AYYX@upWAw", "cp", "ville", "adresse");
    }

    public function monprofil()
    {
        $this->checkLogin();

        if (isset($_GET['n°']) && !empty($_GET['n°']) && $_GET['n°'] == $_SESSION['admin']) {
            $admin_id = $_GET['n°'];
            $admin_details = (new admin)->getAdminInfos($admin_id);
            if ($admin_details) {
                if (isset($_POST['save_admin'])) {
                    $id = $_POST['id_admin'];
                    $nom = $_POST['nom'];
                    $prenom = $_POST['prenom'];
                    $email = $_POST['email'];
                    $telephone = $_POST['telephone'];
                    $adresse = $_POST['adresse'];
                    $code_postal = $_POST['code_postal'];
                    $ville = $_POST['ville'];

                    if (
                        !empty($nom) && !empty($prenom) && !empty($email) && !empty($telephone) && !empty($adresse) && !empty($code_postal) && !empty($ville)
                    ) {
                        if (isset($_FILES['photo']) && $_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE) {

                            if ((new admin)->updateAdmin($id, $_FILES['photo'], $nom, $prenom, $email, $telephone, $adresse, $code_postal, $ville)) {
                                (new view)->monprofil($admin_details, null, "Les modifications ont bien été enregistrées !<script>
                                setTimeout(function(){
                                    window.location.href = 'index.php?action=planning'; 
                                }, 1000); 
                            </script>");
                            } else {
                                (new view)->monprofil($admin_details, "Erreur lors de la modification du profil.", null);
                            }
                        } else {

                            if ((new admin)->updateAdmin($id, null, $nom, $prenom, $email, $telephone, $adresse, $code_postal, $ville)) {
                                (new view)->monprofil($admin_details, null, "Les modifications ont bien été enregistrées !<script>
                                setTimeout(function(){
                                    window.location.href = 'index.php?action=planning'; 
                                }, 1000); 
                            </script>");
                            } else {
                                (new view)->monprofil($admin_details, "Erreur lors de la modification du profil.", null);
                            }
                        }
                    } else {
                        (new view)->monprofil($admin_details, "Il manque des champs.", null);
                    }
                } else {
                    (new view)->monprofil($admin_details, null, null);
                }
            } else {
                (new view)->error404();
            }
        }
    }

    public function logout()
    {
        if (isset($_SESSION['admin'])) {
            session_destroy();
            header("Location: index.php?action=planning");
            exit();
        }
        header("Location: index.php?action=planning");
    }

    public function planningTab()
    {
        $this->checkLogin();
        $techniciens = (new technicien)->getTechniciens();
        $products = (new stock)->getAllStocks();
        $selectedTechnicienId = isset($_GET['selectedTechnicien']) ? $_GET['selectedTechnicien'] : null;

        $weekStart = isset($_GET['weekStart']) ? $_GET['weekStart'] : date('Y-m-d', strtotime('last Monday'));
        $weekEnd = isset($_GET['weekEnd']) ? $_GET['weekEnd'] : date('Y-m-d', strtotime('next Sunday'));

        $interventions = null;
        if ($selectedTechnicienId !== null) {
            $interventions = (new Technicien)->interventionsTechnicien($selectedTechnicienId, $weekStart, $weekEnd);
        }

        if (isset($_POST['add_new_intervention'])) {
            if (
                !empty($_POST['technicien']) && !empty($_POST['id_client_select'])
                && !empty($_POST['description']) && !empty($_POST['date']) && !empty($_POST['duree'])
            ) {
                $type = $_POST['type_intervention'];
                $technicien = $_POST['technicien'];
                $client = $_POST['id_client_select'];
                $categorie = !empty($_POST['categorie']) ? $_POST['categorie'] : $_POST['categorieAutre'];
                $description = $_POST['description'];
                $date = $_POST['date'];
                $duree = $_POST['duree'];

                $dateTime = DateTime::createFromFormat('Y-m-d\TH:i', $date);

                if ($dateTime !== false) {
                    $date = $dateTime->format('Y-m-d H:i:s');

                    $selectedProductsData = [];
                    if (isset($_POST['selected_products'])) {
                        foreach ($_POST['selected_products'] as $productId) {
                            $quantityKey = 'product_quantity_' . $productId;
                            $quantity = isset($_POST[$quantityKey]) ? $_POST[$quantityKey] : 0;
                            $selectedProductsData[] = array(
                                'id' => $productId,
                                'quantity' => $quantity
                            );
                        }
                    }

                    if ((new intervention)->addIntervention($type, $technicien, $client, $categorie, $description, $date, $duree)) {
                        $lastInsertedId = (new intervention)->getLastInsertedInterventionId();
                        $addStockResult = (new intervention_stock)->add_stock($lastInsertedId, $selectedProductsData);

                        if ($addStockResult === true) {
                            (new view)->planningTab($techniciens, $selectedTechnicienId, $interventions, $products, null, "L'intervention a été ajoutée avec succès.");
                        } else if (is_string($addStockResult)) {
                            (new view)->planningTab($techniciens, $selectedTechnicienId, $interventions, $products, $addStockResult, null);
                        } else {
                            (new view)->planningTab($techniciens, $selectedTechnicienId, $interventions, $products, "Une erreur est survenue lors de l'ajout de l'intervention.", null);
                        }
                    } else {
                        (new view)->planningTab($techniciens, $selectedTechnicienId, $interventions, $products, "Une erreur est survenue lors de l'ajout de l'intervention.", null);
                    }
                } else {
                    (new view)->planningTab($techniciens, $selectedTechnicienId, $interventions, $products, "La date a été mal reconvertie pour être ajoutée.", null);
                }
            } else {
                (new view)->planningTab($techniciens, $selectedTechnicienId, $interventions, $products, "Tous les champs sont obligatoires.", null);
            }
        } else {
            (new view)->planningTab($techniciens, $selectedTechnicienId, $interventions, $products, null, null);
        }
    }


    public function detailsIntervention()
    {
        $this->checkLogin();
        if (isset($_GET['intervention'])) {

            $intervention = (new intervention)->getInfosInterventions($_GET['intervention']);
            $technicien = (new intervention)->getInfosTechnicienIntervention($_GET['intervention']);
            $client = (new intervention)->getInfosUserIntervention($_GET['intervention']);
            $stocks = (new stock)->getStockForIntervention($_GET['intervention']);
            $totalstock = (new stock)->getAllStocks();

            if (!$intervention) {
                (new view)->error404();
                return;
            }

            if (isset($_POST['id_intervention_delete'])) {
                $id_intervention = $_POST['id_intervention_delete'];
                if ((new intervention)->deleteIntervention($id_intervention)) {

                    (new view)->detailsIntervention($intervention, $technicien, $client, $stocks, $totalstock, "Intervention supprimée avec succès. <script>
                    setTimeout(function() {
                        window.location.href = 'index.php?action=planning';
                    }, 1000);
                </script>", null);
                } else {
                    (new view)->detailsIntervention($intervention, $technicien, $client, $stocks, $totalstock, null, "Une erreur est survenue lors de la suppression de l'intervention");
                }
            } elseif (isset($_POST['add_stock'])) {
                $id_stock = $_POST['selected_stock'];
                $quantite = $_POST['quantity'];
                if ((new intervention_stock)->updateStockForIntervention($_GET['intervention'], $id_stock, $quantite)) {
                    (new view)->detailsIntervention($intervention, $technicien, $client, $stocks, $totalstock, "Le stock a bien été mis à jour. <script>
                    setTimeout(function() {
                        window.location.href = 'index.php?action=details_intervention&intervention=" . $_GET['intervention'] . "';
                    }, 1000);
                </script>", null);
                } else {
                    (new view)->detailsIntervention($intervention, $technicien, $client, $stocks, $totalstock, null, "Une erreur est survenue lors de la mise à jour du stock.");
                }
            } else {
                (new view)->detailsIntervention($intervention, $technicien, $client, $stocks, $totalstock, null, null);
            }
        } else {
            (new view)->error404();
        }
    }

    public function compte_rendu()
    {
        $this->checkLogin();

        if (isset($_GET['intervention'])) {
            $intervention = (new intervention)->getInfosInterventions($_GET['intervention']);

            if ($intervention && $intervention['statut'] == "Terminée") {
                $cri = (new cri)->getInfosCRI($_GET['intervention']);
                if (isset($_POST['validation_cri']) && isset($_POST['validation'])) {
                    $validation = $_POST['validation'];
                    $commentaire = $_POST['commentaire'];
                    $id_cri = $_POST['id_cri'];
                    if ((new cri)->validation_cri($_GET['intervention'], $id_cri, $validation, $commentaire)) {
                        (new view)->compte_rendu($cri, null, "Compte rendu validé avec succès. <script>
                        setTimeout(function() {
                            window.location.href = 'index.php?action=details_intervention&intervention=" . $_GET['intervention'] . "';
                        }, 1000);
                    </script>");
                    } else {
                        (new view)->compte_rendu($cri,"Une erreur est survenue lors de la validation du compte rendu.", null);
                    }
                } else {
                    (new view)->compte_rendu($cri, null, null);
                }
            } else {
                (new view)->error404();
                return;
            }
        } else {
            (new view)->error404();
            return;
        }
    }


    public function pieces_jointes()
    {
        $this->checkLogin();
        if (isset($_GET['intervention'])) {
            $plan = (new intervention)->getDocumentTechnique($_GET['intervention']);
            $facture = (new intervention)->getFacture($_GET['intervention']);
            $devis = (new intervention)->getDevis($_GET['intervention']);

            $client = (new intervention)->getInfosUserIntervention($_GET['intervention']);
            $intervention = (new intervention)->getInfosInterventions($_GET['intervention']);

            if (!$intervention) {
                (new view)->error404();
                return;
            }

            if (isset($_POST['uploadFichier'])) {
                $typeDocument = $_POST['typeDocument'];
                $nom_affichage = $_POST['nom_affichage'];

                if ((new intervention)->addDocument($_FILES['file_document'], $nom_affichage, $typeDocument, $_SESSION['admin'], $_GET['intervention'])) {

                    (new view)->pieces_jointes($intervention, $client, $plan, $facture, $devis, "Le document a bien été envoyé.<script>
                    setTimeout(function(){
                        window.location.href = 'index.php?action=pieces_jointes&intervention=" . $_GET['intervention'] . "'; 
                    }, 1000); 
                </script>", null);
                } else {
                    (new view)->pieces_jointes($intervention, $client, $plan, $facture, $devis, null, "Le document n'a pas pu être envoyé.");
                }
            } else {
                (new view)->pieces_jointes($intervention, $client, $plan, $facture, $devis, null, null);
            }
        } else {
            (new view)->error404();
        }
    }

    public function rechercheClient()
    {
        if (isset($_GET['query'])) {
            $query = $_GET['query'];
            $clients = (new client)->rechercherClients($query);
            header('Content-Type: application/json');
            echo json_encode($clients);
            exit;
        }
    }


    public function getInterventionsByTechnicienId()
    {
        $this->checkLogin();
        if (isset($_GET['selectedTechnicienId']) && isset($_GET['weekStart']) && isset($_GET['weekEnd'])) {
            $selectedTechnicienId = $_GET['selectedTechnicienId'];
            $weekStart = $_GET['weekStart'];
            $weekEnd = $_GET['weekEnd'];

            if (!empty($selectedTechnicienId) && !empty($weekStart) && !empty($weekEnd)) {
                $interventions = (new Technicien)->interventionsTechnicien($selectedTechnicienId, $weekStart, $weekEnd);

                header('Content-Type: application/json');
                echo json_encode($interventions);
                exit;
            }
        }
        http_response_code(400);
        echo json_encode(array("error" => "Invalid request parameters"));
        exit;
    }


    public function technicienTab()
    {
        $this->checkLogin();
        $technicien = (new technicien)->getTechniciens();
        (new view)->technicienTab($technicien);
    }

    public function technicien_details()
    {
        $this->checkLogin();
        if (isset($_GET['technicien']) && !empty($_GET['technicien'])) {
            $technicien_id = $_GET['technicien'];

            $selectedMonth = isset($_GET['selectedMonth']) ? $_GET['selectedMonth'] : date('m');
            $selectedYear = isset($_GET['selectedYear']) ? $_GET['selectedYear'] : date('Y');

            $technicien_details = (new technicien)->getTechnicienInfos($technicien_id);
            $documents = (new technicien)->getlesdocumentsbyid($technicien_id);

            $total_intervention = (new technicien)->countTechnicienInterventionTotal($technicien_id, $selectedMonth, $selectedYear);
            $total_intervention_fini = (new technicien)->countTechnicienInterventionsFinis($technicien_id, $selectedMonth, $selectedYear);
            $total_intervention_valide = (new technicien)->countTechnicienInterventionsValidee($technicien_id, $selectedMonth, $selectedYear);
            $total_intervention_refusee = (new technicien)->countTechnicienInterventionsRefusee($technicien_id, $selectedMonth, $selectedYear);
            $total_intervention_a_faire = (new technicien)->countTechnicienInterventionAfaire($technicien_id, $selectedMonth, $selectedYear);
            $total_intervention_en_cours = (new technicien)->countTechnicienInterventionEnCours($technicien_id, $selectedMonth, $selectedYear);
            $total_intervention_reportee = (new technicien)->countTechnicienInterventionReportee($technicien_id, $selectedMonth, $selectedYear);
            $years = (new technicien)->getInterventionTechnicienAnnee($technicien_id);

            if ($technicien_details) {

                if (isset($_POST['upload_doc_technicien'])) {

                    $nom_affichage = $_POST['nomAffichage'];
                    $typeDocument = $_FILES['fichier'];

                    if ((new technicien)->addDocumentForTechnicien($technicien_id, $typeDocument, $nom_affichage)) {
                        (new view)->technicien_details(
                            $technicien_details,
                            $documents,
                            $total_intervention,
                            $total_intervention_fini,
                            $total_intervention_valide,
                            $total_intervention_refusee,
                            $total_intervention_a_faire,
                            $total_intervention_en_cours,
                            $total_intervention_reportee,
                            null,
                            null,
                            $years,
                            null,
                            "Le document a bien été envoyé.<script>
                        setTimeout(function(){
                            window.location.href = 'index.php?action=technicien_details&technicien=" . $_GET['technicien'] . "'; 
                        }, 1000)
                    </script>"
                        );
                    } else {
                        (new view)->technicien_details(
                            $technicien_details,
                            $documents,
                            $total_intervention,
                            $total_intervention_fini,
                            $total_intervention_valide,
                            $total_intervention_refusee,
                            $total_intervention_a_faire,
                            $total_intervention_en_cours,
                            $total_intervention_reportee,
                            null,
                            null,
                            $years,
                            "Le document n'a pas pu être envoyé.",
                            null
                        );
                    }
                } elseif (isset($_POST['delete_documents']) && isset($_POST['id_fichier'])) {
                    $id_fichier = $_POST['id_fichier'];
                    if ((new technicien)->deleteDocuments($id_fichier, $technicien_id)) {
                        (new view)->technicien_details(
                            $technicien_details,
                            $documents,
                            $total_intervention,
                            $total_intervention_fini,
                            $total_intervention_valide,
                            $total_intervention_refusee,
                            $total_intervention_a_faire,
                            $total_intervention_en_cours,
                            $total_intervention_reportee,
                            null,
                            null,
                            $years,
                            null,
                            "Les documents ont bien été supprimé.<script>
                        setTimeout(function(){
                            window.location.href = 'index.php?action=technicien_details&technicien=" . $_GET['technicien'] . "'; 
                        }, 1000)
                    </script>"
                        );
                    } else {
                        (new view)->technicien_details(
                            $technicien_details,
                            $documents,
                            $total_intervention,
                            $total_intervention_fini,
                            $total_intervention_valide,
                            $total_intervention_refusee,
                            $total_intervention_a_faire,
                            $total_intervention_en_cours,
                            $total_intervention_reportee,
                            null,
                            null,
                            $years,
                            "Les documents n'ot pas pu être supprimés.",
                            null
                        );
                    }
                } else {
                    (new view)->technicien_details(
                        $technicien_details,
                        $documents,
                        $total_intervention,
                        $total_intervention_fini,
                        $total_intervention_valide,
                        $total_intervention_refusee,
                        $total_intervention_a_faire,
                        $total_intervention_en_cours,
                        $total_intervention_reportee,
                        $selectedMonth,
                        $selectedYear,
                        $years,
                        null,
                        null
                    );
                }
            } else {
                (new view)->error404();
            }
        } else {
            (new view)->error404();
        }
    }

    public function ajouter_technicien()
    {
        $this->checkLogin();
        if (isset($_POST['add_technicien'])) {
            $nom = $_POST['nom'];
            $prenom = $_POST['prenom'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            $password_confirm = $_POST['password2'];
            $telephone = $_POST['telephone'];
            $adresse = $_POST['adresse'];
            $code_postal = $_POST['code_postal'];
            $ville = $_POST['ville'];
            $regex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*\W).{8,}$/';

            if (!empty($_FILES['photo']['name'])) {
                if (
                    !empty($_POST['nom']) && !empty($_POST['prenom']) && !empty($_POST['email']) &&
                    !empty($_POST['password']) && !empty($_POST['password2']) && !empty($_POST['telephone']) &&
                    !empty($_POST['adresse']) && !empty($_POST['code_postal']) && !empty($_POST['ville'])
                ) {
                    if ((new technicien)->emailExisteDeja($email) == false) {
                        if ($password == $password_confirm) {
                            if (preg_match($regex, $password)) {

                                if ((new technicien)->createTechnicien($_FILES['photo'], $nom, $prenom, $email, $password, $telephone, $adresse, $code_postal, $ville)) {
                                    (new view)->ajouter_technicien(null, "Le technicien a été ajouté avec succès, vous allez être redirigé.<script>
                                    setTimeout(function(){
                                        window.location.href = 'index.php?action=technicien'; 
                                    }, 1000); 
                                </script>");
                                } else {
                                    (new view)->ajouter_technicien("Erreur lors de l'ajout du technicien", null);
                                }
                            } else {
                                (new view)->ajouter_technicien("Pour avoir un mot de passe valide, celui-ci doit contenir au minimum :
                                <li>8 caractères</li><li>Une minuscule</li><li>Une majuscule</li><li>Un chiffre</li><li>Un caractère spécial</li>", null);
                            }
                        } else {
                            (new view)->ajouter_technicien("Vos mots de passe ne se correspondent pas.", null);
                        }
                    } else {
                        (new view)->ajouter_technicien("L'adresse e-mail saisie est déjà liée à un compte.", null);
                    }
                } else {
                    (new view)->ajouter_technicien("Vous devez remplir tous les champs.", null);
                }
            } else {
                (new view)->ajouter_technicien("Vous devez choisir une image.", null);
            }
        } elseif (isset($_POST['confirm_update_role_technicien'])) {
            if (!empty($_POST['id_add_technicien_role']) && !empty($_POST['email_add_technicien_role'])) {
                $id = $_POST['id_add_technicien_role'];
                $email = $_POST['email_add_technicien_role'];

                if ((new technicien)->attributeTechnicienRole($id, $email)) {
                    (new view)->ajouter_technicien(null, "Le nouveau technicien a bien été ajouté, vous pouvez aller vérifier <a href='index.php?action=technicien'>ici</a>.");
                } else {
                    (new view)->ajouter_technicien("Erreur lors de l'ajout du rôle.", null);
                }
            } else {
                (new view)->ajouter_technicien("Vous devez saisir un email pour procéder au changement de rôle", null);
            }
        } else {
            (new view)->ajouter_technicien(null, null);
        }
    }

    public function rechercher_technicien()
    {
        if (isset($_POST['searchValue'])) {
            $searchValue = $_POST['searchValue'];

            $results = (new Technicien())->searchPeople($searchValue);

            header('Content-Type: application/json');
            echo json_encode($results);
        } else {
            echo json_encode(array());
        }
    }
    public function modifier_technicien()
    {
        $this->checkLogin();
        if (isset($_GET['technicien']) && !empty($_GET['technicien'])) {
            $technicien_id = $_GET['technicien'];
            $technicien_details = (new technicien)->getTechnicienInfos($technicien_id);
            $competence = (new technicien)->getCompetencesTechnicien($technicien_id);
            if ($technicien_details) {
                if (isset($_POST['save_technicien'])) {
                    $id = $_POST['id_technicien'];
                    $nom = $_POST['nom'];
                    $prenom = $_POST['prenom'];
                    $email = $_POST['email'];
                    $telephone = $_POST['telephone'];
                    $adresse = $_POST['adresse'];
                    $code_postal = $_POST['code_postal'];
                    $ville = $_POST['ville'];

                    if (
                        !empty($nom) && !empty($prenom) && !empty($email) && !empty($telephone) && !empty($adresse) && !empty($code_postal) && !empty($ville)
                    ) {
                        if (isset($_FILES['photo']) && $_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE) {

                            if ((new technicien)->updateTechnicien($id, $_FILES['photo'], $nom, $prenom, $email, $telephone, $adresse, $code_postal, $ville)) {
                                (new view)->modifier_technicien($technicien_details, $competence, null, "Les modifications ont bien été enregistrées !<script>
                                setTimeout(function(){
                                    window.location.href = 'index.php?action=technicien_details&technicien=" . $id . "'; 
                                }, 1000); 
                            </script>");
                            } else {
                                (new view)->modifier_technicien($technicien_details, $competence, "Erreur lors de la modification du profil.", null);
                            }
                        } else {
                            if ((new technicien)->updateTechnicien($id, null, $nom, $prenom, $email, $telephone, $adresse, $code_postal, $ville)) {
                                (new view)->modifier_technicien($technicien_details, $competence, null, "Les modifications ont bien été enregistrées !<script>
                                setTimeout(function(){
                                    window.location.href = 'index.php?action=technicien_details&technicien=" . $id . "'; 
                                }, 1000); 
                            </script>");
                            } else {
                                (new view)->modifier_technicien($technicien_details, $competence, "Erreur lors de la modification du profil.", null);
                            }
                        }
                    } else {
                        (new view)->modifier_technicien($technicien_details, $competence, "Il manque des champs.", null);
                    }
                } elseif (isset($_POST['delete_role_technicien'])) {
                    $id = $_POST['id_technicien'];
                    if ((new technicien)->deleteRoleTechnicien($id)) {
                        (new view)->modifier_technicien($technicien_details, $competence, null, "La suppressions du rôle a été effectuée.<script>
                        setTimeout(function(){
                            window.location.href = 'index.php?action=technicien'; 
                        }, 1000); 
                    </script>");
                    } else {
                        (new view)->modifier_technicien($technicien_details, $competence, "Erreur lors de la suppression du rôle.", null);
                    }
                } elseif (isset($_POST['competence_technicien'])) {
                    $competence_technicien = $_POST['competence'];
                    if ((new technicien)->updateCompetenceTechnicien($_GET['technicien'], $competence_technicien)) {
                        (new view)->modifier_technicien($technicien_details, $competence, null, "Les compétences du technicien ont été mis à jour.");
                    } else {
                        (new view)->modifier_technicien($technicien_details, $competence, "Erreur lors de l'enregistrement des compétences.", null);
                    }
                } else {
                    (new view)->modifier_technicien($technicien_details, $competence, null, null);
                }
            } else {
                (new view)->error404();
            }
        }
    }
    public function cartoTab()
    {
        $this->checkLogin();
        (new view)->cartoTab();
    }

    public function clientTab()
    {
        $this->checkLogin();
        (new view)->clientTab();
    }

    public function stockTab()
    {
        $this->checkLogin();
        $stock = (new stock)->getAllStocks();
        (new view)->stockTab($stock);
    }

    public function add_product()
    {
        $this->checkLogin();

        if (isset($_POST['add_product'])) {
            if (empty($_POST['nom']) || empty($_POST['description']) || empty($_POST['reference']) || empty($_POST['quantite'])) {
                $errorMess = "Veuillez remplir tous les champs.";
                (new view)->add_product($errorMess, null);
                return;
            }

            if (empty($_FILES['photos']['name'][0])) {
                $errorMess = "Veuillez sélectionner au moins une photo.";
                (new view)->add_product($errorMess, null);
                return;
            }

            $nom = $_POST['nom'];
            $description = $_POST['description'];
            $reference = $_POST['reference'];
            $quantite = $_POST['quantite'];
            $photos = $_FILES['photos'];

            if ((new stock)->addStock($nom, $description, $reference, $quantite, $photos)) {
                $successMess = "Le produit a été ajouté avec succès.<script>
                setTimeout(function(){
                    window.location.href = 'index.php?action=stock'; 
                }, 1000); 
            </script>";
                (new view)->add_product(null, $successMess);
            } else {
                $errorMess = "Une erreur s'est produite lors de l'ajout du produit. Veuillez réessayer.";
                (new view)->add_product($errorMess, null);
            }
        } else {
            (new view)->add_product(null, null);
        }
    }

    public function info_product()
    {
        $this->checkLogin();
        if (isset($_GET['product']) && !empty($_GET['product'])) {
            $product_id = $_GET['product'];
            $product_details = (new stock)->getProductInfos($product_id);
            if ($product_details) {
                if (isset($_POST['delete_product'])) {
                    $id = $_POST['id_stock'];
                    if ((new stock)->deleteProduct($id)) {
                        (new view)->info_product($product_details, null, "La suppression du produit a été effectuée.<script>
                        setTimeout(function(){
                            window.location.href = 'index.php?action=stock'; 
                        }, 1000); 
                    </script>");
                    } else {
                        (new view)->info_product($product_details, "Erreur lors de la suppression du produit.", null);
                    }
                } else {
                    (new view)->info_product($product_details, null, null);
                }
            } else {
                (new view)->error404();
            }
        } else {
            (new view)->error404();
        }
    }

    public function update_product()
    {
        $this->checkLogin();
        if (isset($_GET['product']) && !empty($_GET['product'])) {
            $product_id = $_GET['product'];
            $product_details = (new stock)->getProductInfos($product_id);
            if ($product_details) {
                if (isset($_POST['update_product'])) {
                    $id = $_POST['id_stock'];
                    $nom = $_POST['nom'];
                    $description = $_POST['description'];
                    $reference = $_POST['reference'];
                    $quantite = $_POST['quantite'];

                    if (empty($nom) || empty($description) || empty($reference) || empty($quantite)) {
                        $errorMess = "Veuillez remplir tous les champs.";
                        (new view)->update_product($product_details, $errorMess, null);
                        return;
                    }

                    if ((new stock)->update_stock($id, $nom, $description, $reference, $quantite)) {
                        $successMess = "Le produit a été mis à jour avec succès.<script>
                        setTimeout(function(){
                            window.location.href = 'index.php?action=stock'; 
                        }, 1000); 
                    </script>";
                        (new view)->update_product($product_details, null, $successMess);
                    } else {
                        $errorMess = "Une erreur s'est produite lors de la mise à jour du produit. Veuillez réessayer.";
                        (new view)->update_product($product_details, $errorMess, null);
                    }
                } else {
                    (new view)->update_product($product_details, null, null);
                }
            } else {
                (new view)->error404();
            }
        } else {
            (new view)->error404();
        }
    }


    public function communicationTab()
    {
        $this->checkLogin();
        $citations = (new citations)->getCitations();
        $conversations = (new conversation)->getTechniciensConversation();
        $techniciens = (new technicien)->getTechniciens();
        if (isset($_POST['add_citation'])) {
            $citation  = $_POST['citation'];
            if (empty($citation)) {
                $errorMess = "Veuillez remplir le champs pour ajouter une citation.";
                (new view)->communicationTab($techniciens, $conversations, $citations, $errorMess, null);
                return;
            }
            if ((new citations)->addCitation($citation)) {
                (new view)->communicationTab($techniciens, $conversations, $citations, null, "Votre citation a été ajouté avec succès !<script>
                setTimeout(function(){
                    window.location.href = 'index.php?action=communication'; 
                }, 1000); 
            </script>");
            } else {
                (new view)->communicationTab($techniciens, $conversations, $citations, null, "Une erreur s'est produite lors de l'ajout de votre citation. Veuillez réessayer.");
            }
        } elseif (isset($_POST['create_conv'])) {
            $id_technicien = $_POST['technicienSelect'];
            if (empty($id_technicien)) {
                $errorMess = "Veuillez sélectionner un technicien.";
                (new view)->communicationTab($techniciens,  $conversations, $citations, $errorMess, null);
                return;
            }

            if ((new conversation)->getConversationByid($id_technicien) >= 1) {
                (new view)->communicationTab($techniciens, $conversations, $citations, null, "Vous avez déjà une conversation avec ce technicien.");
            } else {
                if ((new conversation)->addConversation($id_technicien)) {
                    (new view)->communicationTab($techniciens, $conversations, $citations, null, "Votre conversation a été ajouté avec succès!<script>
                    setTimeout(function(){
                        window.location.href = 'index.php?action=communication'; 
                    }, 1000)</script>");
                } else {
                    (new view)->communicationTab($techniciens,  $conversations, $citations, null, "Une erreur s'est produite lors de l'ajout de votre conversation. Veuillez réessayer.");
                }
            }
        } else {
            (new view)->communicationTab($techniciens, $conversations, $citations, null, null);
        }
    }

    public function conversation()
    {
        $this->checkLogin();

        if (isset($_GET['conversation'])) {
            $conversation_id = $_GET['conversation'];

            $conversation = (new conversation)->getConversationAndUsersInfo($conversation_id);
            $message = (new conversation)->getMessagesConversationById($conversation_id);

            if ($conversation) {
                if (isset($_POST['send_message'])) {
                    if (!empty($_POST['message']) || !empty($_FILES['file']['name'])) {
                        $message_content = $_POST['message'];

                        if (!empty($_FILES['file']['name'])) {
                            $conversation_id = $_GET['conversation'];
                            $fichier = $_FILES['file'];
                            if ((new conversation)->addFichier($conversation_id, $fichier)) {
                                (new view)->conversation($conversation, $message, null, "<script>
                                setTimeout(function(){
                                    window.location.href = 'index.php?action=conversation&conversation=" . $_GET['conversation'] . "'; 
                                }, 10); 
                            </script>");
                            } else {
                                (new view)->conversation($conversation, $message, "Erreur lors de l'envoi de votrer fichier", null);
                            }
                        } else if ((new conversation)->addMessage($_GET['conversation'], $message_content)) {
                            (new view)->conversation($conversation, $message, null, "<script>
                        setTimeout(function(){
                            window.location.href = 'index.php?action=conversation&conversation=" . $_GET['conversation'] . "'; 
                        }, 10); 
                    </script>");
                        } else {
                            (new view)->conversation($conversation, $message, "Erreur lors de l'envoi du message, assurez-vous d'envoyer une image ou un pdf", null);
                        }
                    } else {
                        (new view)->conversation($conversation, $message, "Veuillez saisir votre message ou sélectionner un fichier.", null);
                    }
                } else {
                    (new view)->conversation($conversation, $message, null, null);
                }
            } else {
                (new view)->error404();
            }
        } else {
            (new view)->error404();
        }
    }


    public function maintenanceTab()
    {
        $this->checkLogin();
        $conges = (new conges)->getCongesPayesNonValide();
        $entretien = (new entretien)->getEntretienNonValide();
        $signalement = (new signalement)->getSignalementNonValide();
        if (isset($_POST['valider_conges']) && isset($_POST['id_conges'])) {
            if ((new conges)->updateConge($_POST['id_conges'])) {
                (new view)->maintenanceTab($conges, $entretien, $signalement, null, "Les demandes de congés ont été pris en compte.<script>
                setTimeout(function(){
                    window.location.href = 'index.php?action=maintenance'; 
                }, 1000); 
            </script>");
            } else {
                (new view)->maintenanceTab($conges, $entretien, $signalement, null, "Une erreur s'est produite lors de la mise à jour des demandes de congés. Veuillez réessayer.");
            }
        } elseif (isset($_POST['valider_entretien']) && isset($_POST['id_entretien'])) {
            if ((new entretien)->updateEntretien($_POST['id_entretien'])) {
                (new view)->maintenanceTab($conges, $entretien, $signalement, null, "Les demandes d'entretien et pannes ont été pris en compte.<script>
            setTimeout(function(){
                window.location.href = 'index.php?action=maintenance'; 
            }, 1000); 
        </script>");
            } else {
                (new view)->maintenanceTab($conges, $entretien, $signalement, null, "Une erreur s'est produite lors de la mise à jour des demandes d'entretien. Veuillez réessayer.");
            }
        } elseif (isset($_POST['valider_signalement']) && $_POST['id_signalement']) {
            if ((new signalement)->updateSignalement($_POST['id_signalement'])) {
                (new view)->maintenanceTab($conges, $entretien, $signalement, null, "Les demandes de signalement ont été pris en compte.<script>
                setTimeout(function(){
                    window.location.href = 'index.php?action=maintenance'; 
                }, 1000); 
            </script>");
            } else {
                (new view)->maintenanceTab($conges, $entretien, $signalement, null, "Une erreur s'est produite lors de la mise à jour des demandes de signalement. Veuillez réessayer.");
            }
        } else {

            (new view)->maintenanceTab($conges, $entretien, $signalement, null, null);
        }
    }

    public function checklistTab()
    {
        $this->checkLogin();
        (new view)->checklistTab();
    }

    public function politique_confidentialite(){
        (new view)->politique_confidentialite();
    }

    public function error404()
    {
        $this->checkLogin();
        (new view)->error404();
    }
}
