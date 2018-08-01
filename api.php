<?php

/*
 * GroupHealth Assignment July 30, 2018
 * Zen Lu
 *
 *
 * Written with extension mongodb 1.4 & php7.2
 *
 * API endpoints for CRUD
 */

include 'vendor/autoload.php'; // include Composer's autoloader
include 'config/database.php'; // include Database connection

date_default_timezone_set('America/Los_Angeles'); // set timezone

/*
    check to see which function should run to accomplish task
*/
if ( isset($_SERVER['REQUEST_METHOD']) ) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // if method is post, create a new booking or update with id
        if ( isset($_POST['_id']) ) {
            updateBooking();
        } else {
            createBooking();
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // if method is get and has an id, display by id else list
        if ( isset($_GET['_id']) ) {
            viewBooking();
        } else {
            listBooking();
        }
    }


    if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        // if method is delete, delete by id
        deleteBooking();
    }
}


function createBooking() {
    /*
        function that grabs post data, validates it, and creates a new booking
    */
    global $client;
    
    $status = 201; // set status code
    
    $error_details = array(); //declare errors array
    
    $username = isset($_POST['username'])? $_POST['username'] : '';
    $visitReason = isset($_POST['visitReason'])? $_POST['visitReason'] : '';
    $startDateTime = isset($_POST['startDateTime'])? $_POST['startDateTime'] : '';
    $endDateTime = isset($_POST['endDateTime'])? $_POST['endDateTime'] : '';
    
    // manually validate fields
    if ( empty($username) ) {
        $error_details['username'] = 'missing username';
    }
    
    if ( empty($visitReason) ) {
        $error_details['visitReason'] = 'missing reason for visit';
    }
    
    if ( empty($startDateTime) ) {
        $error_details['startDateTime'] = 'missing start date and time';
    }
    
    if ( empty($endDateTime) ) {
        $error_details['endDateTime'] = 'missing end date and time';
    }

    if ( !empty($startDateTime) && !empty($endDateTime)) {
        // check to see that the start time isn't in the past
        if (strtotime($startDateTime) < time()){
            $error_details['startDateTime'] = 'start date time must be in the future';
        }
        
        // check to see that the end time is not before the start time
        if (strtotime($endDateTime) < strtotime($startDateTime)) {
            $error_details['endDateTime'] = 'end date time must be in the future';
        }
    }
    
    if (empty($error_details)) {
        // only allow data required to be added
        $data = array();
        $data['username'] = $username;
        $data['visitReason'] = $visitReason;
        $data['startDateTime'] = $startDateTime;
        $data['endDateTime'] = $endDateTime;
        
        $collection = $client->grouphealth->patients; // grab collection (table)
    
        $insertResult = $collection->insertOne( $data ); // insert data from post
        $id = $insertResult->getInsertedId(); // grab id of new insert
        
        viewBooking($id, $status); // return data for newly added booking
    } else {
        $errors = array();
        $errors['errors'] = $error_details; // set error detail to errors key
        echo json_encode($errors); // return errors
    }
}

function listBooking() {
    /*
        function that lists all bookings in the database
    */
    global $client;
    
    $collection = $client->grouphealth->patients; // grab collection
    $cursor = $collection->find(); // find all documents in a collection
    
    $data = array();
    
    $data['status'] = 200;

    // parse data and output it in json format
    foreach($cursor as $id => $value) {
        $data[] = array(
            '_id'=>(string)$value['_id'],
            'username'=>$value['username'],
            'visitReason'=>$value['visitReason'],
            'startDateTime'=>$value['startDateTime'],
            'endDateTime'=>$value['endDateTime']
        );
    }
    
    echo json_encode($data);
}

function viewBooking($id = '', $status = 200){
    /*
        function that displays one booking by id
    */
    global $client;
    
    $data = array();
    
    $collection = $client->grouphealth->patients; // get collection
    
    if ( !isset($id) || empty($id)) {
        // check to see if there is a param $id (from create) or to use $_GET
        $id = (string)$_GET['_id'];
    }
    
    // find one document where id in $id
    $cursor = $collection->findOne([
        '_id' => new MongoDB\BSON\ObjectID($id),
    ]);
    
    if(empty($cursor)) {
        $detail = array('message' => 'user not found');
        $data['errors'] = $detail;
    } else {
        // parse data
        $data['status'] = $status;
        $data['_id'] = (string)$cursor['_id'];
        $data['username'] = $cursor['username'];
        $data['visitReason'] = $cursor['visitReason'];
        $data['startDateTime'] = $cursor['startDateTime'];
        $data['endDateTime'] = $cursor['endDateTime'];
    }
    
    http_response_code($status); // set status code
    
    echo json_encode($data);
}

function updateBooking(){
    /*
        function for updating an existing booking by id
    */
    global $client;
    
    // grab the id and unset (we don't need to update id)
    $id = $_POST['_id'];
    
    unset($_POST['_id']);
    
    if (!empty($_POST) ){
        $object['$set'] = $_POST; // array for updating
        
        $collection = $client->grouphealth->patients; // get collection
        
        // find one document where id in $id
        $booking = $collection->findOne([
            '_id' => new MongoDB\BSON\ObjectID($id),
        ]);
        
        if ( !isset($booking) ) {
            // booking not found errors
            http_response_code(200);
            $errors['errors'] = array('message' => 'booking not found');
            echo json_encode($errors);
        } else {
            $cursor = $collection->updateOne($booking, $object); // update one by id
            viewBooking($id);
        }
        
    } else {
        // display error message if post is empty
        http_response_code(200);
        $errors['errors'] = array('message' => 'no fields given to update');
        echo json_encode($errors);
    }
}

function deleteBooking(){
    /*
        function to delete a booking by id
    */
    global $client;
    
    $collection = $client->grouphealth->patients; // get collection
    
    // delete one where id is in $_GET['id']
    $cursor = $collection->deleteOne([
        '_id' => [
            '$in' => [
                new MongoDB\BSON\ObjectID((string)$_GET['_id'])
            ],
        ],
    ]);
    
    http_response_code(202);
    
    $detail['detail'] = array('message' => 'document deleted');
    echo json_encode($detail); // show detail
}


?>