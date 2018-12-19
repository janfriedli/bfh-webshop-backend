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
        $userJson = '{ "username" : "test", "password" : "TEST", "register_token" : "secret"} ';
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
        $userJson = '{ "username" : "test", "password" : "TEST", "register_token" : "secret1"} ';
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
        $userJson = '{ "username" : "testuser", "password" : "TEST", "register_token" : "secret"} ';
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
}