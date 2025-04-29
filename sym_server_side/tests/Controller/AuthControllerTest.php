<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AuthControllerTest extends WebTestCase
{
    public function testRegister(): void
    {
        
        $client = static::createClient();
        
        
        $data = [
            'username' => 'testuserss',
            'password' => 'password123',
        ];
        
        
        $client->request('POST', '/api/register', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($data));

        
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        
        
        $this->assertJson($client->getResponse()->getContent());
    }

    public function testLogin(): void
    {
        
        $client = static::createClient();
        
        
        $data = [
            'username' => 'testuserss',
            'password' => 'password123',
        ];

        
        $client->request('POST', '/api/login', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($data));

       
        $this->assertResponseIsSuccessful();

        
        $responseContent = json_decode($client->getResponse()->getContent(), true);
        
        
        $this->assertArrayHasKey('token', $responseContent);
    }
}
