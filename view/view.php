<?php

class view
{
  private function url()
  {
    return "http://localhost/LCS_Dash/";
  }

  private function header($title)
  {
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>LCS - <?= $title ?></title>
      <link rel="shortcut icon" href="pieces_jointe/ico_web/logconnectservices_white.png" type="image/x-icon">
      <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
      <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet">
      <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
      <link rel="stylesheet" href="css/style.css">
    </head>
    <?php

    ?>

    <body>
      <header>
        <div class="header d-flex justify-content-between align-items-center">
          <div class="col">
            <a href="index.php?action=planning"><img src="pieces_jointe/ico_web/logconnectservices_white.png" width="150px" height="120px" /></a>
          </div>
          <div class="col">
            <div class="flex-grow-1">Dashboard Admin</div>
          </div>
          <div class="col">
            <div class="dropdown">
              <?php if (isset($_SESSION['admin'])) {
              ?>
                <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  Mon compte
                </button>
                <?php
                if (isset($_SESSION['pdp'])) {
                ?>
                  <a href="index.php?action=monprofil&n°=<?= $_SESSION['admin'] ?>">
                    <img src="<?= $this->url() . $_SESSION['pdp'] ?>" width="70px" height="70px" class="rounded-circle">
                  </a>
                <?php }
                ?>
                <div class="dropdown-menu no-uppercase" aria-labelledby="dropdownMenuButton">
                  <a class="dropdown-item" href="index.php?action=monprofil&n°=<?= $_SESSION['admin'] ?>">Profil</a>
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item" href="index.php?action=logout">Déconnexion</a>
                </div>
              <?php
              }
              ?>
            </div>
          </div>
        </div>
      </header>
      <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
      <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <?php
  }

  private function getInterventionsTerminées()
  {
    return (new intervention)->countFinishedInterventions();
  }


  private function getDemandeConges()
  {
    return (new conges)->countDemandeConges();
  }

  private function tabs()
  {
    $nombreInterventionsTerminees = $this->getInterventionsTerminées();
    $nombreCongesPayes = $this->getDemandeConges();
    echo '
    <div class="container-fluid">
    <ul class="nav nav-tabs" id="" role="tablist">
  <li class="nav-item">
    <a class="nav-link ' . ((isset($_GET['action']) && ($_GET['action'] == 'planning' || $_GET['action'] == 'details_intervention'  || $_GET['action'] == 'pieces_jointes')) ? 'active' : '') . ' " id="planning-tab" href="index.php?action=planning">Planning';
    if ($nombreInterventionsTerminees > 0) {
      echo '<span class="badge badge-primary ml-1">' . $nombreInterventionsTerminees . '</span>';
    }
    echo '</a>
  </li>
  <li class="nav-item">
    <a class="nav-link ' . ((isset($_GET['action']) && ($_GET['action'] == 'technicien' || $_GET['action'] == 'technicien_details'  || $_GET['action'] == 'ajouter_technicien' || $_GET['action'] == 'modifier_technicien'))  ? 'active' : '') . '" id="technicien-tab" href="index.php?action=technicien">Technicien</a>
  </li>
  <li class="nav-item">
    <a class="nav-link ' . ((isset($_GET['action']) && $_GET['action']  == 'stock' || $_GET['action'] == 'add_product' || $_GET['action'] == 'info_product') ? 'active' : '') . '" id="stock-tab" href="index.php?action=stock">Stock</a>
  </li>
  <li class="nav-item">
    <a class="nav-link ' . ((isset($_GET['action']) && $_GET['action'] == 'communication' || $_GET['action'] == 'conversation') ? 'active' : '') . '" id="communication-tab" href="index.php?action=communication">Communication   
    </a>
  </li>
  <li class="nav-item">
    <a class="nav-link ' . ((isset($_GET['action']) && $_GET['action'] == 'maintenance') ? 'active' : '') . '" id="maintenance-tab" href="index.php?action=maintenance">Maintenance</a>
  </li>
</ul>
</div>     
';
  }

  private function footer()
  {
    echo '
    <br><br>
        </body>
        </html>';
  }

  public function lcs_admin_login($errorMess, $successMess)
  {
    $this->header('Connexion');
    ?>
      <div class="container-fluid">
        <div class="row justify-content-center">
          <div class="col-md-10">
            <h2 class="card-title text-center mb-4 mt-4">Connexion</h2>
            <?php
            if (isset($errorMess)) {
              $this->errorMessage($errorMess);
            }
            if (isset($successMess)) {
              $this->successMessage($successMess);
            }
            ?>
            <div class="card">
              <div class="card-body">
                <form action="" method="post">
                  <div class="mb-3 text-center">
                    <img src="pieces_jointe/ico_web/logconnectservices.png" class="" style="width: 215px; height: 170px;">
                  </div>

                  <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="unexemple@gmail.com" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                  </div>
                  <div class="mb-3">
                    <label for="password" class="form-label">Mot de passe</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="••••••••••••">
                  </div>
                  <div class="md-3 text-center">
                    <button type="submit" name="login_admin" class="btn btn-primary w-25">Se connecter</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
      </div>
    <?php
  }

  public function monprofil($admin, $errorMess, $successMess)
  {
    $this->header('Mon profil');
    $this->tabs();
    ?>
      <div class="container-fluid">
        <div class="row justify-content-center">
          <div class="col-md-10">
            <h2 class="card-title text-center mb-4 mt-4">Mon profil</h2>
            <?php
            if (isset($errorMess)) {
              $this->errorMessage($errorMess);
            }
            if (isset($successMess)) {
              $this->successMessage($successMess);
            }
            ?>
            <div class="card">
              <div class="card-body">
                <form action="" method="post" enctype="multipart/form-data">
                  <div class="text-center mb-3">
                    <label for="file-input">
                      <input type="file" name="photo" id="file-input" style="display: none;">
                      <img src="<?= $this->url() . $admin['pdp'] ?>" id="profile-img" class="rounded-circle" alt="Photo de profil" width="150" height="150" style="cursor: pointer;">
                    </label>
                  </div>
                  <input type="hidden" name="id_admin" value="<?= $admin['id_utilisateur'] ?>">
                  <div class="mb-3">
                    <label for="nom" class="form-label">Nom</label>
                    <input type="text" class="form-control" id="nom" name="nom" value="<?= $admin['nom'] ?>">
                  </div>
                  <div class="mb-3">
                    <label for="prenom" class="form-label">Prénom</label>
                    <input type="text" class="form-control" id="prenom" name="prenom" value="<?= $admin['prenom'] ?>">
                  </div>
                  <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?= $admin['email'] ?>">
                  </div>
                  <div class="mb-3">
                    <label for="telephone" class="form-label">Téléphone</label>
                    <input type="text" class="form-control" id="telephone" name="telephone" value="<?= $admin['telephone'] ?>">
                  </div>
                  <div class="mb-3">
                    <label for="adresse" class="form-label">Adresse</label>
                    <input type="text" class="form-control" id="adresse" name="adresse" value="<?= $admin['adresse'] ?>">
                  </div>
                  <div class="row mb-3">
                    <div class="col-md-6">
                      <label for="code_postal" class="form-label">Code Postal</label>
                      <input type="text" class="form-control" id="code_postal" name="code_postal" placeholder="Code Postal" value="<?= $admin['cp'] ?>" required>
                    </div>
                    <div class="col-md-6">
                      <label for="ville" class="form-label">Ville</label>
                      <input type="text" class="form-control" id="ville" name="ville" placeholder="Ville" value="<?= $admin['ville'] ?>" required>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <button type="submit" name="save_admin" class="btn btn-primary">Enregistrer les modifications</button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
      </div>

      <script>
        document.getElementById('file-input').addEventListener('change', function(event) {
          var file = event.target.files[0];
          var reader = new FileReader();
          reader.onload = function(e) {
            document.getElementById('profile-img').src = e.target.result;
          };
          reader.readAsDataURL(file);
        });
      </script>

    <?php
    $this->footer();
  }



