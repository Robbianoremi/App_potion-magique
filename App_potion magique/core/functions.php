<?php
function displayUsers()
{ // Fonction pour afficher les utilisateurs
  global $pdo; // Utilisez l'objet PDO que vous avez créé dans db.php
  $sql = "SELECT nom,email FROM utilisateur ORDER BY idutilisateur DESC"; // Requête SQL pour obtenir tous les noms d'utilisateur
  $stmt = $pdo->prepare($sql); // Préparation de la requête
  $stmt->execute(); // Exécution de la requête
  $users = $stmt->fetchAll(PDO::FETCH_ASSOC); // Récupération de tous les résultats dans un tableau associatif
  echo '<ul class="list-group">'; // Début du marquage HTML pour la liste
  foreach ($users as $user) { // Parcours du tableau des résultats
    echo '<li class="list-group-item">' . $user['nom'] . '<br>' . $user['email'] . '</li>'; // Affichage de chaque nom d'utilisateur ainsi que son email
  }
  echo '</ul>'; // Fin du marquage HTML pour la liste
}
function displayMagicalNiveau()
{
  global $pdo; // Utilisez l'objet PDO que vous avez créé dans db.php

  $sql = "SELECT * FROM niveauMagie"; // Requête SQL pour obtenir tous les niveaux de magie

  $stmt = $pdo->prepare($sql); // Préparation de la requête
  $stmt->execute(); // Exécution de la requête
  $niveaux = $stmt->fetchAll(PDO::FETCH_ASSOC); // Récupération de tous les résultats dans un tableau associatif
  echo '<label for="niveauMagie" class="form-label fw-bold">Niveau de magie</label>'; // Étiquette pour la liste déroulante
  echo '<select class="form-select" name="niveauMagie" id="niveauMagie" required>'; // Début du marquage HTML pour la liste déroulante
  echo '<option value="">Choisissez un niveau de magie</option>'; // Option par défaut
  foreach ($niveaux as $niveau) { // Parcours du tableau des résultats
    echo '<option value="' . $niveau['idniveauMagie'] . '">' . $niveau['niveau'] . '</option>'; // Affichage de chaque niveau de magie
  }
  echo '</select>'; // Fin du marquage HTML pour la liste déroulante
}
function displayUsersAdmin()
{ // Fonction pour afficher les utilisateurs avec leurs rôles
  global $pdo; // Utilisez l'objet PDO que vous avez créé dans db.php
  // Requête SQL pour obtenir tous les utilisateurs et leurs rôles
  $sql = "SELECT utilisateur.idutilisateur, utilisateur.nom, utilisateur.email, GROUP_CONCAT(roleUtilisateur.role SEPARATOR ', ') AS roles 
          FROM utilisateur 
          LEFT JOIN utilisateur_has_roleUtilisateur ON utilisateur.idutilisateur = utilisateur_has_roleUtilisateur.utilisateur_idutilisateur
          LEFT JOIN roleUtilisateur ON utilisateur_has_roleUtilisateur.roleUtilisateur_idroleUtilisateur = roleUtilisateur.idroleUtilisateur 
          GROUP BY utilisateur.idutilisateur
          ORDER BY utilisateur.idutilisateur DESC"; // Requête SQL pour obtenir tous les utilisateurs et leurs rôles
  $stmt = $pdo->prepare($sql); // Préparation de la requête
  $stmt->execute(); // Exécution de la requête
  $users = $stmt->fetchAll(PDO::FETCH_ASSOC); // Récupération de tous les résultats dans un tableau associatif

  echo '<table class="table table-striped table-hover">'; // Bootstrap classes for tables
  echo '<thead class="thead-dark">'; // Bootstrap class for a dark table header
  echo '<tr><th>Nom</th><th>Email</th><th>Rôles</th><th>Action</th></tr>';
  echo '</thead>';
  echo '<tbody class="list-user">';
  foreach ($users as $user) {
    echo '<tr>';
    echo '<td>' . htmlspecialchars($user['nom']) . '</td>';
    echo '<td>' . htmlspecialchars($user['email']) . '</td>';
    echo '<td>' . htmlspecialchars($user['roles']) . '</td>';
    echo '<td><a href="../controllers/deleteUser.php?id=' . $user['idutilisateur'] . '" class="btn btn-danger btn-sm">Supprimer</a></td>';
    echo '</tr>';
  }
  echo '</tbody>';
  echo '</table>';
}

