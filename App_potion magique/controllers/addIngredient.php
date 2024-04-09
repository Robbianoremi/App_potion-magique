<?php 
  session_start();
  require_once '../config/db.php'; // Inclure le fichier de configuration de la base de données
  require_once '../core/functions.php'; // Inclure le fichier de fonctions
  $roles = $_SESSION['profil']['roles'];  // Récupération des rôles de l'utilisateur
  $id = $_SESSION['profil']['idutilisateur'];
  logedIn(); // Appel de la fonction de connexion
  
  if (isset($_POST['submit'])) {
      $name = $_POST['name'];
      $propriete = $_POST['propriete'];
      $type = $_POST['type'];
      $rarete = $_POST['rarete'];
     

      if (empty($name) || empty($propriete) || empty($type) || empty($rarete)) {
          $_SESSION['flash']['danger'] = 'Veuillez remplir tous les champs';
          header('Location: ../user/profil');
          exit;
      }
  
     createIngredient($name, $propriete, $type, $rarete);
      $_SESSION['flash']['success'] = 'Ingredient ajoute avec succes';
      header('Location: ../user/profil');
      exit;

  }
  ?>