  public function planningTab($techniciens, $selectedTechnicienId, $interventions, $stocks, $errorMess, $successMess)
  {
    $this->header('Planning des Interventions');
    $this->tabs();
    ?>
      <div class="container-fluid">
        <div class="row justify-content-center">
          <div class="col-md-10">
            <br>
            <?php
            if (isset($errorMess)) {
              $this->errorMessage($errorMess);
            }
            if (isset($successMess)) {
              $this->successMessage($successMess);
            }
            ?>
            <h2 class="card-title text-center mb-4 mt-4">Planning des techniciens</h2>
            <div class="text-center mb-3">
              <button class="btn btn-primary" onclick="openAddInterventionModal()">
                Ajouter au planning
              </button>
            </div>
            <div class="form-group">
              <label for="selectedTechnicien">Sélectionner un technicien :</label>
              <select name="selectedTechnicien" id="selectedTechnicien" class="form-control" onchange="getSelectedTechnicienInterventions()">
                <option></option>
                <?php foreach ($techniciens as $technicien) : ?>
                  <option value="<?= $technicien['id_utilisateur'] ?>" <?= ($selectedTechnicienId == $technicien['id_utilisateur']) ? 'selected' : '' ?>>
                    <?= $technicien['prenom'] ?> <?= $technicien['nom'] ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="text-center mb-3">
              <button class="btn btn-primary mr-2" onclick="previousWeek()">
                <i class="bi bi-arrow-left"></i>
              </button>
              <span id="weekRange" class="font-weight-bold"></span>
              <button class="btn btn-primary ml-2" onclick="nextWeek()">
                <i class="bi bi-arrow-right"></i>
              </button>
            </div>

            <div class="card mt-3">
              <div class="card-body">
                <h3 id="weekNumber" class="text-center"></h3>
                <br>
                <div class="table-responsive">
                  <table class="table table-bordered">
                    <thead>
                      <tr>
                        <th style="width: 80px;">Créneaux horaires</th>
                        <?php
                        $days = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
                        foreach ($days as $day) {
                          echo '<th class="text-center">' . $day . '</th>';
                        }
                        ?>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $startHour = 8;
                      $endHour = 18;
                      $days = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi'];
                      for ($hour = $startHour; $hour < $endHour; $hour++) {
                        for ($minutes = 0; $minutes < 60; $minutes += 30) {
                          echo '<tr>';
                          echo '<td class="text-center" style="width: 80px; height: 40px;">' . sprintf("%02d", $hour) . ':' . sprintf("%02d", $minutes) . '</td>';
                          foreach ($days as $day) {
                            echo '<td id="' . strtolower($day) . '-' . sprintf("%02d", $hour) . ':' . sprintf("%02d", $minutes) . '" style="width: 100px; height: 40px; overflow: hidden;"></td>';
                          }
                          echo '</tr>';
                        }
                      }
                      ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="modal fade" id="interventionModal" tabindex="-1" role="dialog" aria-labelledby="interventionModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="interventionModalLabel">Ajouter au planning</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <form method="post" action="">
              <div class="modal-body">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                  <li class="nav-item">
                    <a class="nav-link active" id="onglet1-tab" data-toggle="tab" href="#onglet1" role="tab" aria-controls="onglet1" aria-selected="true">Informations</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="onglet2-tab" data-toggle="tab" href="#onglet2" role="tab" aria-controls="onglet2" aria-selected="false">Produits</a>
                  </li>
                </ul>

                <div class="tab-content" id="myTabContent">
                  <div class="tab-pane fade show active" id="onglet1" role="tabpanel" aria-labelledby="onglet1-tab">
                    <div class="form-group mt'3">
                      <label for="type_intervention">Type</label>
                      <select name="type_intervention" id="type_intervention" class="form-control">
                        <option value="intervention">Intervention</option>
                      </select>
                    </div>
                    <div class="form-group">
                      <label for="searchClient">Client : </label>
                      <input type="text" id="searchClient" class="form-control" placeholder="E-mail ou téléphone au complet" onkeyup="searchClients()">
                    </div>
                    <div id="searchResults" class="mt-3"></div>
                    <input type="hidden" id="selectedClientId" name="id_client_select">
                    <div class="form-group">
                      <label for="technicien">Technicien :</label>
                      <select name="technicien" id="technicien" class="form-control">
                        <?php foreach ($techniciens as $technicien) : ?>
                          <option value="<?= $technicien['id_utilisateur'] ?>"><?= $technicien['prenom'] ?> <?= $technicien['nom'] ?></option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                    <div class="form-group">
                      <label for="categorie">Catégorie :</label>
                      <select name="categorie" id="categorie" class="form-control" onchange="toggleOtherField(this)">
                        <option value="Fibre optique">Fibre optique</option>
                        <option value="Maison Connectée">Maison Connectée</option>
                        <option value="Borne de recharge">Borne de recharge</option>
                        <option value="Energie solaire">Energie solaire</option>
                        <option value="Electricité">Electricité</option>
                        <option value="Sanitaire">Sanitaire</option>
                        <option value="">Autre</option>
                      </select>
                    </div>
                    <div id="autreField" class="form-group" style="display: none;">
                      <label for="autreCategorie">Autre catégorie :</label>
                      <input type="text" class="form-control" id="autreCategorie" name="categorieAutre">
                    </div>
                    <div class="form-group">
                      <label for="description">Description :</label>
                      <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                      <label for="date">Date :</label>
                      <input type="datetime-local" class="form-control" id="date" name="date">
                      <small class="form-text text-muted">Seules les heures entières sont autorisées.</small>
                    </div>

                    <div class="form-group">
                      <label for="duree">Durée :</label>
                      <input type="time" class="form-control" id="duree" name="duree">
                      <small class="form-text text-muted">Seules les heures entières ou demi-heures sont autorisées.</small>
                    </div>
                  </div>

                  <div class="tab-pane fade" id="onglet2" role="tabpanel" aria-labelledby="onglet2-tab">
                    <div class="form-group">
                      <label for="products">Produits disponibles:</label>
                      <div class="row">
                        <?php $count = 0; ?>
                        <?php foreach ($stocks as $stock) : ?>
                          <div class="col-md-6">
                            <div class="card mb-4 shadow-sm">
                              <img src="<?= $this->url() . $stock['chemin'] ?>" class="card-img-top img-fluid" alt="<?= $stock['nom'] ?>" style="height: 120px; width: 220px;">

                              <div class="card-body">
                                <h6 class="card-title"><?= $stock['nom'] ?></h6>
                                <p class="card-text" style="font-size: 0.8rem;">Référence: <?= $stock['reference'] ?></p>
                                <p class="card-text" style="font-size: 0.8rem;">Quantité disponible: <?= $stock['quantite'] ?></p>
                                <div class="form-group">
                                  <label for="product_quantity_<?= $stock['id_stock'] ?>" style="font-size: 0.8rem;">Quantité:</label>
                                  <input type="number" value="1" name="product_quantity_<?= $stock['id_stock'] ?>" max="<?= $stock['quantite'] ?>" class="form-control" placeholder="Quantité">
                                </div>
                                <div class="form-check">
                                  <input type="checkbox" name="selected_products[]" value="<?= $stock['id_stock'] ?>" class="form-check-input">
                                  <label class="form-check-label" for="product_<?= $stock['id_stock'] ?>" style="font-size: 0.8rem;">Sélectionner</label>
                                </div>
                              </div>
                            </div>
                          </div>
                          <?php $count++; ?>
                          <?php if ($count % 2 == 0) : ?>
                      </div>
                      <div class="row">
                      <?php endif; ?>
                    <?php endforeach; ?>
                      </div>
                    </div>
                  </div>

                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                    <button type="submit" name="add_new_intervention" class="btn btn-primary">Ajouter</button>
                  </div>
            </form>
          </div>
        </div>
      </div>



      <script>
        function toggleOtherField(selectElement) {
          var autreField = document.getElementById("autreField");
          var autreCategorieInput = document.getElementById("autreCategorie");

          if (selectElement.value === "") {
            autreField.style.display = "block";
            autreCategorieInput.required = true;
          } else {
            autreField.style.display = "none";
            autreCategorieInput.required = false;
          }
        }

        function searchClients() {
          var searchValue = document.getElementById('searchClient').value;
          if (searchValue.length >= 5) {
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
              if (this.readyState == 4 && this.status == 200) {
                var clients = JSON.parse(this.responseText);
                displaySearchResults(clients);
              }
            };
            xhttp.open("GET", "index.php?action=rechercheClient&query=" + searchValue, true);
            xhttp.send();
          } else {
            document.getElementById('searchResults').innerHTML = '';
          }
        }

        function displaySearchResults(clients) {
          var searchResultsDiv = document.getElementById('searchResults');
          searchResultsDiv.innerHTML = '';

          clients.forEach(function(client) {
            var clientListItem = document.createElement('a');
            clientListItem.classList.add('list-group-item');
            clientListItem.classList.add('list-group-item-action');

            clientListItem.innerHTML = `
                  <div class="d-flex w-100 justify-content-between">
                      <h5 class="mb-1">${client.nom} ${client.prenom}</h5>
                  </div>
                  <br>
                  <p class="mb-1">Email: ${client.email}</p>
                  <p>Téléphone: ${client.telephone}</p>
              `;
            clientListItem.addEventListener('click', function() {
              document.getElementById('searchClient').value = client.nom + ' ' + client.prenom + ' - ' + client.email + ' - ' + client.telephone;
              document.getElementById('selectedClientId').value = client.id_utilisateur;
            });
            searchResultsDiv.appendChild(clientListItem);
            var br = document.createElement('br');
            searchResultsDiv.appendChild(br);
          });
        }


        function openAddInterventionModal() {
          $('#interventionModal').modal('show');
        }

        var currentWeekStart = new Date();
        var currentWeekEnd = new Date();
        setCurrentWeekRange();

        function getSelectedTechnicienInterventions() {
          var selectedTechnicienId = document.getElementById('selectedTechnicien').value;
          var weekStart = currentWeekStart.toISOString().split('T')[0];
          var weekEnd = currentWeekEnd.toISOString().split('T')[0];
          var xhttp = new XMLHttpRequest();
          xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
              var interventions = JSON.parse(this.responseText);
              var days = ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];

              for (var i = 0; i < days.length; i++) {
                for (var hour = 8; hour < 18; hour++) {
                  for (var minutes = 0; minutes < 60; minutes += 30) {
                    var cell = document.getElementById(days[i].toLowerCase() + '-' + ('0' + hour).slice(-2) + ':' + ('0' + minutes).slice(-2));
                    if (cell) {
                      cell.innerHTML = '';
                      cell.className = '';
                    }
                  }
                }
              }