function createUserAdmin($name, $email, $password, $idMagie, $role){ // Fonction pour ajouter un utilisateur
  $date = date('Y-m-d H:i:s');  $hashPass = password_hash($password, PASSWORD_DEFAULT); // Hachage du mot de passe
  global $pdo; // Utilisez l'objet PDO que vous avez trouvé dans db.php
  $sql = "INSERT INTO utilisateur (nom, email, motDePasse, dateInscription, niveauMagie_idniveauMagie) VALUES (:name, :email, :password, :date, :niveauMagie)"; // Requête SQL pour ajouter un utilisateur
  $stmt = $pdo->prepare($sql); // Préparation de la requête
  $stmt->bindParam(':name', $name); // Liaison du nom de l'utilisateur à la requête
  $stmt->bindParam(':email', $email); // Liaison de l'email de l'utilisateur à la requête
  $stmt->bindParam(':password', $hashPass); // Liaison du mot de passe de l'utilisateur à la requête
  $stmt->bindParam(':date', $date); // Liaison de la date d'inscription de l'utilisateur à la requête
  $stmt->bindParam(':niveauMagie', $idMagie); // Liaison du niveau de magie de l'utilisateur à la requête
  $stmt->execute(); // Exécution de la requête

  $sql = "INSERT INTO utilisateur_has_roleUtilisateur(utilisateur_idutilisateur, roleUtilisateur_idroleUtilisateur) VALUES (:userId, :roleId)"; // Requête SQL pour lier un utilisateur à un role
  $id = $pdo->lastInsertId(); // Récupération de l'ID de l'utilisateur inscrit
  $stmt = $pdo->prepare($sql); // Préparation de la requête
  $stmt->bindParam(':userId', $id); // Liaison de l'ID de l'utilisateur à la requête
  $stmt->bindParam(':roleId', $role); // Liaison de l'ID du role à la requête
  $stmt->execute(); // Exécution de la requête

if($role == 2){
    $role = 1;
    $sql = "INSERT INTO utilisateur_has_roleUtilisateur(utilisateur_idutilisateur, roleUtilisateur_idroleUtilisateur) VALUES (:userId, :roleId)"; // Requête SQL pour lier un utilisateur à un role
    $stmt = $pdo->prepare($sql); // Préparation de la requête
    $stmt->bindParam(':userId', $id); // Liaison de l'ID de l'utilisateur à la requête
    $stmt->bindParam(':roleId', $role); // Liaison de l'ID du role à la requête
    $stmt->execute(); // Exécution de la requête
  }
  return true;
}
function deleteUser($userId)
{ // Fonction pour supprimer un utilisateur
  global $pdo; // Utilisez l'objet PDO que vous avez créé dans db.php

  if ($userId == $_SESSION['profil']['idusers'] || in_array('ROLE_ADMIN', $_SESSION['profil']['roles'])) { // Vérifiez si l'utilisateur est l'utilisateur connecté ou un administrateur
    $sql = "DELETE FROM utilisateur WHERE idutilisateur = :id"; // Requête SQL pour supprimer un utilisateur
    $stmt = $pdo->prepare($sql); // Préparation de la requête
    $stmt->bindParam(':id', $userId, PDO::PARAM_INT); // Liaison de l'ID de l'utilisateur à la requête

    if ($stmt->execute()) { // Si la requête s'exécute
      if ($userId == $_SESSION['profil']['idusers']) { // Si l'utilisateur supprimé est l'utilisateur connecté
        session_destroy();  // Détruire la session
        header('Location: index.php'); // Redirection vers la page de connexion
        exit; // Arrêt du script
      }
      return true; // Retourne vrai si l'utilisateur est supprimé
    }
  }
  return false; // Retourne faux si l'utilisateur n'est pas supprimé
}
function logedIn()
{ // Fonction pour vérifier si l'utilisateur est connecté et sa redirection suivant son rôle
  if (!isset($_SESSION['profil'])) { // Si l'utilisateur n'est pas connecté
    $_SESSION['flash']['danger'] = 'Vous devez être connecté pour accéder à cette page'; // Message d'erreur
    header('Location: ../index.php'); // Redirection vers la page de connexion
    exit; // Arrêt du script
  }
  $roles = $_SESSION['profil']['roles']; // Récupération des rôles de l'utilisateur
  $currentPage = basename($_SERVER['PHP_SELF']); // Récupération du nom de la page actuelle

  if ($currentPage === 'admin.php' && !in_array('ROLE_ADMIN', $roles)) {
    // Si la page est admin.php et que l'utilisateur n'a pas le rôle ROLE_ADMIN
    $_SESSION['flash']['danger'] = 'Vous n\'avez pas les droits pour accéder à la page d\'administration';
    header('Location: ../index.php'); // Redirection vers la page de connexion
    exit; // Arrêt du script
  }
}
function checkLogedOut()
{ // Fonction pour vérifier si l'utilisateur est déconnecté
  if (isset($_GET['logout']) && $_GET['logout'] == 'success') { // Vérification de la déconnexion
    echo "<div class='row'><div class='alert alert-success col-6 m-auto p-3 my-3 flash-message'>Vous etes bien déconnecté</div></div>";
  }
  return null;
}


