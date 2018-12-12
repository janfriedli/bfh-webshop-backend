<?php
use Liip\FunctionalTestBundle\Test\WebTestCase;

class ProductControllerTest extends WebTestCase
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
        $this->client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'testuser',
            'PHP_AUTH_PW'   => 'test',
        ));
    }
    
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

        $this->client->request('GET', '/v1/product');
        $products = json_decode($this->client->getResponse()->getContent());
        $this->assertEquals(count($products), 20);
        $this->assertTrue(
            $this->client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );
        $this->assertTrue(
            $this->client->getResponse()->headers->contains(
                'Allow',
                'GET, POST'
            )
        );

        foreach ($products as $key => $product) {
            $this->assertEquals('description'.$key, $product->description);
            $this->assertEquals('product'.$key, $product->title);
            $this->assertEquals($key, $product->price);
            $this->assertEquals($key, $product->quantity);
            $this->assertEquals('https://img.url/test' .$key. '.jpg', $product->img_url);
        }
        $this->assertStatusCode(200, $this->client);
    }

    /**
     * POST a product
     */
    public function testPostProduct()
    {
        $this->loadFixtures();

        $productJson = '{
            "title": "testTitle",
            "description": "testDescription",
            "img_url": "https://img.url/test.png",
            "price": 33,
            "quantity": 45
        }';

        $this->client->request(
            'POST',
            '/v1/product',
            [],
            [],
            array('CONTENT_TYPE' => 'application/json'),
            $productJson
        );
        $this->assertStatusCode(201, $this->client);
        $this->assertTrue(
            $this->client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );
        $this->assertTrue(
            $this->client->getResponse()->headers->contains(
                'Allow',
                'GET, POST'
            )
        );

        $product = json_decode($this->client->getResponse()->getContent());
        $this->assertEquals(1, $product->id);
        $this->assertEquals('testTitle', $product->title);
        $this->assertEquals('testDescription', $product->description);
        $this->assertEquals('https://img.url/test.png', $product->img_url);
        $this->assertEquals(33, $product->price);
        $this->assertEquals(45, $product->quantity);
    }

    /**
     * POST a product with error
     */
    public function testPostProductValidation()
    {
        $this->loadFixtures();
        $productJson = '{
            
        }';
        $this->postProduct($productJson, $this->client);

        $this->assertStatusCode(400, $this->client);
        $this->assertTrue(
            $this->client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );
        $this->assertTrue(
            $this->client->getResponse()->headers->contains(
                'Allow',
                'GET, POST'
            )
        );

        $error = json_decode($this->client->getResponse()->getContent());
        $this->assertEquals(5, count($error));
        $this->assertEquals('title', $error[0]->property_path);
        $this->assertEquals('This value should not be blank.', $error[0]->message);
        $this->assertEquals('description', $error[1]->property_path);
        $this->assertEquals('This value should not be blank.', $error[1]->message);
        $this->assertEquals('imgUrl', $error[2]->property_path);
        $this->assertEquals('This value should not be blank.', $error[2]->message);
        $this->assertEquals('price', $error[3]->property_path);
        $this->assertEquals('This value should not be blank.', $error[3]->message);
        $this->assertEquals('quantity', $error[4]->property_path);
        $this->assertEquals('This value should not be blank.', $error[4]->message);

        $this->postProduct('{"img_url": "notARealUrl"}', $this->client);
        $error = json_decode($this->client->getResponse()->getContent());
        $this->assertEquals(5, count($error));
        $this->assertEquals('imgUrl', $error[2]->property_path);
        $this->assertEquals('This value is not a valid URL.', $error[2]->message);

    }

    /**
     * PUT a product
     */
    public function testPutProduct()
    {
        $this->loadFixtures([
            'App\Fixture\Test\ProductFixture'
        ]);

        $productJson = '{
            "title": "newTitle",
            "description": "newDescription",
            "img_url": "https://img.url/test.png",
            "price": 33,
            "quantity": 45
        }';

        $this->putProduct(1, $productJson, $this->client);
        $this->assertStatusCode(200, $this->client);
        $this->assertTrue(
            $this->client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );
        $this->assertTrue(
            $this->client->getResponse()->headers->contains(
                'Allow',
                'GET, PUT, DELETE'
            )
        );

        $updatedProduct = json_decode($this->client->getResponse()->getContent());
        $this->assertEquals(1, $updatedProduct->id);
        $this->assertEquals('newTitle', $updatedProduct->title);
        $this->assertEquals('newDescription', $updatedProduct->description);
        $this->assertEquals('https://img.url/test.png', $updatedProduct->img_url);
        $this->assertEquals(33, $updatedProduct->price);
        $this->assertEquals(45, $updatedProduct->quantity);

        $this->client->request('GET', '/v1/product/1');
        $product = json_decode($this->client->getResponse()->getContent());
        $this->assertEquals($product->id, $updatedProduct->id);
        $this->assertEquals($product->title, $updatedProduct->title);
        $this->assertEquals($product->description, $updatedProduct->description);
        $this->assertEquals($product->img_url, $updatedProduct->img_url);
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

        $productJson = '{
           
        }';

        $this->putProduct(1, $productJson, $this->client);
        $this->assertStatusCode(400, $this->client);
        $this->assertTrue(
            $this->client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );
        $this->assertTrue(
            $this->client->getResponse()->headers->contains(
                'Allow',
                'GET, PUT, DELETE'
            )
        );

        $error = json_decode($this->client->getResponse()->getContent());
        $this->assertEquals(5, count($error));
        $this->assertEquals('title', $error[0]->property_path);
        $this->assertEquals('This value should not be blank.', $error[0]->message);
        $this->assertEquals('description', $error[1]->property_path);
        $this->assertEquals('This value should not be blank.', $error[1]->message);
        $this->assertEquals('imgUrl', $error[2]->property_path);
        $this->assertEquals('This value should not be blank.', $error[2]->message);
        $this->assertEquals('price', $error[3]->property_path);
        $this->assertEquals('This value should not be blank.', $error[3]->message);
        $this->assertEquals('quantity', $error[4]->property_path);
        $this->assertEquals('This value should not be blank.', $error[4]->message);

        $this->postProduct('{"img_url": "notARealUrl"}', $this->client);
        $error = json_decode($this->client->getResponse()->getContent());
        $this->assertEquals(5, count($error));
        $this->assertEquals('imgUrl', $error[2]->property_path);
        $this->assertEquals('This value is not a valid URL.', $error[2]->message);
    }

    /**
     * DELETE a product
     */
    public function testDeleteProduct()
    {
        $this->loadFixtures([
            'App\Fixture\Test\ProductFixture'
        ]);
        $this->client->request(
            'DELETE',
            '/v1/product/1',
            [],
            [],
            array('CONTENT_TYPE' => 'application/json'),
            null
        );
        $this->assertStatusCode(204, $this->client);
        $this->assertTrue(
            $this->client->getResponse()->headers->contains(
                'Allow',
                'GET, PUT, DELETE'
            )
        );

        $this->client->request('GET', '/v1/product');
        $products = json_decode($this->client->getResponse()->getContent());
        $this->assertEquals(count($products), 19);
        $this->assertEquals(2, $products[0]->id);
    }

    /**
     * test all pw protected routes
     */
    public function test401() {
        $client = $this->makeClient();

        $client->request('POST', '/v1/product');
        $this->assertStatusCode(401, $client);

        $client->request('PUT', '/v1/product/1');
        $this->assertStatusCode(401, $client);

        $client->request('DELETE', '/v1/product/1');
        $this->assertStatusCode(401, $client);
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