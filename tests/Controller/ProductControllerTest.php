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

    /**
     * POST a product
     */
    public function testPostProduct()
    {
        $this->loadFixtures();
        $client = $this->makeClient();
        $product = new \App\Entity\Product();
        $product->setDescription('a description');
        $productJson = '{
            "title": "testTitle",
            "description": "testDescription"
        }';

        $client->request(
            'POST',
            '/v1/product',
            [],
            [],
            array('CONTENT_TYPE' => 'application/json'),
            $productJson
        );
        $this->assertStatusCode(201, $client);

        $product = json_decode($client->getResponse()->getContent());
        $this->assertEquals(1, $product->id);
        $this->assertEquals('testTitle', $product->title);
        $this->assertEquals('testDescription', $product->description);
    }
}