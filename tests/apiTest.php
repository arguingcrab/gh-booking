<?php

/*
 * GroupHealth Assignment July 30, 2018
 * Zen Lu
 *
 *
 * Written with extension mongodb 1.4 & php7.2
 *
 * API Unit Testing for api.php
 */

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;

require 'api.php';


class ApiTest extends TestCase{
    
    private $base_uri, $client;
    
    protected function setUp() {
        /*
            setup base uri and client for unit tests
        */
        $this->base_uri = 'http://localhost:8000';
        $this->client = new Client(['base_uri' => $this->base_uri]);
    }
    
    public function test_list_bookings() {
        /*
            test list all bookings works (returns 200)
        */
        $response = $this->client->get('/api.php');
        $this->assertEquals(200, $response->getStatusCode());
    }
    
    public function test_create_bookings() {
        /*
            test create booking success
        */
        $payload = array(); // setup data to send to post
        $payload['username'] = "JoeyMoe";
        $payload['visitReason'] = "Some Reason";
        $payload['startDateTime'] = "2018-08-31T23:06";
        $payload['endDateTime'] = "2018-08-31T23:07";
        
        // send request with payload
        $response = $this->client->post('/api.php', ['form_params' => $payload]);
        
        $this->assertEquals(201, $response->getStatusCode()); // assert created
        
    }
    
    public function test_create_bookings_invalid() {
        /*
            test create booking with improper/missing data
        */
        $response = $this->client->post('/api.php'); // send request to create booking
        $data = json_decode($response->getBody(), true);
        
        $this->assertEquals(200, $response->getStatusCode()); // assert request success
        $this->assertArrayHasKey("errors", $data); // assert there are errors
    }
    
    public function test_view_booking() {
        /*
            test viewing a single booking by id
        */
        $payload = array(); // setup data to send to post to create a new user
        $payload['username'] = "ReinoNordin";
        $payload['visitReason'] = "Some Reason";
        $payload['startDateTime'] = "2018-09-20T23:06";
        $payload['endDateTime'] = "2018-09-20T23:07";
        
        // send request with payload to create user
        $request_create_user = $this->client->post('/api.php', ['form_params' => $payload]);
        $user = json_decode($request_create_user->getBody(), true); // grab response data for id
        
        // get user by id
        $response = $this->client->get('/api.php', ['query'=> ['_id' => $user['_id']]]);
        $data = json_decode($response->getBody(), true);

        $this->assertEquals(200, $response->getStatusCode()); // assert request success
        $this->assertArrayHasKey('_id', $data); // assert there is an _id key
        $this->assertEquals($user['_id'], $data['_id']); // assert id is same
    }
    
    public function test_update_booking_success() {
        /*
            test updating a single booking by id
        */
        $payload = array(); // setup data to send to post to create a new user
        $payload['username'] = "Raappana";
        $payload['visitReason'] = "Some Reason";
        $payload['startDateTime'] = "2018-09-20T23:06";
        $payload['endDateTime'] = "2018-09-20T23:07";
        
        // send request with payload to create user
        $request_create_user = $this->client->post('/api.php', ['form_params' => $payload]);
        $user = json_decode($request_create_user->getBody(), true); // grab response data for id
        $this->assertEquals(201, $request_create_user->getStatusCode()); // assert created

        // create payload for updating
        $update_payload = array();
        $update_payload['_id'] = $user['_id'];
        $update_payload['visitReason'] = 'some reason abcdefg';
        
        // attempt to update
        $response = $this->client->post('/api.php', ['form_params' => $update_payload]);
        $data = json_decode($response->getBody(), true);
        
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('_id', $data); // assert there is an _id key
        $this->assertEquals($user['_id'], $data['_id']); // assert id is same
        $this->assertEquals($update_payload['visitReason'], $data['visitReason']); // assert id is same
        $this->assertNotEquals('Some Reason', $data['visitReason']); // assert id is same
    }
    
    public function test_update_booking_invalid() {
        /*
            test updating a single booking by id with invalid/missing data
        */
        $payload = array(); // setup data to send to post to create a new user
        $payload['username'] = "ClaraSofie";
        $payload['visitReason'] = "Some Reason";
        $payload['startDateTime'] = "2018-09-20T23:06";
        $payload['endDateTime'] = "2018-09-20T23:07";
        
        // send request with payload to create user
        $request_create_user = $this->client->post('/api.php', ['form_params' => $payload]);
        $user = json_decode($request_create_user->getBody(), true); // grab response data for id
        $this->assertEquals(201, $request_create_user->getStatusCode()); // assert created
        
        // create payload for updating
        $update_payload = array();
        $update_payload['_id'] = $user['_id'];
        
        // attempt to update
        $response = $this->client->post('/api.php', ['form_params' => $update_payload]);
        $data = json_decode($response->getBody(), true);
        
        $this->assertEquals(200, $response->getStatusCode()); // assert request success
        $this->assertArrayHasKey('errors', $data); // assert there were errors
        $this->assertEquals('no fields given to update', $data['errors']['message']); // assert no data given to update
    }
    
    public function test_delete_booking() {
        /*
            test deleting a single booking by id
        */
        $payload = array(); // setup data to send to post to create a new user
        $payload['username'] = "Redrama";
        $payload['visitReason'] = "Some Reason";
        $payload['startDateTime'] = "2018-09-20T23:06";
        $payload['endDateTime'] = "2018-09-20T23:07";
        
        // send request with payload to create user
        $request_create_user = $this->client->post('/api.php', ['form_params' => $payload]);
        $user = json_decode($request_create_user->getBody(), true); // grab response data for id
        
        // delete user by id
        $response = $this->client->delete('/api.php', ['query' => ['_id' => $user['_id'] ] ]);
        $this->assertEquals(202, $response->getStatusCode()); // assert user deleted
        
        // check to see if user exists
        $response = $this->client->get('/api.php', ['query'=> ['_id' => $user['_id']]]);
        $data = json_decode($response->getBody(), true);
        
        $this->assertEquals(200, $response->getStatusCode()); // assert request success
        $this->assertArrayHasKey('errors', $data); // assert there were errors
        $this->assertEquals('user not found', $data['errors']['message']); // assert user not found
    }
}

?>