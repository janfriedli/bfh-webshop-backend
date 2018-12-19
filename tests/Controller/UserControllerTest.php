<?php
use Liip\FunctionalTestBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{

    /**
     * @var $client \Symfony\Component\BrowserKit\Client client
     */
    private $client;

    /**
     * prepare an auth client
     */
    public function setUp()
    {
        $this->client = $this->makeClient();
    }
    
    /**
     * POST new User
     */
    public function testCreateUser()
    {

        $this->loadFixtures(); //inits an empty db
        $userJson = '{ "username" : "test12345", "password" : "TEST123456", "register_token" : "secret"} ';
        $this->client->request(
            'POST',
            '/v1/register',
            [],
            [],
            array('CONTENT_TYPE' => 'application/json'),
            $userJson
        );
        $response = json_decode($this->client->getResponse()->getContent());
        $this->assertEmpty($response);
        $this->assertStatusCode(201, $this->client);
    }

    /**
     * POST wrong register token
     */
    public function testWrongToken()
    {
        $this->loadFixtures(); //inits an empty db
        $userJson = '{ "username" : "test12345", "password" : "TEST123456", "register_token" : "secret1"} ';
        $this->client->request(
            'POST',
            '/v1/register',
            [],
            [],
            array('CONTENT_TYPE' => 'application/json'),
            $userJson
        );
        $response = json_decode($this->client->getResponse()->getContent());
        $this->assertEquals($response->message,"Wrong register token");
        $this->assertStatusCode(400, $this->client);
    }

    /**
     * POST duplicated user
     */
    public function testDuplicatedUsername()
    {
        $this->loadFixtures([
            'App\Fixture\Test\UserFixtures'
        ]); //inits an empty db
        $userJson = '{ "username" : "testuser", "password" : "TEST123456", "register_token" : "secret"} ';
        $this->client->request(
            'POST',
            '/v1/register',
            [],
            [],
            array('CONTENT_TYPE' => 'application/json'),
            $userJson
        );
        $response = json_decode($this->client->getResponse()->getContent());
        $this->assertEquals($response->message,"Username is already taken!");
        $this->assertStatusCode(400, $this->client);
    }

    /**
     * POST short input
     */
    public function testShortInput()
    {
        $this->loadFixtures([
            'App\Fixture\Test\UserFixtures'
        ]); //inits an empty db
        $userJson = '{ "username" : "1", "password" : "1", "register_token" : "secret"} ';
        $this->client->request(
            'POST',
            '/v1/register',
            [],
            [],
            array('CONTENT_TYPE' => 'application/json'),
            $userJson
        );
        $response = json_decode($this->client->getResponse()->getContent());
        $this->assertEquals($response[0]->message,"Your username must be at least 5 characters long");
        $this->assertEquals($response[1]->message,"Your password must be at least 8 characters long");
        $this->assertStatusCode(400, $this->client);
    }

    /**
     * POST long input
     */
    public function testLongInput()
    {
        $this->loadFixtures([
            'App\Fixture\Test\UserFixtures'
        ]); //inits an empty db
        $userJson = '{ "username" : "1sadasdasdasdsadsadsaddsaasdsaasdasdasdasdasdassaddsadsadsadsa", "password" : "1sadasdasdasdsadsadsaddsaasdsaasdasdasdasdasdassaddsadsadsadsaasdsadasdsadsadasdsa", "register_token" : "secret"} ';
        $this->client->request(
            'POST',
            '/v1/register',
            [],
            [],
            array('CONTENT_TYPE' => 'application/json'),
            $userJson
        );
        $response = json_decode($this->client->getResponse()->getContent());
        $this->assertEquals($response[0]->message,"Your username cannot be longer than 25 characters");
        $this->assertEquals($response[1]->message,"Your password cannot be longer than 64 characters");
        $this->assertStatusCode(400, $this->client);
    }
}