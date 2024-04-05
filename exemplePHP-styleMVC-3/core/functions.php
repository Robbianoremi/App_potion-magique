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
    echo '<td>' . htmlspecialchars($user['name']) . '</td>';
    echo '<td>' . htmlspecialchars($user['email']) . '</td>';
    echo '<td>' . htmlspecialchars($user['roles']) . '</td>';
    echo '<td><a href="../controllers/deleteUser.php?id=' . $user['idusers'] . '" class="btn btn-danger btn-sm">Supprimer</a></td>';
    echo '</tr>';
  }
  echo '</tbody>';
  echo '</table>';
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
    $potion = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo '<table class="table table-striped table-hover">';
    echo '<thead class="thead-dark">';
    echo '<tr><th>Nom</th><th>Effets</th></tr>';
    echo '</thead>';
    echo '<tbody class="list-potion">';
    foreach ($potion as $potions) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($potions['nom']) . '</td>';
        echo '<td>' . htmlspecialchars($potions['effets']) . '</td>';
        echo '</tr>';
    }
    echo '</tbody>';
    echo '</table>';
}

function displayIngredients() { // Fonction pour afficher les ingrédients
  global $pdo; // Utilisez l'objet PDO que vous avez créé dans db.php
  $sql = "SELECT * FROM ingredient"; // Requête SQL pour obtenir tous les ingrédients
  $stmt = $pdo->prepare($sql); // Préparation de la requête
  $stmt->execute(); // Exécution de la requête
  $ingredients = $stmt->fetchAll(PDO::FETCH_ASSOC); // Récupération de tous les résultats dans un tableau associatif
  foreach ($ingredients as $ingredient) { // Parcours du tableau des résultats
    echo '<ul class="list-group">';
    echo '<li class="list-group-item border-2 m-1 ">' . $ingredient['nom'] . ' : ' . $ingredient['propriete'] . '</li>'; // Affichage de chaque ingrédient
    echo '</ul>';
  }
  }   







function getUserByEmail($email)
{
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
  $hashPass = password_hash($password, PASSWORD_DEFAULT); // Hachage du mot de passe
  $sql = "INSERT INTO utilisateur (nom, email, motDePasse, dateInscription, niveauMagie_idniveauMagie) VALUES (:name, :email, :password, :dateInscription, :idniveauMagie)"; // Requête SQL pour insérer un utilisateur
  $stmt = $pdo->prepare($sql); // Préparation de la requête
  $stmt->bindParam(':name', $name); // Liaison de la variable $name à la requête
  $stmt->bindParam(':email', $email); // Liaison de la variable $email à la requête
  $stmt->bindParam(':password', $hashPass); // Liaison de la variable $hashPass à la requête
  $stmt->bindParam(':dateInscription', date('Y-m-d H:i:s')); // Liaison de la date d'inscription à la requête
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
