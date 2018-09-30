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

    /**
     * POST a product with error
     */
    public function testPostProductValidation()
    {
        $this->loadFixtures();
        $client = $this->makeClient();
        $productJson = '{
            "title": "",
            "description": ""
        }';
        $this->postProduct($productJson, $client);

        $this->assertStatusCode(400, $client);
        $error = json_decode($client->getResponse()->getContent());
        $this->assertEquals(2, count($error->violations));
        $this->assertEquals('title', $error->violations[0]->propertyPath);
        $this->assertEquals('This value should not be blank.', $error->violations[0]->title);
        $this->assertEquals('description', $error->violations[1]->propertyPath);
        $this->assertEquals('This value should not be blank.', $error->violations[1]->title);
    }

    /**
     * PUT a product
     */
    public function testPutProduct()
    {
        $this->loadFixtures([
            'App\Fixture\Test\ProductFixture'
        ]);
        $client = $this->makeClient();

        $productJson = '{
            "title": "newTitle",
            "description": "newDescription"
        }';

        $this->putProduct(1, $productJson, $client);
        $this->assertStatusCode(200, $client);

        $updatedProduct = json_decode($client->getResponse()->getContent());
        $this->assertEquals(1, $updatedProduct->id);
        $this->assertEquals('newTitle', $updatedProduct->title);
        $this->assertEquals('newDescription', $updatedProduct->description);
    }

    /**
     * PUT a product with validation errors
     */
    public function testPutProductValidation()
    {
        $this->loadFixtures([
            'App\Fixture\Test\ProductFixture'
        ]);
        $client = $this->makeClient();

        $productJson = '{
            "title": "",
            "description": ""
        }';

        $this->putProduct(1, $productJson, $client);
        $this->assertStatusCode(400, $client);

        $error = json_decode($client->getResponse()->getContent());
        $this->assertEquals(2, count($error->violations));
        $this->assertEquals('title', $error->violations[0]->propertyPath);
        $this->assertEquals('This value should not be blank.', $error->violations[0]->title);
        $this->assertEquals('description', $error->violations[1]->propertyPath);
        $this->assertEquals('This value should not be blank.', $error->violations[1]->title);
    }


    /**
     * POST a product
     * @param string $body
     * @param $client
     */
    private function postProduct(string $body, $client) {
        $client->request(
            'POST',
            '/v1/product',
            [],
            [],
            array('CONTENT_TYPE' => 'application/json'),
            $body
        );
    }

    /**
     * PUT a product
     * @param $id
     * @param string $body
     * @param $client
     */
    private function putProduct(int $id,string $body, $client) {
        $client->request(
            'PUT',
            '/v1/product/' . $id,
            [],
            [],
            array('CONTENT_TYPE' => 'application/json'),
            $body
        );
    }


}