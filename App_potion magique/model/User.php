<?php 

class User {
// Propriétés de la classe
private $iduser;
private $nom;
private $email;
private $password;

// Constructeur pour initialiser la classe
public function __construct($iduser, $nom, $email, $password) {
$this->setIduser($iduser);
$this->setNom($nom);
$this->setEmail($email);
$this->setPassword($password);
}

public function getIduser() {
return $this->iduser;
}

public function setIduser($iduser) {
$this->iduser = $iduser;
return $this;
}

public function getNom() {
return $this->nom;
}

public function setNom($nom) {
if (empty($nom)) {
throw new InvalidArgumentException("Le nom ne peut pas être vide.");
}
$this->nom = $nom;
return $this;
}

public function getEmail() {
return $this->email;
}

public function setEmail($email) {
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
throw new InvalidArgumentException("Adresse email invalide.");
}
$this->email = $email;
return $this;
}

public function getPassword() {
return $this->password;
}

public function setPassword($password) {
$this->password = password_hash($password, PASSWORD_DEFAULT);
return $this;
}
}