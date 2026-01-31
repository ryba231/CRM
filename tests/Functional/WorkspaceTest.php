<?php

namespace Tests\Functional;

use App\DataFixtures\UserFixtures;
use App\DataFixtures\WorkspaceFixtures;
use App\Tests\Support\DatabaseWebTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\DBAL\Connection;

class WorkspaceTest extends DatabaseWebTestCase
{
    private ?EntityManagerInterface $em = null;
    private ?Connection $connection = null;

    protected function setUp(): void
    {
    }

    protected function tearDown(): void
    {
       if($this->connection && $this->connection->isTransactionActive())
        {
            $this->connection->rollBack();
        }

        $this->em = null;
        $this->connection = null;
        parent::tearDown();
    }

    public function testGetListWorkspaceSucess() : void
    {
        $client = static::createClient();

        $container = self::getContainer();
        $em = $container->get(EntityManagerInterface::class);

        $this->connection = $em->getConnection();
        
        $fixture = new WorkspaceFixtures(
            $container->get('security.user_password_hasher')
        );

        $fixture->load($em);

        $email = 'user.workspace@example.com';


        $client->request(
            'POST',
            '/api/v1/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => $email,
                'password' => 'test1234'
            ])
        );
        $token = json_decode($client->getResponse()->getContent(), true)['token'];

        $client->request(
            'GET',
            '/api/v1/workspace',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            ],
        );
        
        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('data', $data);
        $this->assertArrayHasKey('meta', $data);
        $this->assertSame(
            'Workspace: user.workspace@example.com',
            $data['data'][0]['name']
        );
    }

    public function testCreateWorkspaceFailWithoutBearer() : void 
    {
        $client = static::createClient();

        $container = self::getContainer();
        $em = $container->get(EntityManagerInterface::class);

        $this->connection = $em->getConnection();
        $this->connection->beginTransaction();

        $uniqueWorkspaceName = 'workspace_' . time();

        $client->request(
            'POST',
            '/api/v1/workspace',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json'
            ],
            json_encode([
                'name' => $uniqueWorkspaceName,
            ])
        );
        
        $this->assertResponseStatusCodeSame(401);

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('message', $data);
        $this->assertSame(
            'JWT Token not found',
            $data['message']
        );

    }

    public function testCreateWorkspaceFailWithoutName() : void 
    {
        $client = static::createClient();

        $container = self::getContainer();
        $em = $container->get(EntityManagerInterface::class);

        $this->connection = $em->getConnection();

        $fixture = new WorkspaceFixtures(
            $container->get('security.user_password_hasher')
        );

        $fixture->load($em);

        $email = 'user.workspace@example.com';

        $client->request(
            'POST',
            '/api/v1/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => $email,
                'password' => 'test1234'
            ])
        );

        $token = json_decode($client->getResponse()->getContent(), true)['token'];

        $client->request(
            'POST',
            '/api/v1/workspace',
            [],
            [],
            [
                'HTTP_AUTHORIZATION' => 'Bearer '.$token,
                'CONTENT_TYPE' => 'application/json'
            ]
        );
        
        $this->assertResponseStatusCodeSame(400);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('errors', $data);
        $this->assertSame(
            'This value should not be blank.',
            $data['errors']['name']
        );

    }

    public function testGetWorkspaceSucess() : void 
    {
        $client = static::createClient();

        $container = self::getContainer();
        $em = $container->get(EntityManagerInterface::class);

        $this->connection = $em->getConnection();
        
        $fixture = new WorkspaceFixtures(
            $container->get('security.user_password_hasher')
        );

        $fixture->load($em);

        $email = 'user.workspace@example.com';


        $client->request(
            'POST',
            '/api/v1/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => $email,
                'password' => 'test1234'
            ])
        );
        $token = json_decode($client->getResponse()->getContent(), true)['token'];

        $client->request(
            'GET',
            '/api/v1/workspace/1',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            ],
        );
        
        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('name', $data);
        $this->assertSame(
            'Workspace: user.workspace@example.com',
            $data['name']
        );
    }

    public function testGetWorkspaceFailNotFound() : void 
    {
        $client = static::createClient();

        $container = self::getContainer();
        $em = $container->get(EntityManagerInterface::class);

        $this->connection = $em->getConnection();

        $fixture = new UserFixtures(
            $container->get('security.user_password_hasher')
        );

        $fixture->load($em);

        $email = 'user@example.com';

        $client->request(
            'POST',
            '/api/v1/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => $email,
                'password' => 'test1234'
            ])
        );
        $token = json_decode($client->getResponse()->getContent(), true)['token'];

        $client->request(
            'GET',
            '/api/v1/workspace/100000',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            ],
        );
        
        $this->assertResponseStatusCodeSame(404);

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $data);
        $this->assertSame(
            'Workspace not found',
            $data['error']
        );
    }

    public function testUpdateWorkspaceSucess() : void 
    {
        $client = static::createClient();

        $container = self::getContainer();
        $em = $container->get(EntityManagerInterface::class);

        $this->connection = $em->getConnection();
        
        $fixture = new WorkspaceFixtures(
            $container->get('security.user_password_hasher')
        );

        $fixture->load($em);

        $email = 'user.workspace@example.com';


        $client->request(
            'POST',
            '/api/v1/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => $email,
                'password' => 'test1234'
            ])
        );
        $token = json_decode($client->getResponse()->getContent(), true)['token'];

        $client->request(
            'PATCH',
            '/api/v1/workspace/1',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            ],
            json_encode([
                'name' => $email,
            ])
        );
        
        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('name', $data);
        $this->assertSame(
            $email,
            $data['name']
        );
    }

    public function testUpdateWorkspaceFailWithoutName() : void 
    {
        $client = static::createClient();

        $container = self::getContainer();
        $em = $container->get(EntityManagerInterface::class);

        $this->connection = $em->getConnection();
        
        $fixture = new WorkspaceFixtures(
            $container->get('security.user_password_hasher')
        );

        $fixture->load($em);

        $email = 'user.workspace@example.com';


        $client->request(
            'POST',
            '/api/v1/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => $email,
                'password' => 'test1234'
            ])
        );
        $token = json_decode($client->getResponse()->getContent(), true)['token'];

        $client->request(
            'PATCH',
            '/api/v1/workspace/1',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            ]
        );
        
        $this->assertResponseStatusCodeSame(400);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('errors', $data);
        $this->assertSame(
            'This value should not be blank.',
            $data['errors']['name']
        );
    }

    public function testDeleteWorkspaceSucess() : void 
    {
        $client = static::createClient();

        $container = self::getContainer();
        $em = $container->get(EntityManagerInterface::class);

        $this->connection = $em->getConnection();
        
        $fixture = new WorkspaceFixtures(
            $container->get('security.user_password_hasher')
        );

        $fixture->load($em);

        $email = 'user.workspace@example.com';


        $client->request(
            'POST',
            '/api/v1/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => $email,
                'password' => 'test1234'
            ])
        );
        $token = json_decode($client->getResponse()->getContent(), true)['token'];

        $client->request(
            'DELETE',
            '/api/v1/workspace/1',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            ],
        );
        
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(204);
    }

    public function testDeleteWorkspaceFail() : void 
    {
        $client = static::createClient();

        $container = self::getContainer();
        $em = $container->get(EntityManagerInterface::class);

        $this->connection = $em->getConnection();
        
        $fixture = new WorkspaceFixtures(
            $container->get('security.user_password_hasher')
        );

        $fixture->load($em);

        $email = 'user.workspace@example.com';


        $client->request(
            'POST',
            '/api/v1/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => $email,
                'password' => 'test1234'
            ])
        );
        $token = json_decode($client->getResponse()->getContent(), true)['token'];

        $client->request(
            'DELETE',
            '/api/v1/workspace/2',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            ],
        );
        
        $this->assertResponseStatusCodeSame(404);
    }

    public function testRestoreWorkspaceSucess() : void 
    {
        $client = static::createClient();

        $container = self::getContainer();
        $em = $container->get(EntityManagerInterface::class);

        $this->connection = $em->getConnection();
        
        $fixture = new WorkspaceFixtures(
            $container->get('security.user_password_hasher')
        );

        $fixture->load($em);

        $email = 'user.workspace@example.com';


        $client->request(
            'POST',
            '/api/v1/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => $email,
                'password' => 'test1234'
            ])
        );
        $token = json_decode($client->getResponse()->getContent(), true)['token'];

        $client->request(
            'POST',
            '/api/v1/workspace/3/restore',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            ],
        );
        
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(204);
    }

    public function testRestoreWorkspaceFail() : void 
    {
        $client = static::createClient();

        $container = self::getContainer();
        $em = $container->get(EntityManagerInterface::class);

        $this->connection = $em->getConnection();
        
        $fixture = new WorkspaceFixtures(
            $container->get('security.user_password_hasher')
        );

        $fixture->load($em);

        $email = 'user.workspace@example.com';


        $client->request(
            'POST',
            '/api/v1/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => $email,
                'password' => 'test1234'
            ])
        );
        $token = json_decode($client->getResponse()->getContent(), true)['token'];

        $client->request(
            'POST',
            '/api/v1/workspace/4/restore',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            ],
        );
        
        $this->assertResponseStatusCodeSame(404);
    }
}