              interventions.forEach(function(intervention) {
                var dateParts = intervention.date_intervention.split(/[- :]/);
                var year = parseInt(dateParts[0], 10);
                var month = parseInt(dateParts[1], 10) - 1;
                var day = parseInt(dateParts[2], 10);
                var hour = parseInt(dateParts[3], 10);
                var minutes = parseInt(dateParts[4], 10);
                var dateDebut = new Date(year, month, day, hour, minutes);

                var dureeParts = intervention.duree_intervention.split(':');
                var dureeHours = parseInt(dureeParts[0], 10);
                var dureeMinutes = parseInt(dureeParts[1], 10);

                var dateFin = new Date(dateDebut.getTime() + (dureeHours * 60 + dureeMinutes) * 60000);

                var dayOfWeek = dateDebut.getDay();
                var startCell = document.getElementById(days[dayOfWeek].toLowerCase() + '-' + ('0' + hour).slice(-2) + ':' + ('0' + minutes).slice(-2));

                var texteTypeIntervention;
                if (intervention.type === "intervention") {
                  texteTypeIntervention = "Interv. n°";
                } else if (intervention.type === "pre-visite") {
                  texteTypeIntervention = "Pré-visite n°";
                } else {
                  texteTypeIntervention = "Type inconnu n°";
                }
                if (startCell) {
                  var interventionHtml = '<div class="container"><a href="index.php?action=details_intervention&intervention=' + intervention.id_intervention + '" target="_blank"><div class="intervention_planning">';
                  interventionHtml += '<span class="text-dark"><p class="text-center text-dark"><strong>' + texteTypeIntervention + intervention.id_intervention + '</strong></p>';
                  interventionHtml += '<span class="text-dark">' + intervention.prenom_client + ' ' + intervention.nom_client + '</span>';
                  switch (intervention.statut) {
                    case 'A faire':
                      interventionHtml += '<p><i class="bi bi-hourglass"></i> ' + intervention.statut + '</p>';
                      break;
                    case 'En cours':
                      interventionHtml += '<p><i class="bi bi-arrow-repeat"></i> ' + intervention.statut + '</p>';
                      break;
                    case 'Terminée':
                      interventionHtml += '<p class="text-success"><i class="bi bi-check-circle-fill"></i> ' + intervention.statut + '</p>';
                      break;
                    case 'Reportée':
                      interventionHtml += '<p><i class="bi bi-exclamation-triangle"></i> ' + intervention.statut + '</p>';
                      break;
                    case 'Validée':
                      interventionHtml += '<p class="text-success"><i class="bi bi-check-circle-fill"></i>' + intervention.statut + '</p>';
                      break;
                    case 'Refusée':
                      interventionHtml += '<p class="text-danger"><i class="bi bi-x-circle-fill"></i>' + intervention.statut + '</p>';
                      break;

                    default:
                      interventionHtml += '<p>Statut</p>';
                      break;
                  }
                  interventionHtml += '</div>';
                  startCell.innerHTML += interventionHtml;


                  switch (intervention.categorie) {
                    case 'Fibre optique':
                      startCell.className = 'fibre-optique';
                      break;
                    case 'Maison Connectée':
                      startCell.className = 'maison-connectee';
                      break;
                    case 'Borne de recharge':
                      startCell.className = 'borne-de-recharge';
                      break;
                    case 'Energie solaire':
                      startCell.className = 'energie-solaire';
                      break;
                    case 'Electricité':
                      startCell.className = 'electricite';
                      break;
                    default:
                      startCell.className = 'autre-categorie';
                      break;
                  }

                  var durationHours = dureeHours + (dureeMinutes / 60);
                  startCell.rowSpan = durationHours * 2;

                  for (var h = 1; h < durationHours * 2; h++) {
                    var extraCell = document.getElementById(days[dayOfWeek].toLowerCase() + '-' + ('0' + (hour + Math.floor(h / 2))).slice(-2) + ':' + ((h % 2 === 0) ? '00' : '30'));
                    if (extraCell) {
                      extraCell.parentNode.removeChild(extraCell);
                    }
                  }
                }
              });
            }
          };
          xhttp.open("GET", "index.php?action=getInterventionsByTechnicienId&selectedTechnicienId=" + selectedTechnicienId + "&weekStart=" + weekStart + "&weekEnd=" + weekEnd, true);
          xhttp.send();
        }

        function previousWeek() {
          currentWeekStart.setDate(currentWeekStart.getDate() - 7);
          currentWeekEnd.setDate(currentWeekEnd.getDate() - 7);
          setCurrentWeekRange();
          getSelectedTechnicienInterventions();
        }

        function nextWeek() {
          currentWeekStart.setDate(currentWeekStart.getDate() + 7);
          currentWeekEnd.setDate(currentWeekEnd.getDate() + 7);
          setCurrentWeekRange();
          getSelectedTechnicienInterventions();
        }

        function setCurrentWeekRange() {
          var monday = new Date(currentWeekStart);
          while (monday.getDay() !== 1) {
            monday.setDate(monday.getDate() - 1);
          }

          var sunday = new Date(monday);
          sunday.setDate(monday.getDate() + 6);

          currentWeekStart = monday;
          currentWeekEnd = sunday;

          var weekNumber = getWeekNumber(currentWeekStart);
          document.getElementById('weekRange').innerHTML = currentWeekStart.toLocaleDateString('fr-FR') + ' - ' + currentWeekEnd.toLocaleDateString('fr-FR');
          document.getElementById('weekNumber').innerHTML = 'Semaine n° ' + weekNumber;
        }

        function getWeekNumber(date) {
          var d = new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate()));
          var dayNum = d.getUTCDay() || 7;
          d.setUTCDate(d.getUTCDate() + 4 - dayNum);
          var yearStart = new Date(Date.UTC(d.getUTCFullYear(), 0, 1));
          return Math.ceil((((d - yearStart) / 86400000) + 1) / 7);
        }
      </script>

    <?php
    $this->footer();
  }


  public function detailsIntervention($interventions, $technicien, $client, $stocks, $totalstock, $successMess, $errorMess)
  {
    $this->header('Détails intervention');
    $this->tabs();

    $jours = array('Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi');
    $mois = array('Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre');
    $date_intervention = new DateTime($interventions['date_intervention']);

    $jour_semaine = $jours[$date_intervention->format('w')];
    $jour = $date_intervention->format('j');
    $mois = $mois[$date_intervention->format('n') - 1];
    $annee = $date_intervention->format('Y');
    $heure = $date_intervention->format('H\hi');

    $duree_intervention = $interventions['duree_intervention'];
    $duree_secondes = strtotime($duree_intervention) - strtotime('TODAY');
    $heures = floor($duree_secondes / 3600);
    $minutes = floor(($duree_secondes % 3600) / 60);
    $duree_formattee = '';
    if ($heures > 0) {
      $duree_formattee .= $heures . ' heure';
      if ($heures > 1) {
        $duree_formattee .= 's';
      }
      $duree_formattee .= ' ';
    }
    if ($minutes > 0) {
      $duree_formattee .= $minutes . ' minute';
      if ($minutes > 1) {
        $duree_formattee .= 's';
      }
    }
    $categorie = $interventions['categorie'];
    $classe_css = '';
    $style_css = '';
    switch ($categorie) {
      case 'Fibre optique':
        $classe_css = 'bg-primary text-white';
        break;
      case 'Maison Connectée':
        $classe_css = 'text-white';
        $style_css = 'background-color: rgba(133, 51, 255);';
        break;
      case 'Energie solaire':
        $classe_css = 'text-white';
        $style_css = 'background-color: rgba(255, 102, 0);';
        break;
      case 'Borne de recharge':
        $classe_css = 'text-white';
        $style_css = 'background-color: rgba(0, 230, 0);';
        break;
      case 'Electricité':
        $classe_css = 'bg-danger text-white';
        break;
      default:
        $classe_css = 'bg-secondary text-white';
        break;
    }

    $statut = $interventions['statut'];
    $statutIntervention = '';
    switch ($statut) {
      case 'A faire':
        $statutIntervention = '<p class="text-muted"><i class="bi bi-hourglass"></i> ' . $statut . '</p>';
        break;
      case 'En cours':
        $statutIntervention = '<p class="text-success"><i class="bi bi-arrow-repeat"></i> ' . $statut . '</p>';
        break;
      case 'Terminée':
        $statutIntervention = '<p class="text-primary"><i class="bi bi-check-circle-fill"></i> ' . $statut . '</p>';
        break;
      case 'Reportée':
        $statutIntervention = '<p class="text-danger"><i class="bi bi-exclamation-triangle"></i> ' . $statut . '</p>';
        break;
      case 'Validée':
        $statutIntervention = '<p class="text-success"><i class="bi bi-check-circle-fill"></i> ' . $statut . '</p>';
        break;
      case 'Refusée':
        $statutIntervention = '<p class="text-danger"><i class="bi bi-x-circle-fill"></i> ' . $statut . '</p>';
        break;
      default:
        $statutIntervention = '<p>Statut introuvable</p>';
        break;
    }
    ?>

      <div class="container-fluid mt-5">
        <?php
        if (isset($errorMess)) {
          $this->errorMessage($errorMess);
        }
        if (isset($successMess)) {
          $this->successMessage($successMess);
        }
        ?>
        <h2 class="text-center mt-3">Intervention n°<?= $interventions['id_intervention'] ?></h2>
        <h4 class="text-center mt-3"><?= $statutIntervention ?></h4>
        <div class="d-flex justify-content-end mr-3">

          <?php if ($interventions['statut'] == "Terminée") : ?>
            <a href="index.php?action=compte_rendu&intervention=<?= $interventions['id_intervention'] ?>" class="mr-2">
              <button type="button" class="btn btn-warning">
                <i class="bi bi-file-earmark-text"></i> Compte rendu
              </button>
            </a>
          <?php endif; ?>

          <a href="index.php?action=pieces_jointes&intervention=<?= $interventions['id_intervention'] ?>" class="mr-2">
            <button class="btn btn-info"><i class="bi bi-paperclip"></i> Pièces jointes</button>
          </a>
          <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteIntervention">
            <i class="bi bi-trash"></i> Supprimer cette intervention
          </button>
        </div>

        <!-- popup supprimer intervention -->
        <div class="modal" id="deleteIntervention" tabindex="-1" role="dialog">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Confirmation de suppression</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer cette intervention ?</p>
                <form method="post" action="">
                  <input type="hidden" id="intervention_id" name="id_intervention_delete" value="<?= $interventions['id_intervention'] ?>">
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-link" data-dismiss="modal">Annuler</button>
                <button type="submit" class="btn btn-danger" name="delete_intervention">Confirmer</button>
              </div>
              </form>
            </div>
          </div>
        </div>
        <div class="row my-3">
          <div class="col-md-6">
            <div class="card">
              <h5 class="card-header bg-success text-white">Client</h5>
              <div class="card-body">
                <h4 class="text-center mt-0 mb-3"><?= $client['prenom'] . " " . $client['nom'] ?></h4>
                <p><strong>Email :</strong> <?= $client['email'] ?></p>
                <p><strong>Téléphone :</strong> <?= $client['telephone'] ?></p>
                <p><strong>Adresse :</strong> <?= $client['adresse'] . "<br>" . $client['cp'] . " " . $client['ville'] ?></p>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="card">
              <h5 class="card-header text-white" style="background-color: #FFA500;">Technicien</h5>
              <div class="card-body">
                <h4 class="text-center mt-0 mb-3"><?= $technicien['prenom'] . " " . $technicien['nom'] ?></h4>
                <p><strong>Email :</strong> <?= $technicien['email'] ?></p>
                <p><strong>Téléphone :</strong> <?= $technicien['telephone'] ?></p>
                <p><strong>Adresse :</strong> <?= $technicien['adresse'] . "<br>" . $technicien['cp'] . " " . $technicien['ville'] ?></p>
              </div>
            </div>
          </div>
        </div>
        <div class="row mt-4">
          <div class="col-md-12">
            <div class="card">
              <h5 class="card-header <?= $classe_css ?>" style="<?= $style_css ?>">Détails de l'Intervention</h5>
              <div class="card-body">
                <h4 class="text-center mt-0 mb-3"><?= $categorie ?></h4>
                <p><strong>Date de l'intervention :</strong> <?= "$jour_semaine $jour $mois $annee à $heure" ?></p>
                <p><strong>Durée :</strong> <?= $duree_formattee ?></p>
                <p><strong>Statut :</strong> <?= $interventions['statut'] ?></p>
                <p><strong>Description :</strong>
                  <span id="description">
                    <?php
                    $description = $interventions['description'];
                    if (strlen($description) > 300) {
                      $truncated_description = substr($description, 0, 300);
                      echo $truncated_description;
                    } else {
                      echo $description;
                    }
                    ?>
                  </span>
                  <?php if (strlen($description) > 300) { ?>
                    <a href="#" id="voir-plus" class="">Voir plus</a>
                  <?php } ?>
                </p>
              </div>
            </div>
          </div>
        </div>

        <div class="row mt-4">
          <div class="col-md-12">
            <div class="card">
              <h5 class="card-header bg-info text-white">Stocks utilisés</h5>
              <div class="card-body">
                <div class="row">
                  <?php foreach ($stocks as $stock) : ?>
                    <div class="col-md-2 mb-3">
                      <div class="card h-100">
                        <img src="<?= $this->url() . $stock['chemin'] ?>" class="card-img-top img-fluid" alt="<?= $stock['nom'] ?>" style="height: 150px;"> <!-- Fixation de la hauteur de l'image -->
                        <div class="card-body">
                          <h5 class="card-title"><?= $stock['nom'] ?></h5>
                          <p class="card-text"><strong>Référence :</strong> <?= $stock['reference'] ?></p>
                          <p class="card-text"><strong>Quantité :</strong> <?= $stock['quantite_utilisee'] ?></p>
                        </div>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>
              <div class="card-footer text-right">
                <button type="button" id="addStockModalBtn" class="btn btn-primary" data-toggle="modal" data-target="#addStockModal">Ajouter du stock</button>
              </div>
            </div>
          </div>
        </div>


        <!-- popup ajout stock -->
        <div class="modal fade" id="addStockModal" tabindex="-1" role="dialog" aria-labelledby="addStockModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="addStockModalLabel">Ajouter du stock</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <form method="post" action="">
                <div class="modal-body">
                  <div class="form-group">
                    <label for="selectedStock">Sélectionner le stock :</label>
                    <select class="form-control" id="selectedStock" name="selected_stock">
                      <?php foreach ($totalstock as $stock) : ?>
                        <option value="<?= $stock['id_stock'] ?>">
                          <?= $stock['nom'] ?> - <?= $stock['reference'] ?> (<?= $stock['quantite'] ?> disponibles)
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="quantity">Quantité :</label>
                    <input type="number" class="form-control" id="quantity" name="quantity" min="1" max="<?= $stock['quantite'] ?>">
                  </div>

                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                  <button type="submit" name="add_stock" class="btn btn-primary">Ajouter</button>
                </div>
              </form>
            </div>
          </div>
        </div>


      </div>


      <script>
        var fullDescription = <?= json_encode($description) ?>;
        var truncatedDescription = <?= isset($truncated_description) ? json_encode($truncated_description) : "''" ?>;
        var isFullDescriptionShown = false;

        document.addEventListener("DOMContentLoaded", function() {
          var voirPlusBtn = document.getElementById('voir-plus');
          var descriptionSpan = document.getElementById('description');
          if (voirPlusBtn) {
            voirPlusBtn.addEventListener('click', function(event) {
              event.preventDefault();
              if (isFullDescriptionShown) {
                descriptionSpan.innerHTML = truncatedDescription;
                voirPlusBtn.innerHTML = 'Voir plus';
              } else {
                descriptionSpan.innerHTML = fullDescription;
                voirPlusBtn.innerHTML = 'Voir moins';
              }
              isFullDescriptionShown = !isFullDescriptionShown;
            });
          }
        });
      </script>

    <?php
    $this->footer();
  }

  public function compte_rendu($cri, $errorMess, $successMess)
  {
    $this->header("Compte Rendu");
    $this->tabs();
    ?>
      <div class="container-fluid mt-4">
        <?php
        if (isset($errorMess)) {
          $this->errorMessage($errorMess);
        }
        if (isset($successMess)) {
          $this->successMessage($successMess);
        }
        ?>
        <div class="row">
          <div class="col-md-6">
            <div class="card border-primary">
              <div class="card-header bg-primary text-white">
                Actions
              </div>
              <div class="card-body">
                <p class="card-text"><?= !empty($cri['actions']) ? $cri['actions'] : 'Aucune action enregistrée' ?></p>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="card border-success">
              <div class="card-header bg-success text-white">
                Équipements
              </div>
              <div class="card-body">
                <p class="card-text"><?= !empty($cri['equipements']) ? $cri['equipements'] : 'Aucun équipement enregistré' ?></p>
              </div>
            </div>
          </div>
        </div>
        <div class="row mt-4">
          <div class="col-md-6">
            <div class="card border-danger">
              <div class="card-header bg-danger text-white">
                Problèmes
              </div>
              <div class="card-body">
                <p class="card-text"><?= !empty($cri['problemes']) ? $cri['problemes'] : 'Aucun problème enregistré' ?></p>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="card border-warning">
              <div class="card-header bg-warning text-white">
                Observations
              </div>
              <div class="card-body">
                <p class="card-text"><?= !empty($cri['observations']) ? $cri['observations'] : 'Aucune observation enregistrée' ?></p>
              </div>
            </div>
          </div>
        </div>

        <div class="row mt-4">
          <div class="col-md-6">
            <div class="card border-info">
              <div class="card-header bg-info text-white">
                Signature Client
              </div>
              <div class="card-body">
                <?php if (!empty($cri['signature_client'])) : ?>
                  <img src="<?= $this->url() . $cri['signature_client'] ?>" class="img-fluid" alt="Signature Client">
                <?php else : ?>
                  <p class="card-text">Aucune signature client enregistrée</p>
                <?php endif; ?>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="card border-info">
              <div class="card-header bg-info text-white">
                Signature Technicien
              </div>
              <div class="card-body">
                <?php if (!empty($cri['signature_technicien'])) : ?>
                  <img src="<?= $this->url() . $cri['signature_technicien'] ?>" class="img-fluid" alt="Signature Technicien">
                <?php else : ?>
                  <p class="card-text">Aucune signature technicien enregistrée</p>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>

        <div class="row mt-4">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header bg-secondary text-white">
                Validation du compte rendu
              </div>
              <div class="card-body">
                <form action="" method="post">
                  <input type="hidden" name="id_cri" value="<?= $cri['id_cri'] ?>">
                  <div class="form-group">
                    <label for="validation">Validation du compte rendu :</label>
                    <select class="form-control" id="validation" name="validation">
                      <option value="valider">Valider</option>
                      <option value="refuser">Refuser</option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="commentaire">Commentaire : <small>(facultatif)</small></label>
                    <textarea class="form-control" id="commentaire" name="commentaire" rows="3" placeholder="Saisissez votre commentaire ici"></textarea>
                  </div>
                  <button type="submit" name="validation_cri" class="btn btn-primary">Soumettre</button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>

    <?php
    $this->footer();
  }


  public function pieces_jointes($interventions, $client, $plan, $facture, $devis, $successMess, $errorMess)
  {
    $this->header('Planning des Interventions');
    $this->tabs();
    ?>
      <div class="container-fluid mt-5">
        <h2 class="text-center mt-3">Pièces jointes - Interv. n°<?= $interventions['id_intervention'] ?></h2>
        <?php
        if (isset($errorMess)) {
          $this->errorMessage($errorMess);
        }
        if (isset($successMess)) {
          $this->successMessage($successMess);
        }
        ?>
        <div class="row mt-4">
          <div class="col text-center">
            <button class="btn btn-primary" data-toggle="modal" data-target="#ajouterDocumentModal">Ajouter un document</button>
          </div>
        </div>

        <div class="modal" id="ajouterDocumentModal" tabindex="-1" role="dialog">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Ajouter un document</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <form method="post" action="" enctype="multipart/form-data">
                  <div class="form-group">
                    <label for="nom_affichage">Nom d'affichage</label>
                    <input type="text" class="form-control" name="nom_affichage" id="nom_affichage" placeholder="Facture de Monsieur X">
                  </div>
                  <div class="form-group">
                    <label for="typeDocument">Type de document</label>
                    <select class="form-control" id="typeDocument" name="typeDocument">
                      <option value="plan">Documentation technique</option>
                      <option value="facture">Facture</option>
                      <option value="devis">Devis</option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="file">Choisir le fichier (PDF uniquement)</label>
                    <input type="file" class="form-control-file" id="file" name="file_document" accept=".pdf">
                  </div>
                  <button type="submit" name="uploadFichier" class="btn btn-primary">Ajouter</button>
                </form>
              </div>
            </div>
          </div>
        </div>

        <div class="row mt-4">
          <div class="col-lg-4 mb-4">
            <div class="card">
              <div class="card-header text-center">
                <h5 class="card-title h4">Documentation technique</h5>
              </div>
              <div class="card-body">
                <?php foreach ($plan as $document) : ?>
                  <a href="<?= $this->url() . $document['chemin'] ?>" target="_blank">
                    <i class="bi bi-file-pdf text-danger fs-3"></i>
                    <span class="fs-5"><?= $document['nom'] ?></span>
                  </a><br>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
          <div class="col-lg-4 mb-4">
            <div class="card">
              <div class="card-header text-center">
                <h5 class="card-title h4">Factures</h5>
              </div>
              <div class="card-body">
                <?php foreach ($facture as $document) : ?>
                  <a href="<?= $this->url() . $document['chemin'] ?>" target="_blank">
                    <i class="bi bi-file-pdf text-danger fs-3"></i>
                    <span class="fs-5"><?= $document['nom'] ?></span>
                  </a><br>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
          <div class="col-lg-4 mb-4">
            <div class="card">
              <div class="card-header text-center">
                <h5 class="card-title h4">Devis</h5>
              </div>
              <div class="card-body">
                <?php foreach ($devis as $document) : ?>
                  <a href="<?= $this->url() . $document['chemin'] ?>" target="_blank">
                    <i class="bi bi-file-pdf text-danger fs-3"></i>
                    <span class="fs-5"><?= $document['nom'] ?></span>
                  </a><br>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
        </div>
      </div>
      </div>
    <?php
    $this->footer();
  }


  public function technicienTab($techniciens)
  {
    $this->header('Technicien');
    $this->tabs();
    ?>
      <div class="container-fluid">
        <div class="row justify-content-center">
          <div class="col-md-10">
            <h2 class="card-title text-center mb-4 mt-4">Vos Techniciens</h2>
            <div class="card">
              <div class="card-body">
                <div class="d-flex justify-content-end mb-3">
                  <a href="index.php?action=ajouter_technicien" class="btn btn-primary">Ajouter un technicien</a>
                </div>
                <?php
                if (!empty($techniciens)) {
                  $count = 0;
                  foreach ($techniciens as $technicien) {
                    if ($count % 3 === 0) {
                      echo '<div class="row">';
                    }
                ?>
                    <div class="col-md-4 mb-4">
                      <a href="index.php?action=technicien_details&technicien=<?= $technicien['id_utilisateur'] ?>" class="card-link">
                        <div class="card h-100 shadow">
                          <img src="<?= $this->url() . $technicien['pdp'] ?>" class="card-img-top" style="height: 350px; object-fit: cover;" alt="Photo de profil">
                          <div class="card-body">
                            <h5 class="card-title"><?= $technicien['nom'] ?></h5>
                            <p class="card-text"><strong>Email:</strong> <?= $technicien['email'] ?></p>
                            <p class="card-text"><strong>Téléphone:</strong> <?= $technicien['telephone'] ?></p>
                          </div>
                        </div>
                      </a>
                    </div>
                <?php
                    if ($count % 3 === 2 || $count === count($techniciens) - 1) {
                      echo '</div>';
                    }
                    $count++;
                  }
                } else {
                  echo '<div class="alert alert-info" role="alert">Aucun technicien trouvé.</div>';
                }
                ?>
              </div>
            </div>
          </div>
        <?php
        $this->footer();
      }


      public function technicien_details(
        $technicien,
        $documents,
        $totalIntervention,
        $nombreInterventionsTerminees,
        $nombreInterventionValidees,
        $nombreInterventionRefusee,
        $nombreInterventionAfaire,
        $nombreInterventionEnCours,
        $nombreInterventionReportee,
        $selectedMonth,
        $selectedYear,
        $years,
        $errorMess,
        $successMess
      ) {
        $this->header('Détails du Technicien');
        $this->tabs();
        ?>
          <div class="container-fluid">
            <?php
            if (isset($errorMess)) {
              $this->errorMessage($errorMess);
            }
            if (isset($successMess)) {
              $this->successMessage($successMess);
            }
            ?>
            <div class="row justify-content-center">
              <div class="col-md-10">
                <h2 class="card-title text-center mb-4 mt-4">Détails de <?= $technicien['prenom'] ?></h2>
                <div class="card mt-3">
                  <div class="card-body">
                    <div class="row">
                      <div class="col-md-4">
                        <h5 class="card-title"><?= $technicien['prenom'] . " " . $technicien['nom'] ?></h5>
                        <p class="card-text"><strong>Email :</strong> <?= $technicien['email'] ?></p>
                        <p class="card-text"><strong>Téléphone :</strong> <?= $technicien['telephone'] ?></p>
                        <p class="card-text"><strong>Adresse :</strong> <?= $technicien['adresse'] ?>, <?= $technicien['cp'] ?> <?= $technicien['ville'] ?></p>
                        <hr>
                      </div>
                      <div class="col-md-4">
                        <h4 class="text-center">Documents</h4>
                        <ul class="list-group" style="max-height: 100px; overflow-y: auto;">
                          <?php foreach ($documents as $document) : ?>
                            <?php if (strpos($document['chemin'], $technicien['id_utilisateur'] . "_technicien") !== false) : ?>
                              <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                  <a href="<?= $this->url() . $document['chemin'] ?>" target="_blank">
                                    <i class="bi bi-file-text"></i>
                                    <?= $document['nom_affichage'] ?>
                                  </a>
                                </div>
                                <form method="post">
                                  <div class="form-check text-center">
                                    <label class="checkbox-btn">
                                      <input id="checkbox" type="checkbox" name="id_fichier[]" value="<?php echo $document['id_fichiers']; ?>">
                                      <span class="checkmark"></span>
                                    </label>
                                  </div>
                              </li>
                            <?php endif; ?>
                          <?php endforeach; ?>
                        </ul>
                      </div>
                      <div class="col-md-4">
                        <img src="<?= $this->url() . $technicien['pdp'] ?>" class="img-thumbnail rounded-circle" alt="Photo de profil" width="150" height="150">
                      </div>
                    </div>
                  </div>
                  <div class="card-footer">
                    <div class="row justify-content-center">
                      <div class="col-md-4 text-center">
                        <a href="index.php?action=modifier_technicien&technicien=<?= $technicien['id_utilisateur'] ?>" class="btn btn-primary">Modifier le technicien</a>
                      </div>
                      <div class="col-md-8">
                        <button type="button" class="btn btn-success" id="ouvrirModal">Ajouter un document</button>
                        <button type="submit" name="delete_documents" class="btn btn-danger">Supprimer les documents</button>
                      </div>
                    </div>
                  </div>
                  </form>
                </div>

                <div class="card mt-3">
                  <div class="card-body">
                    <h3>Statistiques des interventions</h3>
                    <form method="get" action="index.php">
                      <div class="row justify-content-center">
                        <div class="col-md-4 text-center">
                          <label for="selectedMonth">Mois</label>
                          <select name="selectedMonth" id="selectedMonth" class="form-control">
                            <option value="">Tous les mois</option>
                            <option value="01" <?php if ($selectedMonth == "01") echo "selected"; ?>>Janvier</option>
                            <option value="02" <?php if ($selectedMonth == "02") echo "selected"; ?>>Février</option>
                            <option value="03" <?php if ($selectedMonth == "03") echo "selected"; ?>>Mars</option>
                            <option value="04" <?php if ($selectedMonth == "04") echo "selected"; ?>>Avril</option>
                            <option value="05" <?php if ($selectedMonth == "05") echo "selected"; ?>>Mai</option>
                            <option value="06" <?php if ($selectedMonth == "06") echo "selected"; ?>>Juin</option>
                            <option value="07" <?php if ($selectedMonth == "07") echo "selected"; ?>>Juillet</option>
                            <option value="08" <?php if ($selectedMonth == "08") echo "selected"; ?>>Août</option>
                            <option value="09" <?php if ($selectedMonth == "09") echo "selected"; ?>>Septembre</option>
                            <option value="10" <?php if ($selectedMonth == "10") echo "selected"; ?>>Octobre</option>
                            <option value="11" <?php if ($selectedMonth == "11") echo "selected"; ?>>Novembre</option>
                            <option value="12" <?php if ($selectedMonth == "12") echo "selected"; ?>>Décembre</option>
                          </select>
                        </div>
                        <div class="col-md-4 text-center">
                          <label for="selectedYear">Année</label>
                          <select name="selectedYear" id="selectedYear" class="form-control">
                            <option value="">Toutes les années</option>
                            <?php
                            foreach ($years as $year) {
                              echo '<option value="' . $year . '"';
                              if ($selectedYear == $year) {
                                echo " selected";
                              }
                              echo ">" . $year . "</option>";
                            }
                            ?>
                          </select>
                        </div>
                      </div>
                      <div class="row justify-content-center">
                        <div class="col-md-4">
                          <button type="submit" class="btn btn-primary btn-block mt-3">Filtrer</button>
                          <br><br>
                        </div>
                      </div>
                      <input type="hidden" name="action" value="technicien_details">
                      <input type="hidden" name="technicien" value="<?= $technicien['id_utilisateur'] ?>">
                    </form>
                    <canvas id="chartInterTech" width="400" height="200"></canvas>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- popup ajouter document -->
        <div class="modal fade" id="ajouterDocumentModal" tabindex="-1" aria-labelledby="ajouterDocumentModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="ajouterDocumentModalLabel">Ajouter un document</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <form method="post" enctype="multipart/form-data">
                  <div class="mb-3">
                    <label for="nomAffichage" class="form-label">Nom d'affichage du document</label>
                    <input type="text" class="form-control" id="nomAffichage" name="nomAffichage" placeholder="Nom d'affichage">
                  </div>
                  <div class="mb-3">
                    <label for="fichier" class="form-label">Fichier PDF</label>
                    <input type="file" class="form-control" id="fichier" name="fichier" accept=".pdf">
                  </div>
                  <button type="submit" name="upload_doc_technicien" class="btn btn-primary">Ajouter</button>
                </form>
              </div>
            </div>
          </div>
        </div>


        <script>
          document.getElementById('ouvrirModal').addEventListener('click', function() {
            var myModal = new bootstrap.Modal(document.getElementById('ajouterDocumentModal'));
            myModal.show();
          });

          var ctx = document.getElementById("chartInterTech").getContext("2d");
          var myChart = new Chart(ctx, {
            type: "bar",
            data: {
              labels: ["Interventions validées : <?= $nombreInterventionValidees ?>", "Interventions refusée : <?= $nombreInterventionRefusee ?>", "Intervention à faire <?= $nombreInterventionAfaire ?>", "Intervention en cours <?= $nombreInterventionEnCours ?>", "Interventions reportées <?= $nombreInterventionReportee ?>"],
              datasets: [{
                label: "Nombre d'interventions totales : <?= $totalIntervention ?>",
                data: [<?= $nombreInterventionValidees ?>, <?= $nombreInterventionRefusee ?>, <?= $nombreInterventionAfaire ?>, <?= $nombreInterventionEnCours ?>, <?= $nombreInterventionReportee ?>],
                backgroundColor: [
                  "rgba(0, 102, 255)",
                  "rgba(255, 83, 26)",
                  "rgba(179, 179, 179)",
                  "rgba(0, 153, 51)",
                  "rgba(255, 83, 26)"
                ],
                borderColor: [
                  "rgba(0, 102, 255, 1)",
                  "rgba(255, 83, 26)",
                  "rgba(179, 179, 179, 1)",
                  "rgba(0, 153, 51, 1)",
                  "rgba(255, 83, 26, 1)"
                ],
                borderWidth: 1
              }]
            },
            options: {
              scales: {
                y: {
                  beginAtZero: true,
                  max: <?= $totalIntervention ?>
                }
              },
              plugins: {
                legend: {
                  labels: {
                    font: {
                      size: 16
                    }
                  }
                }
              }
            }
          });
        </script>
      <?php
        $this->footer();
      }

      public function ajouter_technicien($errorMess, $successMess)
      {
        $this->header('Ajouter Technicien');
        $this->tabs();
      ?>
        <div class="container-fluid">
          <div class="row justify-content-center">
            <div class="col-md-10">
              <h2 class="card-title text-center mb-4 mt-4">Ajouter un technicien</h2>
              <?php
              if (isset($errorMess)) {
                $this->errorMessage($errorMess);
              }
              if (isset($successMess)) {
                $this->successMessage($successMess);
              }
              ?>
              <ul class="nav nav-tabs">
                <li class="nav-item">
                  <button class="nav-link active" id="btnCreerTechnicien">Créer un technicien</button>
                </li>
                <li class="nav-item">
                  <button class="nav-link" id="btnAttribuerRole">Attribuer le rôle technicien</button>
                </li>
              </ul>
              <div id="creerTechnicienForm" class="tab-content mt-3" style="display: block;">
                <form action="" method="post" enctype="multipart/form-data">
                  <div class="mb-3">
                    <label class="form-label">Photo</label>
                    <input type="file" class="form-control" id="photo" name="photo" style="display: none;">
                    <div id="preview-container" class="d-flex justify-content-center align-items-center" style="width: 150px; height: 150px; overflow: hidden;">
                      <img id="preview" src="<?= $this->url() . 'pieces_jointe/avatars/placeholder.jpg' ?>" class="img-fluid rounded-circle" alt="Photo de profil">
                    </div>
                  </div>
                  <div class="mb-3">
                    <label for="nom" class="form-label">Nom</label>
                    <input type="text" class="form-control" id="nom" name="nom" placeholder="Nom" value="<?= isset($_POST['nom']) ? htmlspecialchars($_POST['nom']) : '' ?>" required>
                  </div>
                  <div class="mb-3">
                    <label for="prenom" class="form-label">Prénom</label>
                    <input type="text" class="form-control" id="prenom" name="prenom" placeholder="Prénom" value="<?= isset($_POST['prenom']) ? htmlspecialchars($_POST['prenom']) : '' ?>" required>
                  </div>
                  <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Email" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" required>
                  </div>
                  <div class="mb-3">
                    <label for="password" class="form-label">
                      Mot de passe
                    </label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Mot de passe" required>
                    <small id="passwordHelp" class="form-text text-muted">Générer un mot de passe sécurisé <a href="https://www.dashlane.com/fr/features/password-generator" target="_blank">ici</a></small>
                  </div>
                  <div class="mb-3">
                    <label for="password2" class="form-label">Confirmer</label>
                    <input type="password" class="form-control" id="password2" name="password2" placeholder="Confirmer mot de passe" required>
                  </div>
                  <div class="mb-3">
                    <label for="telephone" class="form-label">Téléphone</label>
                    <input type="text" class="form-control" id="telephone" name="telephone" placeholder="Téléphone" value="<?= isset($_POST['telephone']) ? htmlspecialchars($_POST['telephone']) : '' ?>" required>
                  </div>
                  <div class="mb-3">
                    <label for="adresse" class="form-label">Adresse</label>
                    <input type="text" class="form-control" id="adresse" name="adresse" placeholder="Adresse" value="<?= isset($_POST['adresse']) ? htmlspecialchars($_POST['adresse']) : '' ?>" required>
                  </div>
                  <div class="row mb-3">
                    <div class="col-md-6">
                      <label for="code_postal" class="form-label">Code Postal</label>
                      <input type="text" class="form-control" id="code_postal" name="code_postal" placeholder="Code Postal" value="<?= isset($_POST['code_postal']) ? htmlspecialchars($_POST['code_postal']) : '' ?>" required>
                    </div>
                    <div class="col-md-6">
                      <label for="ville" class="form-label">Ville</label>
                      <input type="text" class="form-control" id="ville" name="ville" placeholder="Ville" value="<?= isset($_POST['ville']) ? htmlspecialchars($_POST['ville']) : '' ?>" required>
                    </div>
                  </div>
                  <div class="text-center">
                    <button type="submit" name="add_technicien" class="btn btn-primary btn-block">Ajouter un technicien</button>
                  </div>
                  <br><br><br>
              </div>
              </form>

              <div id="attribuerRoleForm" class="tab-content mt-3" style="display: none;">
                <form action="" method="post" id="attribuerRoleFormSubmit">
                  <div class="mb-3">
                    <label for="searchTechnicien" class="form-label">A qui souhaitez-vous attribuer le rôle ?</label>
                    <div class="input-group">
                      <input type="text" class="form-control" id="searchTechnicien" name="email_add_technicien_role" placeholder="Rechercher par email">
                      <button class="btn btn-primary" type="button" id="confirmAttribuerRole">Attribuer le rôle</button>
                    </div>
                  </div>
                  <div id="searchResults" class="list-group"></div>
                  <input type="hidden" id="selectedUserId" name="id_add_technicien_role" value="">
                </form>
              </div>

              <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="confirmationModalLabel">Confirmation</h5>
                    </div>
                    <div class="modal-body">
                      Êtes-vous sûr de vouloir attribuer ce rôle ?
                    </div>
                    <div class="modal-footer">
                      <button type="submit" name="confirm_update_role_technicien" class="btn btn-primary" form="attribuerRoleFormSubmit">Confirmer</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <script>
          document.getElementById('confirmAttribuerRole').addEventListener('click', function() {
            var popup_confirm = new bootstrap.Modal(document.getElementById('confirmationModal'));
            popup_confirm.show();
          });

          document.getElementById('btnCreerTechnicien').addEventListener('click', function() {
            document.getElementById('creerTechnicienForm').style.display = 'block';
            document.getElementById('attribuerRoleForm').style.display = 'none';
            document.getElementById('btnCreerTechnicien').classList.add('active');
            document.getElementById('btnAttribuerRole').classList.remove('active');
          });

          document.getElementById('btnAttribuerRole').addEventListener('click', function() {
            document.getElementById('creerTechnicienForm').style.display = 'none';
            document.getElementById('attribuerRoleForm').style.display = 'block';
            document.getElementById('btnAttribuerRole').classList.add('active');
            document.getElementById('btnCreerTechnicien').classList.remove('active');
          });

          document.getElementById('searchTechnicien').addEventListener('input', function() {
            var searchValue = this.value.trim();
            if (searchValue !== '') {
              var xhr = new XMLHttpRequest();
              xhr.open('POST', 'index.php?action=rechercher_technicien', true);
              xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
              xhr.onload = function() {
                if (xhr.status === 200) {
                  var results = JSON.parse(xhr.responseText);
                  updateSearchResults(results);
                } else {
                  console.error('Erreur lors de la requête AJAX');
                }
              };
              xhr.send('searchValue=' + encodeURIComponent(searchValue));
            } else {
              clearSearchResults();
            }
          });

          function updateSearchResults(results) {
            var searchResultsDiv = document.getElementById('searchResults');
            searchResultsDiv.innerHTML = '';

            if (results.length > 0) {
              var resultList = document.createElement('div');
              resultList.classList.add('list-group');

              results.forEach(function(result) {
                var listItem = document.createElement('button');
                listItem.textContent = result.email;
                listItem.classList.add('list-group-item', 'list-group-item-action', 'text-start');
                listItem.addEventListener('click', function() {
                  document.getElementById('searchTechnicien').value = result.email;
                  document.getElementById('selectedUserId').value = result.id_utilisateur;
                  clearSearchResults();
                });
                resultList.appendChild(listItem);
              });

              searchResultsDiv.appendChild(resultList);
            } else {
              var noResultsMsg = document.createElement('div');
              noResultsMsg.textContent = 'Aucun résultat trouvé';
              noResultsMsg.classList.add('text-muted', 'mt-3');
              searchResultsDiv.appendChild(noResultsMsg);
            }
          }

          function clearSearchResults() {
            document.getElementById('searchResults').innerHTML = '';
          }
          document.getElementById('photo').addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
              const reader = new FileReader();
              reader.onload = function(e) {
                const preview = document.getElementById('preview');
                preview.src = e.target.result;
              };
              reader.readAsDataURL(file);
            } else {
              document.getElementById('preview').src = '<?= $this->url() . 'pieces_jointe/avatars/placeholder.jpg' ?>';
            }
          });

          document.getElementById("btnCreerTechnicien").addEventListener("click", function() {
            document.getElementById("creerTechnicienForm").style.display = "block";
            document.getElementById("attribuerRoleForm").style.display = "none";
            document.getElementById("btnCreerTechnicien").classList.add("active");
            document.getElementById("btnAttribuerRole").classList.remove("active");
          });

          document.getElementById("btnAttribuerRole").addEventListener("click", function() {
            document.getElementById("creerTechnicienForm").style.display = "none";
            document.getElementById("attribuerRoleForm").style.display = "block";
            document.getElementById("btnAttribuerRole").classList.add("active");
            document.getElementById("btnCreerTechnicien").classList.remove("active");
          });
          document.getElementById('preview').addEventListener('click', function() {
            document.getElementById('photo').click();
          });
        </script>
      <?php
        $this->footer();
      }

      public function modifier_technicien($technicien, $competence, $errorMess, $successMess)
      {
        $this->header('Modifier Technicien');
        $this->tabs();
      ?>

        <div class="container-fluid">
          <div class="row justify-content-center">
            <div class="col-md-10">
              <h2 class="card-title text-center mb-4 mt-4">Modifier un technicien</h2>
              <?php
              if (isset($errorMess)) {
                $this->errorMessage($errorMess);
              }
              if (isset($successMess)) {
                $this->successMessage($successMess);
              }
              ?>
              <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                  <a class="nav-link active" id="informations-tab" data-toggle="tab" href="#informations" role="tab" aria-controls="informations" aria-selected="true">Informations</a>
                </li>
                <li class="nav-item" role="presentation">
                  <a class="nav-link" id="competences-tab" data-toggle="tab" href="#competences" role="tab" aria-controls="competences" aria-selected="false">Compétences</a>
                </li>
              </ul>
              <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="informations" role="tabpanel" aria-labelledby="informations-tab">
                  <div class="card">
                    <div class="card-body">
                      <form action="" method="post" enctype="multipart/form-data">
                        <div class="text-center mb-3">
                          <label for="file-input">
                            <input type="file" name="photo" id="file-input" accept="image/*" style="display: none;">
                            <img src="<?= $this->url() . $technicien['pdp'] ?>" id="profile-img" class="rounded-circle" alt="Photo de profil" width="150" height="150" style="cursor: pointer;">
                          </label>
                        </div>
                        <input type="hidden" name="id_technicien" value="<?= $technicien['id_utilisateur'] ?>">
                        <div class="mb-3">
                          <label for="nom" class="form-label">Nom</label>
                          <input type="text" class="form-control" id="nom" name="nom" value="<?= $technicien['nom'] ?>">
                        </div>
                        <div class="mb-3">
                          <label for="prenom" class="form-label">Prénom</label>
                          <input type="text" class="form-control" id="prenom" name="prenom" value="<?= $technicien['prenom'] ?>">
                        </div>
                        <div class="mb-3">
                          <label for="email" class="form-label">Email</label>
                          <input type="email" class="form-control" id="email" name="email" value="<?= $technicien['email'] ?>">
                        </div>
                        <div class="mb-3">
                          <label for="telephone" class="form-label">Téléphone</label>
                          <input type="text" class="form-control" id="telephone" name="telephone" value="<?= $technicien['telephone'] ?>">
                        </div>
                        <div class="mb-3">
                          <label for="adresse" class="form-label">Adresse</label>
                          <input type="text" class="form-control" id="adresse" name="adresse" value="<?= $technicien['adresse'] ?>">
                        </div>
                        <div class="row mb-3">
                          <div class="col-md-6">
                            <label for="code_postal" class="form-label">Code Postal</label>
                            <input type="text" class="form-control" id="code_postal" name="code_postal" placeholder="Code Postal" value="<?= $technicien['cp'] ?>" required>
                          </div>
                          <div class="col-md-6">
                            <label for="ville" class="form-label">Ville</label>
                            <input type="text" class="form-control" id="ville" name="ville" placeholder="Ville" value="<?= $technicien['ville'] ?>" required>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-md-6">
                            <button type="submit" name="save_technicien" class="btn btn-primary">Enregistrer les modifications</button>
                          </div>
                          <div class="col-md-6 d-flex justify-content-end">
                            <button type="submit" name="delete_role_technicien" class="btn btn-danger">Supprimer le rôle</button>
                          </div>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
                <div class="tab-pane fade" id="competences" role="tabpanel" aria-labelledby="competences-tab">
                  <div class="card">
                    <div class="card-body">
                      <form action="" method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                          <label for="competence" class="form-label">Compétences</label>
                          <textarea class="form-control" id="competence" name="competence" style="resize: none; height: 300px; overflow-y: hidden;"><?= isset($competence['competence']) ? trim($competence['competence']) : '' ?></textarea>
                        </div>
                        <div class="mb-3">
                          <button type="submit" name="competence_technicien" class="btn btn-primary">Enregistrer</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <script>
          document.getElementById('file-input').addEventListener('change', function(event) {
            var file = event.target.files[0];
            var reader = new FileReader();
            reader.onload = function(e) {
              document.getElementById('profile-img').src = e.target.result;
            };
            reader.readAsDataURL(file);
          });
        </script>

      <?php
        $this->footer();
      }


      public function cartoTab()
      {
        $this->header('Cartographie');
        $this->tabs();
        echo 'Carto';

        $this->footer();
      }

      public function clientTab()
      {
        $this->header('Client');
        $this->tabs();
        echo 'Client';

        $this->footer();
      }

      public function stockTab($stock)
      {
        $this->header('Stock');
        $this->tabs();
      ?>

        <div class="container-fluid">
          <div class="row justify-content-center">
            <div class="col-md-10">
              <h2 class="text-center mb-4 mt-4">Stock</h2>
              <div class="text-center mb-4">
                <a href="index.php?action=add_product" class="btn btn-primary">Ajouter un produit</a>
              </div>
              <div class="row row-cols-1 row-cols-md-5">
                <?php foreach ($stock as $item) : ?>
                  <div class="col mb-4">
                    <a href="index.php?action=info_product&product=<?= $item['id_stock'] ?>">
                      <div class="card h-100">
                        <img src="<?= $item['chemin'] ? $this->url() . $item['chemin'] : $this->url() . 'pieces_jointe/stock/produit.jpg' ?>" width="200px" height="200px" class="card-img-top" alt="<?= $item['nom'] ?>">
                        <div class="card-body">
                          <h5 class="card-title"><?= $item['nom'] ?></h5>
                          <p class="card-text">Référence: <?= $item['reference'] ?></p>
                          <p class="card-text">Quantité en stock: <?= $item['quantite'] ?></p>
                        </div>
                      </div>
                    </a>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
        </div>

      <?php
        $this->footer();
      }

      public function add_product($errorMess, $successMess)
      {
        $this->header("Ajouter un produit");
        $this->tabs();
      ?>

        <div class="container-fluid">
          <?php
          if (isset($errorMess)) {
            $this->errorMessage($errorMess);
          }
          if (isset($successMess)) {
            $this->successMessage($successMess);
          }
          ?>
          <div class="row justify-content-center">
            <div class="col-md-8">
              <div class="card mt-4">
                <div class="card-header bg-primary text-white">
                  <h4>Ajouter un produit</h4>
                </div>
                <div class="card-body">
                  <form action="" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                      <label for="nom">Nom du produit</label>
                      <input type="text" class="form-control" id="nom" name="nom" required>
                    </div>
                    <div class="form-group">
                      <label for="description">Description</label>
                      <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                      <label for="reference">Référence</label>
                      <input type="text" class="form-control" id="reference" name="reference" required>
                    </div>
                    <div class="form-group">
                      <label for="quantite">Quantité en stock</label>
                      <input type="number" class="form-control" id="quantite" name="quantite" required>
                    </div>
                    <div class="form-group">
                      <label for="photos">Photo du produit</label>
                      <input type="file" class="form-control-file" id="photos" name="photos[]" multiple accept="image/*" required>
                    </div>

                    <div class="form-group d-flex flex-wrap" id="preview-container"></div>
                    <button type="submit" name="add_product" class="btn btn-primary">Ajouter le produit</button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>

        <script>
          document.getElementById('photos').addEventListener('change', function() {
            const files = this.files;
            const previewContainer = document.getElementById('preview-container');

            for (let i = 0; i < files.length; i++) {
              const file = files[i];
              const reader = new FileReader();

              reader.onload = function(event) {
                const previewItem = document.createElement('div');
                previewItem.classList.add('m-2');
                previewItem.innerHTML = `
            <div class="card" style="width: 150px; height:150px">
                <img src="${event.target.result}" class="card-img-top preview-image" alt="Image preview">
            </div>
        `;
                previewContainer.appendChild(previewItem);
              };

              reader.readAsDataURL(file);
            }
          });
        </script>


      <?php
        $this->footer();
      }


      public function info_product($produit, $errorMess, $successMess)
      {
        $this->header("Produit : " . $produit['reference']);
        $this->tabs();
      ?>

        <div class="container mt-4">
          <?php
          if (isset($errorMess)) {
            $this->errorMessage($errorMess);
          }
          if (isset($successMess)) {
            $this->successMessage($successMess);
          }
          ?>
          <div class="row justify-content-center">
            <div class="col-md-8">
              <h2 class="text-center mb-4"><?= $produit['nom'] ?></h2>
              <div class="card">
                <div class="card-body">
                  <div class="text-center mb-4">
                    <?php if (!empty($produit['chemin_photo'])) : ?>
                      <img src="<?= $this->url() . $produit['chemin_photo'] ?>" width="250" height="250" alt="<?= $produit['nom'] ?>">
                    <?php else : ?>
                      <img src="<?= $this->url() . "pieces_jointe/stock/produit.jpg" ?>" width="250" height="160" alt="Placeholder">
                    <?php endif; ?>
                  </div>

                  <div>
                    <p class="mb-3"><strong>Référence:</strong> <?= $produit['reference'] ?></p>
                    <p class="mb-3"><strong>Quantité en stock:</strong> <?= $produit['quantite'] ?></p>
                    <p class="mb-3"><strong>Description:</strong> <?= $produit['description'] ?></p>
                  </div>
                </div>
                <div class="card-footer d-flex justify-content-center">
                  <a href="index.php?action=update_product&product=<?= $produit['id_stock'] ?>">
                    <button class="btn btn-primary btn-lg mr-3">Modifier</button>
                  </a>
                  <button class="btn btn-danger btn-lg" data-toggle="modal" data-target="#confirmationModal">Supprimer</button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Confirmation de suppression</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <form method="post" action="">
                <div class="modal-body">
                  Êtes-vous sûr de vouloir supprimer ce produit ?
                  <input type="hidden" id="stockId" name="id_stock" value="<?= $produit['id_stock'] ?>">
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                  <button type="submit" name="delete_product" class="btn btn-danger">Supprimer</button>
                </div>
              </form>
            </div>
          </div>
        </div>

      <?php
        $this->footer();
      }

      public function update_product($produit, $errorMess, $successMess)
      {
        $this->header("Produit : " . $produit['reference']);
        $this->tabs();
      ?>

        <div class="container mt-4">
          <?php if (isset($errorMess)) : ?>
            <div class="alert alert-danger" role="alert">
              <?= $errorMess ?>
            </div>
          <?php endif; ?>
          <?php if (isset($successMess)) : ?>
            <div class="alert alert-success" role="alert">
              <?= $successMess ?>
            </div>
          <?php endif; ?>
          <div class="row justify-content-center">
            <div class="col-md-8">
              <h3 class="text-center mb-4">Modifier un produit</h3>
              <div class="card">
                <div class="card-body">
                  <div class="text-center mb-4">
                    <?php if (!empty($produit['chemin_photo'])) : ?>
                      <img src="<?= $this->url() . $produit['chemin_photo'] ?>" class="img-fluid" alt="<?= $produit['nom'] ?>" width="250" height="160">
                    <?php else : ?>
                      <img src="<?= $this->url() . "pieces_jointe/stock/produit.jpg" ?>" class="img-fluid" alt="Placeholder" width="250" height="160">
                    <?php endif; ?>
                  </div>
                  <form action="" method="POST">
                    <input type="hidden" class="form-control" id="id_stock" name="id_stock" value="<?= $produit['id_stock'] ?>" required>

                    <div class="form-group">
                      <label for="nom">Nom du produit</label>
                      <input type="text" class="form-control" id="nom" name="nom" value="<?= $produit['nom'] ?>" required>
                    </div>
                    <div class="form-group">
                      <label for="reference">Référence</label>
                      <input type="text" class="form-control" id="reference" name="reference" value="<?= $produit['reference'] ?>" required>
                    </div>
                    <div class="form-group">
                      <label for="description">Description</label>
                      <textarea class="form-control" id="description" name="description" rows="3" required><?= $produit['description'] ?></textarea>
                    </div>
                    <div class="form-group">
                      <label for="quantite">Quantité en stock</label>
                      <input type="number" class="form-control" id="quantite" name="quantite" min="0" value="<?= $produit['quantite'] ?>" required>
                    </div>
                    <button type="submit" name="update_product" class="btn btn-primary">Mettre à jour le produit</button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>

      <?php
        $this->footer();
      }


      public function communicationTab($techniciens, $conversations, $citations, $errorMess, $successMess)
      {
        $this->header('Communication');
        $this->tabs();
      ?>

        <div class="container-fluid mt-4">
          <?php if (isset($errorMess)) : ?>
            <div class="alert alert-danger" role="alert">
              <?= $errorMess ?>
            </div>
          <?php endif; ?>
          <?php if (isset($successMess)) : ?>
            <div class="alert alert-success" role="alert">
              <?= $successMess ?>
            </div>
          <?php endif; ?>

          <div class="row mb-3">
            <div class="col-md-12">
              <div class="text-right">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#conversationModal">Créer une conversation</button>
              </div>
            </div>
          </div>

          <!-- popup créer conv -->
          <div class="modal fade" id="conversationModal" tabindex="-1" role="dialog" aria-labelledby="conversationModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="conversationModalLabel">Créer une conversation</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <form method="post" action="">
                  <div class="modal-body">
                    <div class="form-group">
                      <label for="technicienSelect">Sélectionner un technicien :</label>
                      <select class="form-control" id="technicienSelect" name="technicienSelect">
                        <?php foreach ($techniciens as $technicien) : ?>
                          <option value="<?= $technicien['id_utilisateur'] ?>">
                            <?= $technicien['nom'] ?> - <?= $technicien['prenom'] ?> - <?= $technicien['email'] ?>
                          </option>
                        <?php endforeach; ?>
                      </select>
                    </div>

                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                    <button type="submit" name="create_conv" class="btn btn-primary">Créer</button>
                  </div>
              </div>
              </form>
            </div>
          </div>

          <div class="row">
            <div class="col-md-8">
              <div class="card mb-4 message-card">
                <h5 class="card-header bg-primary text-white">Messages</h5>
                <div class="card-body">
                  <?php foreach ($conversations as $conversation) : ?>
                    <a href="index.php?action=conversation&conversation=<?= $conversation['id_conversation'] ?>" class="card mb-2 conversation-link">
                      <div class="card-body d-flex align-items-center">
                        <div class="mr-3">
                          <img src="<?= $this->url() . $conversation['pdp'] ?>" alt="Photo de profil" class="img-fluid rounded-circle" style="width: 50px; height: 50px;">
                        </div>
                        <div>
                          <h6 class="mb-0"><?= $conversation['prenom'] ?> <?= $conversation['nom'] ?></h6>
                        </div>
                      </div>
                    </a>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>
          </div>

          <div class="card">
            <h5 class="card-header bg-success text-white">Ajouter une citation</h5>
            <div class="card-body">
              <form method="post" action="">
                <div class="form-group">
                  <label for="citation">Citation :</label>
                  <textarea class="form-control" id="citation" name="citation" rows="3"></textarea>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <button type="button" class="btn btn-info btn-block mb-3" data-toggle="modal" data-target="#citationsModal">Afficher toutes les citations</button>
                  </div>
                  <div class="col-md-6">
                    <button type="submit" name="add_citation" class="btn btn-primary btn-block">Ajouter</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>


      <!-- popup citations -->
      <div class="modal fade" id="citationsModal" tabindex="-1" role="dialog" aria-labelledby="citationsModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="citationsModalLabel">Toutes les citations</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <?php foreach ($citations as $citation) : ?>
                <div class="card mb-2">
                  <div class="card-body">
                    <?= $citation['citation'] ?>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
            </div>
          </div>
        </div>
      </div>

    <?php
        $this->footer();
      }

      public function conversation($conversation, $messages, $errorMess, $successMess)
      {
        $this->header('Conversation');
        $this->tabs();
    ?>
      <div class="container-fluid mt-4">
        <?php if (isset($errorMess)) : ?>
          <div class="alert alert-danger" role="alert">
            <?= $errorMess ?>
          </div>
        <?php endif; ?>
        <?php if (isset($successMess)) : ?>
          <div class="alert alert-success" role="alert">
            <?= $successMess ?>
          </div>
        <?php endif; ?>
        <div class="row">
          <div class="col-md-8">
            <div class="card">
              <h5 class="card-header bg-primary text-white">Conversation avec
                <?php if ($conversation['id_utilisateur1'] != $_SESSION['admin']) : ?>
                  <?= $conversation['nom_utilisateur1'] ?> <?= $conversation['prenom_utilisateur1'] ?>
                <?php else : ?>
                  <?= $conversation['nom_utilisateur2'] ?> <?= $conversation['prenom_utilisateur2'] ?>
                <?php endif; ?>
              </h5>
              <div id="message-container" class="card-body p-0" style="height: 400px; overflow-y: scroll;">
                <?php if (empty($messages)) : ?>
                  <p class="text-center mt-3">C'est vide ici...</p>
                <?php else : ?>
                  <?php foreach ($messages as $message) : ?>
                    <?php
                    $marginClass = ($message['id_envoyeur'] == $_SESSION['admin']) ? 'mr-3' : 'ml-3';
                    ?>
                    <div class="message-container <?= ($message['id_envoyeur'] == $_SESSION['admin']) ? 'text-right' : 'text-left' ?> <?= $marginClass ?> mb-3">
                      <div class="pdp-thumbnail rounded-circle" style="width: 30px; height: 30px; overflow: hidden; <?= ($message['id_envoyeur'] == $_SESSION['admin']) ? 'float: right; margin-left: 10px;' : 'float: left; margin-right: 10px;' ?>">
                        <img src="<?= $this->url() . $message['pdp'] ?>" alt="PDP" class="img-fluid rounded-circle">
                      </div>
                      <div class="message <?= ($message['id_envoyeur'] == $_SESSION['admin']) ? 'bg-primary text-white rounded-lg' : 'bg-secondary text-white rounded-lg' ?>" style="width: auto; max-width: 70%; display: inline-block;">
                        <?php if ($message['type_message'] == 'Fichier') : ?>
                          <p class="m-0 p-2 text-justify">
                            <strong><?= $message['prenom_utilisateur'] ?> <?= $message['nom_utilisateur'] ?></strong><br>
                            <a class="text-white" href="<?= $this->url() . $message['message'] ?>">
                              <i class="bi bi-file-earmark-text"></i>
                              Voir le fichier
                            </a>
                          </p>
                        <?php else : ?>
                          <p class="m-0 p-2 text-justify">
                            <strong><?= $message['prenom_utilisateur'] ?> <?= $message['nom_utilisateur'] ?></strong><br>
                            <?= $message['message'] ?>
                          </p>
                        <?php endif; ?>

                      </div>
                    </div>
                  <?php endforeach; ?>

                <?php endif; ?>
              </div>
              <div class="card-footer">
                <form action="" method="post" enctype="multipart/form-data">
                  <div class="form-group">
                    <textarea class="form-control" id="message" name="message" rows="3" placeholder="Saisissez votre message ici"></textarea>
                  </div>
                  <div class="form-group">
                    <label for="file">Envoyer un fichier (PDF ou image) :</label>
                    <input type="file" class="form-control-file" id="file" name="file" accept="image/*, application/pdf">
                  </div>
                  <button type="submit" name="send_message" class="btn btn-primary">Envoyer</button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
      <script>
        function scrollToBottom() {
          var element = document.getElementById("message-container");
          element.scrollTop = element.scrollHeight;
        }

        window.onload = scrollToBottom;
      </script>

    <?php
      }
      public function maintenanceTab($conges, $pannes, $signaler, $errorMess, $successMess)
      {
        $this->header('Maintenance');
        $this->tabs();
        $jours = ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
        $mois = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];

    ?>
      <div class="container-fluid mt-4">
        <?php if (isset($errorMess)) : ?>
          <div class="alert alert-danger" role="alert">
            <?= $errorMess ?>
          </div>
        <?php endif; ?>
        <?php if (isset($successMess)) : ?>
          <div class="alert alert-success" role="alert">
            <?= $successMess ?>
          </div>
        <?php endif; ?>
        <div class="row">
          <div class="col-md-4">
            <div class="card">
              <div class="card-header bg-success text-center text-white">
                <h5 class="card-title"><i class="bi bi-calendar-event-fill"></i> Demande de congés</h5>
              </div>
              <form action="" method="post">
                <div class="card-body" style="max-height: 300px; height: 300px; overflow-y: auto;">
                  <?php if (!empty($conges)) : ?>
                    <ul class="list-group">
                      <?php foreach ($conges as $conge) : ?>
                        <?php
                        $date_debut_fr = DateTime::createFromFormat('Y-m-d', $conge['date_debut']);
                        $date_fin_fr = DateTime::createFromFormat('Y-m-d', $conge['date_fin']);
                        $date_debut_format_fr = $date_debut_fr->format('d') . ' ' . $mois[intval($date_debut_fr->format('m')) - 1] . ' ' . $date_debut_fr->format('Y');
                        $date_fin_format_fr = $date_fin_fr->format('d') . ' ' . $mois[intval($date_fin_fr->format('m')) - 1] . ' ' . $date_fin_fr->format('Y');
                        ?>
                        <br>
                        <li class="list-group-item">
                          <div class="row align-items-center">
                            <div class="col-md-10">
                              <h5 class="card-title text-center"><?php echo $conge['prenom'] . ' ' . $conge['nom']; ?></h5><br>
                              <h6 class="card-subtitle mb-2 text-muted">Motif: <?php echo $conge['motif']; ?></h6>
                              <?php if ($conge['motif'] == "Autre") : ?>
                                <p class="card-text">Commentaire: <?php echo $conge['commentaire']; ?></p>
                              <?php endif; ?>
                              <p class="card-text"><?php echo "Du <strong>" . $date_debut_format_fr . "</strong> jusqu'au <strong>" . $date_fin_format_fr . " </strong>"; ?></p>
                            </div>
                            <div class="col-md-2 text-end">
                              <div class="form-check">
                                <label class="checkbox-btn">
                                  <label for="checkbox"></label>
                                  <input id="checkbox" type="checkbox" name="id_conges[]" value="<?php echo $conge['id_conges_payes']; ?>">
                                  <span class="checkmark"></span>
                                </label>
                              </div>
                            </div>
                          </div>
                        </li>
                      <?php endforeach; ?>
                    </ul>
                  <?php else : ?>
                    <p>Aucune demande de congés.</p>
                  <?php endif; ?>
                </div>
                <div class="card-footer text-center">
                  <button type="submit" name="valider_conges" class="c-button c-button--gooey"> Valider
                    <div class="c-button__blobs">
                      <div></div>
                      <div></div>
                      <div></div>
                    </div>
                  </button>
                  <svg xmlns="http://www.w3.org/2000/svg" version="1.1" style="display: block; height: 0; width: 0;">
                    <defs>
                      <filter id="goo">
                        <feGaussianBlur in="SourceGraphic" stdDeviation="10" result="blur"></feGaussianBlur>
                        <feColorMatrix in="blur" mode="matrix" values="1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 18 -7" result="goo"></feColorMatrix>
                        <feBlend in="SourceGraphic" in2="goo"></feBlend>
                      </filter>
                    </defs>
                  </svg>
                </div>
              </form>
            </div>
          </div>


          <div class="col-md-4">
            <div class="card">
              <div class="card-header bg-warning text-center text-white">
                <h5 class="card-title"><i class="bi bi-exclamation-triangle-fill"></i> Panne et entretien véhicule</h5>
              </div>
              <form action="" method="post">
                <div class="card-body" style="max-height: 300px; height: 300px; overflow-y: auto;">
                  <?php if (!empty($pannes)) : ?>
                    <ul class="list-group">
                      <?php foreach ($pannes as $panne) : ?>
                        <br>
                        <li class="list-group-item">
                          <div class="row align-items-center">
                            <div class="col-md-10">
                              <h5 class="card-title text-center"><?= $panne['prenom'] . ' ' . $panne['nom']; ?></h5><br>
                              <h6 class="card-subtitle mb-2 text-muted">Immatriculation : <?= $panne['immatriculation']; ?></h6>
                              <?php
                              $date_heure_fr = $jours[date('w', strtotime($panne['date_heure']))] . ' ' . date('j', strtotime($panne['date_heure'])) . ' ' . $mois[date('n', strtotime($panne['date_heure'])) - 1] . ' ' . date('Y', strtotime($panne['date_heure'])) . ' à ' . date('H\hi', strtotime($panne['date_heure']));
                              ?>
                              <p class="card-text">Envoyé le<strong> <?= $date_heure_fr; ?></strong></p>
                            </div>
                            <div class="col-md-2 text-end">
                              <div class="form-check">
                                <label class="checkbox-btn">
                                  <label for="checkbox"></label>
                                  <input id="checkbox" type="checkbox" name="id_entretien[]" value="<?= $panne['id_entretien']; ?>">
                                  <span class="checkmark"></span>
                                </label>
                              </div>
                            </div>
                          </div>
                        </li>
                      <?php endforeach; ?>
                    </ul>
                  <?php else : ?>
                    <p>Aucune demande à traiter.</p>
                  <?php endif; ?>
                </div>
                <div class="card-footer text-center">
                  <button type="submit" name="valider_entretien" class="c-button c-button--gooey"> Valider
                    <div class="c-button__blobs">
                      <div></div>
                      <div></div>
                      <div></div>
                    </div>
                  </button>
                  <svg xmlns="http://www.w3.org/2000/svg" version="1.1" style="display: block; height: 0; width: 0;">
                    <defs>
                      <filter id="goo">
                        <feGaussianBlur in="SourceGraphic" stdDeviation="10" result="blur"></feGaussianBlur>
                        <feColorMatrix in="blur" mode="matrix" values="1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 18 -7" result="goo"></feColorMatrix>
                        <feBlend in="SourceGraphic" in2="goo"></feBlend>
                      </filter>
                    </defs>
                  </svg>
                </div>
              </form>
            </div>
          </div>

          <div class="col-md-4">
            <div class="card">
              <div class="card-header bg-danger text-center text-white">
                <h5 class="card-title"><i class="bi bi-tools"></i> Signalement</h5>
              </div>
              <form action="" method="post">
                <div class="card-body" style="max-height: 300px; height: 300px; overflow-y: auto;">
                  <?php if (!empty($signaler)) : ?>
                    <ul class="list-group">
                      <?php foreach ($signaler as $signalement) : ?>
                        <br>
                        <li class="list-group-item">
                          <div class="row align-items-center">
                            <div class="col-md-10">
                              <h5 class="card-title text-center"><?= $signalement['prenom'] . ' ' . $signalement['nom']; ?></h5><br>
                              <h6 class="card-subtitle mb-2 text-muted">Motif : <?= $signalement['motif']; ?></h6>
                              <p class="card-text">Description : <strong> <?= $signalement['description']; ?></strong></p>
                            </div>
                            <div class="col-md-2 text-end">
                              <div class="form-check">
                                <label class="checkbox-btn">
                                  <label for="checkbox"></label>
                                  <input id="checkbox" type="checkbox" name="id_signalement[]" value="<?= $signalement['id_signalement']; ?>">
                                  <span class="checkmark"></span>
                                </label>
                              </div>
                            </div>
                          </div>
                        </li>
                      <?php endforeach; ?>
                    </ul>
                  <?php else : ?>
                    <p>Aucune demande à traiter.</p>
                  <?php endif; ?>
                </div>
                <div class="card-footer text-center">
                  <button type="submit" name="valider_signalement" class="c-button c-button--gooey"> Valider
                    <div class="c-button__blobs">
                      <div></div>
                      <div></div>
                      <div></div>
                    </div>
                  </button>
                  <svg xmlns="http://www.w3.org/2000/svg" version="1.1" style="display: block; height: 0; width: 0;">
                    <defs>
                      <filter id="goo">
                        <feGaussianBlur in="SourceGraphic" stdDeviation="10" result="blur"></feGaussianBlur>
                        <feColorMatrix in="blur" mode="matrix" values="1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 18 -7" result="goo"></feColorMatrix>
                        <feBlend in="SourceGraphic" in2="goo"></feBlend>
                      </filter>
                    </defs>
                  </svg>
                </div>
              </form>
            </div>
          </div>

        </div>
      </div>

    <?php
        $this->footer();
      }

      public function checklistTab()
      {
        $this->header('Check-list');
        $this->tabs();
        echo 'Check list';

        $this->footer();
      }

      public function error404()
      {
        $this->header('error 404');
        $this->tabs();
    ?>
      <d iv class="container d-flex flex-column align-items-center" style="height: 100vh;">
        <img src="pieces_jointe/ico_web/error404.png" alt="Erreur 404" width="600" height="600">
        <div class="text-center mt-3">
          <small>Votre page semble introuvable, cliquez <a href="index.php?action=planning">ici pour revenir</a> à l'accueil.</small>
        </div>
        </div>
    <?php
        $this->footer();
      }

      private function errorMessage($message)
      {
        echo '<div class="alert alert-danger" role="alert">' . $message . '</div>';
      }

      private function successMessage($message)
      {
        echo '<div class="alert alert-success" role="alert">' . $message . '</div>';
      }
    }
