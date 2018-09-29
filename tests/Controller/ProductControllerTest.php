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


    /**
     * GET the populated products
     */
    public function testGetPopulatedProducts()
    {
        $this->loadFixtures([
            'App\Fixture\Test\ProductFixture'
        ]);

        $client = $this->makeClient();
        $client->request('GET', '/v1/product');
        $products = json_decode($client->getResponse()->getContent());
        $this->assertEquals(count($products), 20);
        foreach ($products as $key => $product) {
            $this->assertEquals('description'.$key, $product->description);
            $this->assertEquals('product'.$key, $product->title);
        }
        $this->assertStatusCode(200, $client);
    }
}