<?php
session_start();
require_once '../config/db.php'; // Inclure le fichier de configuration de la base de données
require_once '../core/functions.php'; // Inclure le fichier de fonctions
$userId = $_SESSION['profil']['idutilisateur'];

if (isset($_POST['submit'])) {
    $name = $_POST['nomPotion'];
    $nMagie = $_POST['niveauMagie'];
    $description = $_POST['descriptionPotion'];
    $dureeChiffre = $_POST['dureePotion'];
    $uniteDuree = $_POST['uniteDureePotion'];
    $duree = $dureeChiffre . " " . $uniteDuree;

    $potion = createPotion($name, $nMagie, $description, $duree, $userId);
    if ($potion) {

        if (isset($_POST['effetsExistant'])) {
            $effet = $_POST['effetsExistant'];
            foreach ($effet as $key => $value) {
                $idEffet = $value;
                $sql = "INSERT INTO potionmagique_has_effet (potionmagique_idpotionmagique, effet_ideffet) VALUES (:potion, :effet)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':potion', $potion);
                $stmt->bindParam(':effet', $idEffet);
                $stmt->execute();
            }
        }
     
        if (isset($_POST['ingredientsExistant'])) {
            $ingredient = $_POST['ingredientsExistant'];
                // var_dump($ingredient);die();
            foreach ($ingredient as $key => $value) {
                $idIngredient = $value;
                $quantite = $_POST['quantiteExistant'][$key];
                $uniteMesure = $_POST['uniteMesureExistant'][$key];
                $sql = "INSERT INTO `potionmagique_has_ingredient`(`potionMagique_idpotionMagique`, `ingredient_idingredient`, `quantite`, `uniteMesure_iduniteMesure`) VALUES (:potion, :ingredient, :quantite, :uniteMesure)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':potion', $potion);
                $stmt->bindParam(':ingredient', $idIngredient);
                $stmt->bindParam(':quantite', $quantite);
                $stmt->bindParam(':uniteMesure', $uniteMesure);
                $stmt->execute();
            }
        }
        if (isset($_POST['numeroEtape'])) {
            $numEtapes = $_POST['numeroEtape'];
            foreach ($numEtapes as $key => $value) {
                $idEtape = $value;
                $description = $_POST['descriptionEtape'][$key];
                $duree = $_POST['dureeEstimee'][$key];
                $uniteDuree = $_POST['uniteDureeEstimee'][$key];
                $sql = "INSERT INTO etapeDePreparation (numeroEtape, description, dureeEstime, potionMagique_idpotionMagique) VALUES (:numeroEtape, :description, :duree, :potion)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':potion', $potion);
                $stmt->bindParam(':numeroEtape', $idEtape);
                $stmt->bindParam(':description', $description);
                $stmt->bindParam(':duree', $duree);
                $stmt->execute();
            }
        }
        $_SESSION['flash']['success'] = 'Potion ajoute avec succes';
        header('Location: ../user/profil');
        exit;
    } else {
        $_SESSION['flash']['danger'] = 'Une erreur s\'est produite lors de l\'ajout de la potion';
        header('Location: ../user/profil');
        exit;
    }
}
