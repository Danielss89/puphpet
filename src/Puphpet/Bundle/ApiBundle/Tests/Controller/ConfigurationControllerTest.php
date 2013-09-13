<?php

namespace Puphpet\Bundle\ApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @group functional
 */
class ConfigurationControllerTest extends WebTestCase
{
    /**
     * @group valid
     */
    public function testValidateCorrectConfiguration()
    {
        $client = static::createClient();

        $crawler = $client->request(
            'POST',
            '/api/configuration/default/validate',
            array(), // parameters
            array(), // files
            array(), //server
            json_encode(
                [
                    'configuration' =>
                    [
                        'server' => ['packages' => []],

                        'apache' => [
                            'modules' => ['rewrite', 'ssl'],

                            'vhosts' => [
                                [
                                    'server_name' => 'vhost1.example.com',
                                    'server_aliases' => ['alias1.example.com', 'www.example.com'],
                                    'document_root' => '/var/www/project',
                                    'port' => 80,
                                    'env_vars' => ['foo' => 'bar']
                                ]
                            ]

                        ]// end apache

                    ]
                ]
            )
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $decoded = $this->parseResult($client);
        $this->assertInternalType('array', $decoded);
        $this->assertArrayHasKey('result', $decoded);
        $this->assertTrue($decoded['result']);
    }

    public function testInvalideConfiguration()
    {
        $client = static::createClient();

        $crawler = $client->request(
            'POST',
            '/api/configuration/default/validate',
            array(), // parameters
            array(), // files
            array(), //server
            json_encode(['configuration' => ['unknown' => ['packages' => []]]])
        );

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    public function testIncompleteConfiguration()
    {
        $client = static::createClient();

        $crawler = $client->request(
            'POST',
            '/api/configuration/default/validate',
            array(), // parameters
            array(), // files
            array(), //server
            json_encode([])
        );

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    private function parseResult(Client $client)
    {
        return json_decode($client->getResponse()->getContent(), true);
    }
}
