<?php
$hote = '127.0.0.1';
$port = "3306";
$nom_bdd = 'nissan';
$utilisateur = 'root';
$mot_de_passe ='67tjn2RAXFA';

try {
	//On test la connexion à la base de donnée
    $pdo = new PDO('mysql:host='.$hote.';port='.$port.';dbname='.$nom_bdd, $utilisateur, $mot_de_passe);
	//$pdo->exec('SET NAMES utf8');

} catch(Exception $e) {
	//Si la connexion n'est pas établie, on stop le chargement de la page.
	//reponse_json($success, $data, 'Echec de la connexion à la base de données');
	echo "soucis de connexion à la BD";
    exit();
}
?>