function addIngredient(){
  global $pdo;
  $sql = "SELECT * FROM ingredient";
  $stmt = $pdo->prepare($sql);
  $stmt->execute();
  $ingredients = $stmt->fetchAll(PDO::FETCH_ASSOC);
  echo '<select class="form-select" name="ingredient" id="ingredient" required>';
  echo '<option value="">Choisissez un ingredient</option>';
  foreach ($ingredients as $ingredient) {
    echo '<option value="' . $ingredient['idingredient'] . '">' . $ingredient['nom'] . '</option>';
  }
  echo '</select>';
}

function displayRoles(){
  global $pdo;
  $sql = "SELECT * FROM roleUtilisateur";
  $stmt = $pdo->prepare($sql);
  $stmt->execute();
  $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
  echo '<select class="form-select" name="role" id="role" required>';
  echo '<option value="">Choisissez un role</option>';
  foreach ($roles as $role) {
    echo '<option value="' . $role['idroleUtilisateur'] . '">' . $role['role'] . '</option>';
  }
  echo '</select>';
}

function displayMessage()
{ // Fonction pour afficher les messages flash
  if (isset($_SESSION['flash']['danger'])) {
    echo '<div class="alert alert-danger flash-message fw-bold border-2" role="alert">' . htmlspecialchars($_SESSION['flash']['danger']) .
      '<button type="button" class="close btn" data-dismiss="alert" aria-label="Close"> ✖️</button></div>';
    unset($_SESSION['flash']['danger']);
  }
  if (isset($_SESSION['flash']['success'])) {
    echo '<div class="alert alert-success flash-message fw-bold border-2" role="alert">' . htmlspecialchars($_SESSION['flash']['success']) .
      ' <button type="button" class="close btn" data-dismiss="alert" aria-label="Close"> ✖️</button></div>';
    unset($_SESSION['flash']['success']);
  }
}

