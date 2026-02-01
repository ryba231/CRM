<?php

namespace Tests\Functional;

use App\DataFixtures\WorkspaceUserFixtures;
use App\Tests\Support\DatabaseWebTestCase;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;

class WorkspaceUserTest extends DatabaseWebTestCase
{
    private ?Connection $connection = null;

    protected function setUp(): void
    {}

    protected function tearDown(): void
    {
        if($this->connection && $this->connection->isTransactionActive())
        {
            $this->connection->rollBack();
        }

        $this->connection = null;
        parent::tearDown();
    }

    public function testGetListWorkspaceUserSuccess() : void 
    {
        $client = static::createClient();

        $container = self::getContainer();
        $em = $container->get(EntityManagerInterface::class);

        $this->connection = $em->getConnection();
        
        $fixture = new WorkspaceUserFixtures(
            $container->get('security.user_password_hasher')
        );

        $fixture->load($em);

        $owner = 'owner@example.com';

        $client->request(
            'POST',
            '/api/v1/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => $owner,
                'password' => 'test1234'
            ])
        );
        $token = json_decode($client->getResponse()->getContent(), true)['token'];

        $client->request(
            'GET',
            '/api/v1/workspace-user',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => 'Bearer '.$token,
                'HTTP_X-Workspace-Id' => '1'
            ],
        );
        
        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('data', $data);
        $this->assertNotEmpty($data['data']);
        $this->assertArrayHasKey('meta', $data);
        $this->assertSame(
            $owner,
            $data['data'][0]['email']
        );
    }

    public function testGetListWorkspaceUserFailNotAccess() : void 
    {
        $client = static::createClient();

        $container = self::getContainer();
        $em = $container->get(EntityManagerInterface::class);

        $this->connection = $em->getConnection();
        
        $fixture = new WorkspaceUserFixtures(
            $container->get('security.user_password_hasher')
        );

        $fixture->load($em);

        $member = 'member@example.com';

        $client->request(
            'POST',
            '/api/v1/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => $member,
                'password' => 'test1234'
            ])
        );
        $token = json_decode($client->getResponse()->getContent(), true)['token'];

        $client->request(
            'GET',
            '/api/v1/workspace-user',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => 'Bearer '.$token,
                'HTTP_X-Workspace-Id' => '1'
            ],
        );
        
        $this->assertResponseStatusCodeSame(404);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $data);
        $this->assertSame(
            'Access denied to workspace',
            $data['error']
        );
    }

    public function testAddWorkspaceUserSuccess() : void 
    {
        $client = static::createClient();

        $container = self::getContainer();
        $em = $container->get(EntityManagerInterface::class);

        $this->connection = $em->getConnection();
        
        $fixture = new WorkspaceUserFixtures(
            $container->get('security.user_password_hasher')
        );

        $fixture->load($em);

        $owner = 'owner@example.com';
        $member = 'add@example.com';

        $client->request(
            'POST',
            '/api/v1/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => $owner,
                'password' => 'test1234'
            ])
        );
        $token = json_decode($client->getResponse()->getContent(), true)['token'];

        $client->request(
            'POST',
            '/api/v1/workspace-user',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => 'Bearer '.$token,
                'HTTP_X-Workspace-Id' => '1'
            ],
            json_encode([
                'email' => $member,
                'role' => 'member'
            ])
        );
        
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(204);
    }

    public function testAddWorkspaceUserFailWithoutRole() : void 
    {
        $client = static::createClient();

        $container = self::getContainer();
        $em = $container->get(EntityManagerInterface::class);

        $this->connection = $em->getConnection();
        
        $fixture = new WorkspaceUserFixtures(
            $container->get('security.user_password_hasher')
        );

        $fixture->load($em);

        $owner = 'owner@example.com';
        $add = 'add@example.com';

        $client->request(
            'POST',
            '/api/v1/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => $owner,
                'password' => 'test1234'
            ])
        );
        $token = json_decode($client->getResponse()->getContent(), true)['token'];

        $client->request(
            'POST',
            '/api/v1/workspace-user',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => 'Bearer '.$token,
                'HTTP_X-Workspace-Id' => '1'
            ],
            json_encode([
                'email' => $add,
            ])
        );
        
        $this->assertResponseStatusCodeSame(400);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('errors', $data);
        $this->assertSame(
            'This value should not be blank.',
            $data['errors']['role']
        );
    }

    public function testAddWorkspaceUserFailNoAccess() : void 
    {
        $client = static::createClient();

        $container = self::getContainer();
        $em = $container->get(EntityManagerInterface::class);

        $this->connection = $em->getConnection();
        
        $fixture = new WorkspaceUserFixtures(
            $container->get('security.user_password_hasher')
        );

        $fixture->load($em);

        $member = 'member@example.com';
        $add = 'add@example.com';

        $client->request(
            'POST',
            '/api/v1/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => $member,
                'password' => 'test1234'
            ])
        );
        $token = json_decode($client->getResponse()->getContent(), true)['token'];

        $client->request(
            'POST',
            '/api/v1/workspace-user',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => 'Bearer '.$token,
                'HTTP_X-Workspace-Id' => '1'
            ],
            json_encode([
                'email' => $add,
                'role' => 'admin'
            ])
        );
        
        $this->assertResponseStatusCodeSame(404);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $data);
        $this->assertSame(
            'Access denied to workspace',
            $data['error']
        );
    }

    public function testChangeRoleWorkspaceUserSuccess() : void 
    {
        $client = static::createClient();

        $container = self::getContainer();
        $em = $container->get(EntityManagerInterface::class);

        $this->connection = $em->getConnection();
        
        $fixture = new WorkspaceUserFixtures(
            $container->get('security.user_password_hasher')
        );

        $fixture->load($em);

        $owner = 'owner@example.com';

        $client->request(
            'POST',
            '/api/v1/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => $owner,
                'password' => 'test1234'
            ])
        );
        $token = json_decode($client->getResponse()->getContent(), true)['token'];

        $client->request(
            'PATCH',
            '/api/v1/workspace-user/2',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => 'Bearer '.$token,
                'HTTP_X-Workspace-Id' => '1'
            ],
            json_encode([
                'role' => 'admin'
            ])
        );
        
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('role', $data);
        $this->assertSame(
            'admin',
            $data['role']
        );
    }

    public function testChangeRoleWorkspaceUserFailNoAccess() : void 
    {
        $client = static::createClient();

        $container = self::getContainer();
        $em = $container->get(EntityManagerInterface::class);

        $this->connection = $em->getConnection();
        
        $fixture = new WorkspaceUserFixtures(
            $container->get('security.user_password_hasher')
        );

        $fixture->load($em);

        $member = 'member@example.com';

        $client->request(
            'POST',
            '/api/v1/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => $member,
                'password' => 'test1234'
            ])
        );
        $token = json_decode($client->getResponse()->getContent(), true)['token'];

        $client->request(
            'PATCH',
            '/api/v1/workspace-user/1',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => 'Bearer '.$token,
                'HTTP_X-Workspace-Id' => '1'
            ],
            json_encode([
                'role' => 'admin'
            ])
        );
        
        $this->assertResponseStatusCodeSame(404);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $data);
        $this->assertSame(
            'Access denied to workspace',
            $data['error']
        );
    }

    public function testDeleteWorkspaceUserSuccess() : void 
    {
        $client = static::createClient();

        $container = self::getContainer();
        $em = $container->get(EntityManagerInterface::class);

        $this->connection = $em->getConnection();
        
        $fixture = new WorkspaceUserFixtures(
            $container->get('security.user_password_hasher')
        );

        $fixture->load($em);

        $owner = 'owner@example.com';

        $client->request(
            'POST',
            '/api/v1/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => $owner,
                'password' => 'test1234'
            ])
        );
        $token = json_decode($client->getResponse()->getContent(), true)['token'];

        $client->request(
            'DELETE',
            '/api/v1/workspace-user/2',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => 'Bearer '.$token,
                'HTTP_X-Workspace-Id' => '1'
            ]
        );
        
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(204);
    }

    public function testDeleteWorkspaceUserFailNoAccess() : void 
    {
        $client = static::createClient();

        $container = self::getContainer();
        $em = $container->get(EntityManagerInterface::class);

        $this->connection = $em->getConnection();
        
        $fixture = new WorkspaceUserFixtures(
            $container->get('security.user_password_hasher')
        );

        $fixture->load($em);

        $member = 'member@example.com';

        $client->request(
            'POST',
            '/api/v1/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => $member,
                'password' => 'test1234'
            ])
        );
        $token = json_decode($client->getResponse()->getContent(), true)['token'];

        $client->request(
            'DELETE',
            '/api/v1/workspace-user/1',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => 'Bearer '.$token,
                'HTTP_X-Workspace-Id' => '1'
            ]
        );
        
        $this->assertResponseStatusCodeSame(404);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $data);
        $this->assertSame(
            'Access denied to workspace',
            $data['error']
        );
    }

}