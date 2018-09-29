<?php
use Liip\FunctionalTestBundle\Test\WebTestCase;

class ProductControllerTest extends WebTestCase
{

    /**
     * GET the empty products
     */
    public function testGetEmptyProducts()
    {
        $this->loadFixtures(); //inits an empty db
        $client = $this->makeClient();
        $client->request('GET', '/v1/product');
        $this->assertEmpty(json_decode($client->getResponse()->getContent()));
        $this->assertStatusCode(200, $client);
    }
}