function displayPotion()
{
    global $pdo;
    $sql = "SELECT potionmagique.nom, GROUP_CONCAT(effet.description SEPARATOR ', ' ) AS effets
        FROM potionmagique
        JOIN potionmagique_has_effet ON potionmagique.idpotionmagique = potionmagique_has_effet.potionmagique_idpotionmagique
        JOIN effet ON potionmagique_has_effet.effet_ideffet = effet.ideffet
        GROUP BY idpotionmagique";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $potions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo '<select class="form-select" name="potion" id="potion" required>'; // Début du marquage HTML pour la liste déroulante
    echo '<option value="">Choisir une potion</option>'; // Option par défaut
    foreach ($potions as $potion) { // Parcours du tableau des résultats
  
      echo '<option title="' . htmlspecialchars($potion['description']) . '" value="' . $potion['idpotionmagique'] . '">' . $potion['nom'] . '</option>';
    }
    echo '</select>';
   
}
function displayIngredients() { // Fonction pour afficher les ingrédients
  global $pdo; // Utilisez l'objet PDO que vous avez créé dans db.php
  $sql = "SELECT * FROM ingredient"; // Requête SQL pour obtenir tous les ingrédients
  $stmt = $pdo->prepare($sql); // Préparation de la requête
  $stmt->execute(); // Exécution de la requête
  $ingredients = $stmt->fetchAll(PDO::FETCH_ASSOC); // Récupération de tous les résultats dans un tableau associatif
  echo '<select class="form-select" name="ingredient" id="ingredient" required>'; // Début du marquage HTML pour la liste déroulante
  echo '<option value="">Choisir un ingrédient</option>'; // Option par défaut
  foreach ($ingredients as $ingredient) { // Parcours du tableau des résultats

    echo '<option title="' . htmlspecialchars($ingredient['propriete']) . '" value="' . $ingredient['idingredient'] . '">' . $ingredient['nom'] . '</option>';
  }
  echo '</select>';
  }   

function displayeffets() { // Fonction pour afficher les effets
  global $pdo; // Utilisez l'objet PDO que vous avez été dans db.php
  $sql = "SELECT * FROM effet"; // Requête SQL pour obtenir tous les effets
  $stmt = $pdo->prepare($sql); // Préparation de la requête
  $stmt->execute(); // Exécution de la requête
  $effets = $stmt->fetchAll(PDO::FETCH_ASSOC); // Récupération de tous les résultats dans un tableau associatif
  echo '<select class="form-select" name="effet" id="effet" required>'; // Début du marquage HTML pour la liste déroulante
  echo '<option value="">Choisir un effet</option>'; // Option par défaut
  foreach ($effets as $effet) { // Parcours du tableau des résultats
    echo '<option title="' . htmlspecialchars($effet['description']) . '" value="' . $effet['ideffet'] . '">' . $effet['nom'] . '</option>'; // Affichage de chaque effet

  }
echo '</select>';
  }

  function displayOptionNiveauMagie(){
    global $pdo;
    $sql = "SELECT niveau, description, idniveauMagie FROM niveauMagie ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $niveaux = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($niveaux as $niveau) {
      echo '<option value="' . $niveau['idniveauMagie'] . '">' . $niveau['niveau'] . '</option>';
    }

  }

  function displayOptionEffets() {
    global $pdo;
    $sql = "SELECT ideffet, nom, description FROM effet";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $effets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo '<option value="">Choisissez un effet</option>';
    foreach ($effets as $effet) {
      echo '<option value="' . $effet['ideffet'] . '">' . $effet['nom'] . '</option>';
    }
  }

  function displayOptionIngredients() {
    global $pdo;
    $sql = "SELECT idingredient, nom FROM ingredient";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $ingredients = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo '<option value="">Choisissez un ingredient</option>';
    foreach ($ingredients as $ingredient) {
      echo '<option value="' . $ingredient['idingredient'] . '">' . $ingredient['nom'] . '</option>';
    }
  }

  function displayOptionUniteMesure() {
    global $pdo;
    $sql = "SELECT iduniteMesure, nom FROM uniteMesure";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $unites = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo '<option value="">Choisissez une unite de mesure</option>';
    foreach ($unites as $unite) {
      echo '<option value="' . $unite['iduniteMesure'] . '">' . $unite['nom'] . '</option>';
    }
  }

