<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{

    public function testIndex() {
        $client = static::createClient([], [
            'HTTP_HOST' => 'localhost:8000'
        ]);

        $crawler = $client->request('GET', '/');



        $this->assertEquals($crawler->filter('html:contains("Bienvenue sur le site de la copropriété")')->count(), 1);
    }
}