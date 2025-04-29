<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Todo;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TodoRepository;

class TodoControllerTest extends WebTestCase
{
    private $client;
    private $em;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->em = static::getContainer()->get(EntityManagerInterface::class); 
    }

    // Test GET /api/todos
    public function testGetTodos(): void
    {
        $this->client->request('GET', '/api/todos', [], [], [
            'HTTP_Authorization' => 'Bearer ' . $this->getJwtToken(),
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJson($this->client->getResponse()->getContent());
    }

    // Test POST /api/todos
    public function testCreateTodo(): void
    {
        $data = [
            'title' => 'Test Todo',
            'description' => 'Test description',
        ];

        
        $this->client->request('POST', '/api/todos', [], [], [
            'HTTP_Authorization' => 'Bearer ' . $this->getJwtToken(),
            'CONTENT_TYPE' => 'application/json'
        ], json_encode($data));

        
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('message', $responseData);
        $this->assertArrayHasKey('id', $responseData);
        $this->assertEquals('Todo created successfully!', $responseData['message']);
    }

    // Test PUT /api/todos/{id}
    public function testUpdateTodo(): void
    {
        
        $todo = new Todo();
        $todo->setTitle('Old Title');
        $todo->setDescription('Old description');
        $this->em->persist($todo);
        $this->em->flush();

        
        $data = [
            'title' => 'Updated Title',
            'description' => 'Updated description',
            'completed' => true,
        ];

        
        $this->client->request('PUT', '/api/todos/' . $todo->getId(), [], [], [
            'HTTP_Authorization' => 'Bearer ' . $this->getJwtToken(),
            'CONTENT_TYPE' => 'application/json'
        ], json_encode($data));

        
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('message', $responseData);
        $this->assertArrayHasKey('id', $responseData);
        $this->assertEquals('Todo updated successfully!', $responseData['message']);
    }

    // Test DELETE /api/todos/{id} 
    public function testDeleteTodo(): void
    {
        
        $todo = new Todo();
        $todo->setTitle('Todo to delete');
        $todo->setDescription('Todo description');
        $this->em->persist($todo);
        $this->em->flush();

        $this->client->request('DELETE', '/api/todos/' . $todo->getId(), [], [], [
            'HTTP_Authorization' => 'Bearer ' . $this->getJwtToken(),
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('message', $responseData);
        $this->assertArrayHasKey('id', $responseData);
        $this->assertEquals('Todo deleted successfully!', $responseData['message']);
    }

    
    private function getJwtToken(): string
    {
        $this->client->request('POST', '/api/login', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'username' => 'hamzaelm',
            'password' => 'password',
        ]));

        $data = json_decode($this->client->getResponse()->getContent(), true);
        return $data['token'];
    }
}