function rareteIngredient(){
  global $pdo;
  $sql = "SELECT rarete FROM ingredient GROUP BY rarete";
  $stmt = $pdo->prepare($sql);
  $stmt->execute();
  $raretes = $stmt->fetchAll(PDO::FETCH_ASSOC);
  echo '<label class="form-label fw-bold"> rarete </label>';
  echo '<select class="form-control" name="rarete"> ';
  echo '<option value=""> choisissez la rarete </option>' ;
  foreach ($raretes as $rarete) {
    echo '<option value="' . $rarete['rarete'] . '">' . $rarete['rarete'] . '</option>';

  }
  echo '</select>';

  
}

function getUserByEmail($email) {
  global $pdo;
  try {
    // Préparation de la requête pour récupérer l'utilisateur et ses rôles dans une seule requête
    $stmt = $pdo->prepare('SELECT utilisateur.*, GROUP_CONCAT(roleUtilisateur.role) AS roles 
                             FROM utilisateur
                             INNER JOIN utilisateur_has_roleUtilisateur ON utilisateur.idutilisateur = utilisateur_has_roleUtilisateur.utilisateur_idutilisateur
                             INNER JOIN roleUtilisateur ON utilisateur_has_roleUtilisateur.roleUtilisateur_idroleUtilisateur = roleUtilisateur.idroleUtilisateur
                             WHERE utilisateur.email = :email
                             GROUP BY utilisateur.idutilisateur');
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
      // Séparation des rôles dans un tableau
      $user['roles'] = explode(',', $user['roles']);
    }
    return $user;
  } catch (PDOException $e) {
    error_log($e->getMessage());
    return null;
  }
}

function createUser($name, $email, $password, $idMagie)
{ // Fonction pour créer un utilisateur
  global $pdo; // Utilisez l'objet PDO que vous avez créé dans db.php
  $date = 'Y-m-d H:i:s';
  $hashPass = password_hash($password, PASSWORD_DEFAULT); // Hachage du mot de passe
  $sql = "INSERT INTO utilisateur (nom, email, motDePasse, dateInscription, niveauMagie_idniveauMagie) VALUES (:name, :email, :password, :dateInscription, :idniveauMagie)"; // Requête SQL pour insérer un utilisateur
  $stmt = $pdo->prepare($sql); // Préparation de la requête
  $stmt->bindParam(':name', $name); // Liaison de la variable $name à la requête
  $stmt->bindParam(':email', $email); // Liaison de la variable $email à la requête
  $stmt->bindParam(':password', $hashPass); // Liaison de la variable $hashPass à la requête
  $stmt->bindParam(':dateInscription', $date); // Liaison de la date d'inscription à la requête
  $stmt->bindParam(':idniveauMagie', $idMagie); // Liaison de la variable $idMagie à la requête
  if ($stmt->execute()) { // Si la requête s'exécute
    $roleLevel = 1; // Niveau de rôle pour l'utilisateur
    $userId = $pdo->lastInsertId(); // Récupération de l'ID de l'utilisateur
    $sql = "INSERT INTO utilisateur_has_roleUtilisateur (utilisateur_idutilisateur, roleUtilisateur_idroleUtilisateur) VALUES (:userId, :roleLevel)"; // Ajout du rôle utilisateur
    $stmt = $pdo->prepare($sql); // Préparation de la requête
    $stmt->bindParam(':userId', $userId); // Liaison de l'ID de l'utilisateur à la requête
    $stmt->bindParam(':roleLevel', $roleLevel); // Liaison du niveau de rôle à la requête
    $stmt->execute(); // Exécution de la requête
    $sql = "SELECT roleUtilisateur.role FROM roleUtilisateur WHERE idroleUtilisateur = :roleLevel"; // Requête SQL pour obtenir le rôle de l'utilisateur
    $stmt = $pdo->prepare($sql); // Préparation de la requête
    $stmt->bindParam(':roleLevel', $roleLevel); // Liaison du niveau de rôle à la requête
    $stmt->execute(); // Exécution de la requête
    $roleLevel = $stmt->fetchColumn(); // Récupération du rôle de l'utilisateur
    return ['userId' => $userId, 'roleLevel' => $roleLevel]; // Retourne l'ID de l'utilisateur
  }
  return false; // Retourne faux si la requête échoue
}
function createEffet($name, $description, $duree){
  global $pdo; // Utilisez l'objet PDO que vous avez trouvé dans db.php
  $sql = "INSERT INTO effet (nom, description, duree) VALUES (:name, :description, :duree)"; // Requête SQL pour ajouter une potion
  $stmt = $pdo->prepare($sql); // Préparation de la requête
  $stmt->bindParam(':name', $name); // Liaison du nom de la potion à la requête
  $stmt->bindParam(':description', $description); // Liaison de la description de la potion à la requête
  $stmt->bindParam(':duree', $duree); // Liaison du temps de preparation de la potion à la requête
  $stmt->execute(); // Exécution de la requête
  return true;
}

