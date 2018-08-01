<?php

require 'vendor/autoload.php';
require 'config/database.php';

// Create seed data
$seedData = array(
    array(
        'username' => 'NatalieLance', 
        'visitReason' => 'General Checkup',
        'startDateTime' =>  "2018-07-30T02:04",
        'endDateTime' => "2018-07-30T03:04"
    ),
    array(
        'username' => 'AnneVulcan', 
        'visitReason' => 'Prescription Refill',
        'startDateTime' => "2018-07-30T03:05",
        'endDateTime' => "2018-07-30T05:04"
    )
);

/*
 * Add seed data
*/
$patients = $client->grouphealth->patients;
$patients->insertMany($seedData);

?>

<h1>seed data generated</h1>
<p><?php echo var_dump($seedData) ?></p>