## Guide de l'installation du projet web 

# Ce projet est la partie dashboard admin de l'application mobile réalise en PHP MVC 

Projet WEB (en lien à l'appli mobile obligatoire) : https://github.com/DeMoNiiAkZz/E5---Projet-web

**Pré-requis :**

- Avoir WAMP d'installer 

- Dézipper le projet 

- Renommer "E5---Projet-web-main" par "LCS_Dash"

- Il faut surtout s'assurer que tous les codes du projet se trouve dans "C:\wamp64\www\LCS_Dash" et pas "C:\wamp64\www\LCS_Dash\LCS_Dash", chose qui peut arriver à cause du dézippage (c'est pareil pour LCS_Dash)	

- Vérifier si le service de WAMP est démarrer puis se connecter à PhpMyAdmin avec comme identifiant "root" sans mot de passe

- Créer une base nommée "lcs_mobile" (cette base de donnée sera la même pour le dashboard et appli mobile)

- Insérer le code SQL qui se situe dans le projet mobile dans le répertoire "SQL" ce qui donne comme chemin "C:\wamp64\www\LCS_mobile\SQL\lcs_mobile.sql"

- Si tout est bien fait, aucun code n'est à modifier pour les chemins ou autre


**Utilisation application web**
- Une fois les pré-requis fait, rendez vous sur votre navigateur web à cette URL "http://localhost/LCS_Dash/"
- Vous atterissez vers la page de connexion, pour se connecter :
  - E-mail : jesuisadmin@gmail.com
  - Mot de passe : 3wa5Q?2La8AYYX@upWAw
- Vous accéderez à l'interface admin avec différents onglets (j'ai supprimé tous les onglets qui m'appartenaient pas sauf "Synthèse" faisant office de page d'accueil)
- Pour l'ajout d'une intervention dans un planning, vous pouvez prendre le client "jesuisclient@gmail.com" ou "jesuisclient2@gmail.com" puis cliquer sur celui-ci pour confirmer
- Pour afficher des interventions, il y a deux solutions :
  - Rendez-vous dans la base de donnée dans la table "intervention" et remplacer les date_interventions par la date actuelle
  - Ou via le calendrier de l'onglet planning, vous sélectionner un technicien et s'arranger de tomber sur la bonne semaine
