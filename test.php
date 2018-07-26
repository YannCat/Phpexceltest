<?php

include('pdo.php');
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$sql = "SELECT cb.description,
			CASE 
				  WHEN c.connector_id = 1 THEN 'CHAdeMO'
				  WHEN connector_id = 2 THEN 'AC'
				  ELSE 'Combo'
			END AS 'prise', COUNT(t2.transaction_pk) as Nb_transaction, 
			SUM(t2.stop_value-t2.start_value)/1000 as kWh_tot,ROUND((SUM(t2.stop_value-t2.start_value)/1000)/COUNT(t2.transaction_pk),2) as meter_value_average,
			sec_to_time(SUM(unix_timestamp(t2.stop_timestamp)-unix_timestamp(t2.start_timestamp))) as temps_tot,
            sec_to_time((SUM(unix_timestamp(t2.stop_timestamp)-unix_timestamp(t2.start_timestamp)))/COUNT(t2.transaction_pk)) as temps_moy
		FROM (SELECT t.* FROM nissan.transaction as t ORDER BY t.start_timestamp DESC) as t2
        INNER JOIN nissan.connector as c ON c.connector_pk = t2.connector_pk
        INNER JOIN nissan.charge_box as cb ON cb.charge_box_id = c.charge_box_id
        INNER JOIN nissan.address as a ON a.address_pk = cb.address_pk
		WHERE DATEDIFF(curdate(),t2.start_timestamp) < 60
			AND t2.stop_timestamp <> 0
			AND t2.stop_value - t2.start_value <> 0
			AND cb.description LIKE '%Ikea%'
			AND a.country = 'FR'
		GROUP BY t2.connector_pk
		ORDER BY cb.description DESC;";
		
$requete = $pdo->query($sql);
$con = array();
$spreadsheet = new Spreadsheet();

$arrayData[] = ['Borne', 'Prise', 'Nombre de charges', 'Consommation totale (en kWh)', 'Consommation moyenne (en kWh)', 'Temps totale', 'Durée',];


$requete->bindColumn(1, $Borne);
$requete->bindColumn(2, $Prise);
$requete->bindColumn(3, $Nombre);
$requete->bindColumn(4, $Conso_tot);
$requete->bindColumn(5, $Conso_moy);
$requete->bindColumn(6, $time);
$requete->bindColumn(7, $temps_moy);

 while ($row = $requete->fetch(PDO::FETCH_BOUND)) {
      $arrayData[] = [utf8_decode($Borne), $Prise, $Nombre, $Conso_tot, $Conso_moy, $time, $temps_moy,];
    }
	
//print_r($arrayData);

$spreadsheet->getActiveSheet()
    ->fromArray(
        $arrayData,  // The data to set
        NULL,        // Array values with this value will not be set
        'A1'         // Top left coordinate of the worksheet range where
                     //    we want to set these values (default is A1)
    );
$writer = new Xlsx($spreadsheet);
$writer->save('hello world.xlsx');

?>
