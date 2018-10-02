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
        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );
        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Allow',
                'GET, POST'
            )
        );
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
        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );
        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Allow',
                'GET, POST'
            )
        );

        foreach ($products as $key => $product) {
            $this->assertEquals('description'.$key, $product->description);
            $this->assertEquals('product'.$key, $product->title);
            $this->assertEquals($key, $product->price);
            $this->assertEquals($key, $product->quantity);
            $this->assertEquals('https://img.url/test' .$key. '.jpg', $product->imgUrl);
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
            "description": "testDescription",
            "imgUrl": "https://img.url/test.png",
            "price": 33,
            "quantity": 45
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
        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );
        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Allow',
                'GET, POST'
            )
        );

        $product = json_decode($client->getResponse()->getContent());
        $this->assertEquals(1, $product->id);
        $this->assertEquals('testTitle', $product->title);
        $this->assertEquals('testDescription', $product->description);
        $this->assertEquals('https://img.url/test.png', $product->imgUrl);
        $this->assertEquals(33, $product->price);
        $this->assertEquals(45, $product->quantity);
    }

    /**
     * POST a product with error
     */
    public function testPostProductValidation()
    {
        $this->loadFixtures();
        $client = $this->makeClient();
        $productJson = '{
            
        }';
        $this->postProduct($productJson, $client);

        $this->assertStatusCode(400, $client);
        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );
        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Allow',
                'GET, POST'
            )
        );

        $error = json_decode($client->getResponse()->getContent());
        $this->assertEquals(5, count($error->violations));
        $this->assertEquals('title', $error->violations[0]->propertyPath);
        $this->assertEquals('This value should not be blank.', $error->violations[0]->title);
        $this->assertEquals('description', $error->violations[1]->propertyPath);
        $this->assertEquals('This value should not be blank.', $error->violations[1]->title);
        $this->assertEquals('imgUrl', $error->violations[2]->propertyPath);
        $this->assertEquals('This value should not be blank.', $error->violations[2]->title);
        $this->assertEquals('price', $error->violations[3]->propertyPath);
        $this->assertEquals('This value should not be blank.', $error->violations[3]->title);
        $this->assertEquals('quantity', $error->violations[4]->propertyPath);
        $this->assertEquals('This value should not be blank.', $error->violations[4]->title);

        $this->postProduct('{"imgUrl": "notARealUrl"}', $client);
        $error = json_decode($client->getResponse()->getContent());
        $this->assertEquals(5, count($error->violations));
        $this->assertEquals('imgUrl', $error->violations[2]->propertyPath);
        $this->assertEquals('This value is not a valid URL.', $error->violations[2]->title);

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
            "description": "newDescription",
            "imgUrl": "https://img.url/test.png",
            "price": 33,
            "quantity": 45
        }';

        $this->putProduct(1, $productJson, $client);
        $this->assertStatusCode(200, $client);
        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );
        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Allow',
                'GET, PUT, DELETE'
            )
        );

        $updatedProduct = json_decode($client->getResponse()->getContent());
        $this->assertEquals(1, $updatedProduct->id);
        $this->assertEquals('newTitle', $updatedProduct->title);
        $this->assertEquals('newDescription', $updatedProduct->description);
        $this->assertEquals('https://img.url/test.png', $updatedProduct->imgUrl);
        $this->assertEquals(33, $updatedProduct->price);
        $this->assertEquals(45, $updatedProduct->quantity);

        $client->request('GET', '/v1/product/1');
        $product = json_decode($client->getResponse()->getContent());
        $this->assertEquals($product->id, $updatedProduct->id);
        $this->assertEquals($product->title, $updatedProduct->title);
        $this->assertEquals($product->description, $updatedProduct->description);
        $this->assertEquals($product->imgUrl, $updatedProduct->imgUrl);
        $this->assertEquals($product->price, $updatedProduct->price);
        $this->assertEquals($product->quantity, $updatedProduct->quantity);
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
           
        }';

        $this->putProduct(1, $productJson, $client);
        $this->assertStatusCode(400, $client);
        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );
        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Allow',
                'GET, PUT, DELETE'
            )
        );

        $error = json_decode($client->getResponse()->getContent());
        $this->assertEquals(5, count($error->violations));
        $this->assertEquals('title', $error->violations[0]->propertyPath);
        $this->assertEquals('This value should not be blank.', $error->violations[0]->title);
        $this->assertEquals('description', $error->violations[1]->propertyPath);
        $this->assertEquals('This value should not be blank.', $error->violations[1]->title);
        $this->assertEquals('imgUrl', $error->violations[2]->propertyPath);
        $this->assertEquals('This value should not be blank.', $error->violations[2]->title);
        $this->assertEquals('price', $error->violations[3]->propertyPath);
        $this->assertEquals('This value should not be blank.', $error->violations[3]->title);
        $this->assertEquals('quantity', $error->violations[4]->propertyPath);
        $this->assertEquals('This value should not be blank.', $error->violations[4]->title);

        $this->postProduct('{"imgUrl": "notARealUrl"}', $client);
        $error = json_decode($client->getResponse()->getContent());
        $this->assertEquals(5, count($error->violations));
        $this->assertEquals('imgUrl', $error->violations[2]->propertyPath);
        $this->assertEquals('This value is not a valid URL.', $error->violations[2]->title);
    }

    /**
     * DELETE a product
     */
    public function testDeleteProduct()
    {
        $this->loadFixtures([
            'App\Fixture\Test\ProductFixture'
        ]);
        $client = $this->makeClient();
        $client->request(
            'DELETE',
            '/v1/product/1',
            [],
            [],
            array('CONTENT_TYPE' => 'application/json'),
            null
        );
        $this->assertStatusCode(204, $client);
        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Allow',
                'GET, PUT, DELETE'
            )
        );

        $client->request('GET', '/v1/product');
        $products = json_decode($client->getResponse()->getContent());
        $this->assertEquals(count($products), 19);
        $this->assertEquals(2, $products[0]->id);
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