function createIngredient($name, $propriete, $type, $rarete){
  global $pdo; // Utilisez l'objet PDO que vous avez trouvé dans db.php
  $sql = "INSERT INTO ingredient (nom, propriete, type, rarete) VALUES (:name, :propriete, :type, :rarete)"; // Requête SQL pour ajouter une potion
  $stmt = $pdo->prepare($sql); // Préparation de la requête
  $stmt->bindParam(':name', $name); // Liaison du nom de la potion à la requête
  $stmt->bindParam(':propriete', $propriete); // Liaison de la description de la potion à la requête
  $stmt->bindParam(':type', $type); // Liaison de la description de la potion à la requête
  $stmt->bindParam(':rarete', $rarete); // Liaison de la description de la potion à la requête

 
  $stmt->execute(); // Exécution de la requête
  return true;

}

function typeIngredient(){
  global $pdo;
  $sql = "SELECT type FROM ingredient GROUP BY type";
  $stmt = $pdo->prepare($sql);
  $stmt->execute();
  $types = $stmt->fetchAll(PDO::FETCH_ASSOC);
  echo '<label class="form-label fw-bold"> type </label>';
  echo '<select class="form-control" name="type"> ';
  echo '<option value=""> choisissez votre type </option>' ;
  foreach ($types as $type) {
    echo '<option value="' . $type['type'] . '">' . $type['type'] . '</option>';

  }
  echo '</select>';

  
}

function validateUserExists($email)
{ // Fonction pour vérifier si l'utilisateur existe
  global $pdo; // Utilisez l'objet PDO que vous avez créé dans db.php
  $sql = "SELECT COUNT(*) FROM utilisateur WHERE email = :email"; // Requête SQL pour compter le nombre d'utilisateurs avec le même email
  $stmt = $pdo->prepare($sql); // Préparation de la requête
  $stmt->bindParam(':email', $email); // Liaison de la variable $email à la requête
  $stmt->execute(); // Exécution de la requête
  if ($stmt->fetchColumn() > 0) { // Si le nombre d'utilisateurs avec le même email est supérieur à 0
    return $_SESSION['flash']['danger'] = "Cette email est déjà utilisé.";
  }
  return null;
}
function validateNotEmpty($field, $fieldName)
{ // Fonction pour valider si un champ n'est pas vide
  if (empty($field)) {
    return "Le champ $fieldName ne peut pas être vide.";
  }
  return null;
}
function validateUsername($username)
{ // Fonction pour valider le nom d'utilisateur
  if (!preg_match('/^[a-zA-Z]{4,30}$/', $username)) {
    return "Le nom d'utilisateur doit être composé de 4 à 30 lettres et sans chiffres ou caractères spéciaux.";
  }
  return null;
}
function validateEmail($email)
{ // Fonction pour valider l'email
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    return "L'adresse email n'est pas valide.";
  }
  return null;
}
function validatePassword($password)
{ // Fonction pour valider le mot de passe
  // Vérifie si le mot de passe a une longueur de 4 à 30 caractères et contient au moins une lettre minuscule, une lettre majuscule et un chiffre
  if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{4,30}$/', $password)) {
    return "Le mot de passe doit être composé de 4 à 30 caractères, incluant au moins une majuscule, une minuscule et un chiffre.";
  }
  return null;
}
