<?php
use Liip\FunctionalTestBundle\Test\WebTestCase;

class StoreOrderControllerTest extends WebTestCase
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
     * GET the empty storeOrders
     */
    public function testGetEmptyStoreOrders()
    {
        $this->loadFixtures();
        $this->client->request('GET', '/v1/order');
        $this->assertEmpty(json_decode($this->client->getResponse()->getContent()));
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
                'GET, POST'
            )
        );
    }


    /**
     * GET the populated storeOrders
     */
    public function testGetPopulatedStoreOrders()
    {
        $this->loadFixtures([
            'App\Fixture\Test\StoreOrderFixture'
        ]);

        $this->client->request('GET', '/v1/order');
        $storeOrders = json_decode($this->client->getResponse()->getContent());
        $this->assertEquals(count($storeOrders), 2);
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
        $this->assertStatusCode(200, $this->client);

        foreach ($storeOrders as $key => $storeOrder) {
            $this->assertEquals('CH'.$key, $storeOrder->country);
            $this->assertEquals('testName'.$key, $storeOrder->fullname);
            $this->assertEquals('testStreet'.$key, $storeOrder->street);
            $this->assertEquals('testZip'.$key, $storeOrder->zip);
        }

        $details = $storeOrders[0]->details;
        $this->assertEquals(2, count($details));
        $this->assertEquals(1, $details[0]->id);
        $this->assertEquals(2, $details[0]->quantity);
        $this->assertEquals(1, $details[0]->product->id);
        $this->assertEquals(2, $details[1]->id);
        $this->assertEquals(55, $details[1]->quantity);
        $this->assertEquals(2, $details[1]->product->id);
    }

    /**
     * POST a storeOrder
     */
    public function testPostStoreOrder()
    {
        $this->loadFixtures([
            'App\Fixture\Test\StoreOrderFixture'
        ]);

        $storeOrderJson = '{
            "street": "testStreet",
            "zip": "testZip",
            "fullname": "testName",
            "country": "CH",
            "paid": false,
            "details": [
                {
                    "product": {
                        "id": 1,
                        "title": "product",
                        "description": "description",
                        "img_url": "https://img.url/test.jpg",
                        "price": 0,
                        "quantity": 0
                    },
                    "quantity": 2
                },
                {
                    "product": {
                        "id": 2,
                        "title": "product0",
                        "description": "description0",
                        "img_url": "https://img.url/test0.jpg",
                        "price": 0,
                        "quantity": 0
                    },
                    "quantity": 55
                }
            ]
        }';

        $this->client->request(
            'POST',
            '/v1/order',
            [],
            [],
            array('CONTENT_TYPE' => 'application/json'),
            $storeOrderJson
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

        $storeOrder = json_decode($this->client->getResponse()->getContent());
        $this->assertEquals('CH', $storeOrder->country);
        $this->assertEquals('testName', $storeOrder->fullname);
        $this->assertEquals('testStreet', $storeOrder->street);
        $this->assertEquals('testZip', $storeOrder->zip);
        $this->assertFalse($storeOrder->paid);
        $details = $storeOrder->details;
        $this->assertEquals(2, count($details));
        $this->assertEquals(1, $details[0]->product->id);
        $this->assertEquals(2, $details[1]->product->id);
    }

    /**
     * POST a storeOrder with error
     */
    public function testPostStoreOrderValidation()
    {
        $this->loadFixtures([
            'App\Fixture\Test\StoreOrderFixture'
        ]);

        $storeOrderJson = '{

        }';
        $this->postStoreOrder($storeOrderJson, $this->client);

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
        $this->assertEquals('street', $error[0]->property_path);
        $this->assertEquals('This value should not be blank.', $error[0]->message);
        $this->assertEquals('zip', $error[1]->property_path);
        $this->assertEquals('This value should not be blank.', $error[1]->message);
        $this->assertEquals('fullname', $error[2]->property_path);
        $this->assertEquals('This value should not be blank.', $error[2]->message);
        $this->assertEquals('country', $error[3]->property_path);
        $this->assertEquals('This value should not be blank.', $error[3]->message);

        $storeOrderJson = '{
            "street": "testStreet",
            "zip": "testZip",
            "fullname": "testName",
            "country": "CH",
            "paid": false,
            "details": []
        }';

        $this->postStoreOrder($storeOrderJson, $this->client);
        $this->assertStatusCode(400, $this->client);
        $error = json_decode($this->client->getResponse()->getContent());
        $this->assertEquals('details', $error[0]->property_path);
        $this->assertEquals('This value should not be blank.', $error[0]->message);
    }

    /**
     * PUT a storeOrder
     */
    public function testPutStoreOrder()
    {
        $this->loadFixtures([
            'App\Fixture\Test\StoreOrderFixture'
        ]);

        $storeOrderJson = '{
            "street": "testStreet",
            "zip": "testZip",
            "fullname": "testFullname",
            "country": "testCountry",
            "paid": false,
            "details": [
               {
                    "id": "1",
                    "product": {
                        "id": 3,
                        "title": "product2",
                        "description": "description2",
                        "img_url": "https://img.url/test2.jpg",
                        "price": 2,
                        "quantity": 0
                    },
                    "quantity": 3
                },
                {
                    "id": "2",
                    "product": {
                        "id": 4,
                        "title": "product5",
                        "description": "description5",
                        "img_url": "https://img.url/test5.jpg",
                        "price": 5,
                        "quantity": 0
                    },
                    "quantity": 55
                }
            ]
        }';

        $this->putStoreOrder(1, $storeOrderJson, $this->client);
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

        $updatedStoreOrder = json_decode($this->client->getResponse()->getContent());
        $this->assertEquals(1, $updatedStoreOrder->id);
        $this->assertEquals('testStreet', $updatedStoreOrder->street);
        $this->assertEquals('testZip', $updatedStoreOrder->zip);
        $this->assertEquals('testFullname', $updatedStoreOrder->fullname);
        $this->assertEquals('testCountry', $updatedStoreOrder->country);
        $this->assertEquals(2, count($updatedStoreOrder->details));
        $this->assertEquals(3, $updatedStoreOrder->details[0]->product->id);
        $this->assertEquals(3, $updatedStoreOrder->details[0]->quantity);
        $this->assertEquals(4, $updatedStoreOrder->details[1]->product->id);
        $this->assertEquals(55, $updatedStoreOrder->details[1]->quantity);

        $this->client->request('GET', '/v1/order/1');
        $storeOrder = json_decode($this->client->getResponse()->getContent());
        $this->assertEquals($storeOrder->id, $updatedStoreOrder->id);
        $this->assertEquals($storeOrder->street, $updatedStoreOrder->street);
        $this->assertEquals($storeOrder->zip, $updatedStoreOrder->zip);
        $this->assertEquals($storeOrder->fullname, $updatedStoreOrder->fullname);
        $this->assertEquals($storeOrder->country, $updatedStoreOrder->country);
        $this->assertEquals(2, count($updatedStoreOrder->details));
        $this->assertEquals($storeOrder->details[0]->product->id, $updatedStoreOrder->details[0]->product->id);
        $this->assertEquals($storeOrder->details[0]->quantity, $updatedStoreOrder->details[0]->quantity);
        $this->assertEquals($storeOrder->details[1]->product->id, $updatedStoreOrder->details[1]->product->id);
        $this->assertEquals($storeOrder->details[1]->quantity, $updatedStoreOrder->details[1]->quantity);
    }

    /**
     * PUT a storeOrder with validation errors
     */
    public function testPutStoreOrderValidation()
    {
        $this->loadFixtures([
            'App\Fixture\Test\StoreOrderFixture'
        ]);

        $storeOrderJson = '{

        }';

        $this->putStoreOrder(1, $storeOrderJson, $this->client);
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
        $this->assertEquals('street', $error[0]->property_path);
        $this->assertEquals('This value should not be blank.', $error[0]->message);
        $this->assertEquals('zip', $error[1]->property_path);
        $this->assertEquals('This value should not be blank.', $error[1]->message);
        $this->assertEquals('fullname', $error[2]->property_path);
        $this->assertEquals('This value should not be blank.', $error[2]->message);
        $this->assertEquals('country', $error[3]->property_path);
        $this->assertEquals('This value should not be blank.', $error[3]->message);

        $storeOrderJson = '{
            "street": "testStreet",
            "zip": "testZip",
            "fullname": "testFullname",
            "country": "testCountry",
            "paid": false,
            "products": [

            ]
        }';

        $this->postStoreOrder($storeOrderJson, $this->client);
        $this->assertStatusCode(400, $this->client);
        $error = json_decode($this->client->getResponse()->getContent());
        $this->assertEquals('details', $error[0]->property_path);
        $this->assertEquals('This value should not be blank.', $error[0]->message);
    }

    /**
     * DELETE a storeOrder
     */
    public function testDeleteStoreOrder()
    {
        $this->loadFixtures([
            'App\Fixture\Test\StoreOrderFixture'
        ]);

        $this->client->request(
            'DELETE',
            '/v1/order/1',
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

        $this->client->request('GET', '/v1/order');
        $storeOrders = json_decode($this->client->getResponse()->getContent());
        $this->assertEquals(count($storeOrders), 1);
        $this->assertEquals(2, $storeOrders[0]->id);
    }

    /**
     * test all pw protected routes
     */
    public function test401() {
        $client = $this->makeClient();

        $client->request('GET', '/v1/order');
        $this->assertStatusCode(401, $client);

        $client->request('PUT', '/v1/order/1');
        $this->assertStatusCode(401, $client);

        $client->request('DELETE', '/v1/order/1');
        $this->assertStatusCode(401, $client);
    }


    /**
     * POST a storeOrder
     * @param string $body
     * @param $client
     */
    private function postStoreOrder(string $body, $client) {
        $client->request(
            'POST',
            '/v1/order',
            [],
            [],
            array('CONTENT_TYPE' => 'application/json'),
            $body
        );
    }

    /**
     * PUT a storeOrder
     * @param $id
     * @param string $body
     * @param $client
     */
    private function putStoreOrder(int $id,string $body, $client) {
        $client->request(
            'PUT',
            '/v1/order/' . $id,
            [],
            [],
            array('CONTENT_TYPE' => 'application/json'),
            $body
        );
    }
}