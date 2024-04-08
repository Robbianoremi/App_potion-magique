<?php 
  session_start();
  require_once '../config/db.php'; // Inclure le fichier de configuration de la base de données
  require_once '../core/functions.php'; // Inclure le fichier de fonctions
  $roles = $_SESSION['profil']['roles'];  // Récupération des rôles de l'utilisateur
  $id = $_SESSION['profil']['idutilisateur'];
  logedIn(); // Appel de la fonction de connexion
  
  if (isset($_POST['submit'])) {
      $name = $_POST['name'];
      $description = $_POST['description'];
      $duree = $_POST['duree'];
  
      createEffet($name, $description, $duree);
      $_SESSION['flash']['success'] = 'Effet ajoute avec succes';
      header('Location: ../user/profil');
      exit;
  }
  ?>

  


