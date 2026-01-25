<?php

namespace Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\DataFixtures\UserFixtures;
use App\Repository\User\UserRepository;
use Doctrine\DBAL\Connection;

class AuthTest extends WebTestCase
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

    public function testLoginSuccess(): void
    {
        $client = static::createClient();

        $container = self::getContainer();
        $em = $container->get(EntityManagerInterface::class);

        $this->connection = $em->getConnection();
        
        $this->connection->beginTransaction();

        $fixture = new UserFixtures(
            $container->get('security.user_password_hasher')
        );

        $fixture->load($em);

        $client->request(
            'POST',
            '/api/v1/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => 'user@example.com',
                'password' => 'test1234'
            ])
        );

        $this->assertResponseIsSuccessful();

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('token', $data);
    }

    public function testLoginFail() : void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/v1/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => 'user+fail@example.com',
                'password' => 'pass'
            ])
        );

        $this->assertResponseStatusCodeSame(401);

    }

    public function testRegisterSuccess() : void 
    {
        $client = static::createClient();

        $container = self::getContainer();
        $em = $container->get(EntityManagerInterface::class);

        $this->connection = $em->getConnection();
        $this->connection->beginTransaction();

        $uniqueEmail = 'user_' . time() . '@example.com';
        $password = 'pass_' . time();

        $client->request(
            'POST',
            '/api/v1/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => $uniqueEmail,
                'first_name' => 'user',
                'last_name' => 'user',
                'password' => $password
            ])
        );

        $this->assertResponseIsSuccessful();

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('token', $data);
        $this->assertArrayHasKey('user', $data);

        $user = self::getContainer()
            ->get(UserRepository::class)
            ->findOneBy(['email' => $uniqueEmail]);

        self::assertNotNull($user);
    
    }

    public function testPasswordIsHashed() : void 
    {
        $client = static::createClient();

        $container = self::getContainer();
        $em = $container->get(EntityManagerInterface::class);

        $this->connection = $em->getConnection();
        $this->connection->beginTransaction();

        $uniqueEmail = 'user_' . time() . '@example.com';
        $password = 'pass_' . time();

        $client->request(
            'POST',
            '/api/v1/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => $uniqueEmail,
                'first_name' => 'user',
                'last_name' => 'user',
                'password' => $password
            ])
        );

        $this->assertResponseIsSuccessful();

        $user = self::getContainer()
            ->get(UserRepository::class)
            ->findOneBy(['email' => $uniqueEmail]);

        self::assertNotEquals($password, $user->getPassword());

    }

    public function testRegisterFailWithoutEmail() : void 
    {
        $client = static::createClient();

        $container = self::getContainer();
        $em = $container->get(EntityManagerInterface::class);

        $this->connection = $em->getConnection();
        $this->connection->beginTransaction();

        $password = 'pass_' . time();

        $client->request(
            'POST',
            '/api/v1/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'first_name' => 'user',
                'last_name' => 'user',
                'password' => $password
            ])
        );
        
        $this->assertResponseStatusCodeSame(400);

        $response = $client->getResponse();
        $this->assertJson($response->getContent());

        $data = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('errors', $data);
        $this->assertArrayHasKey('email', $data['errors']);
        $this->assertSame(
            'This value should not be blank.',
            $data['errors']['email']
        );
    }

    public function testRegisterFailWithExistingEmail(): void
    {
        $client = static::createClient();

        $container = self::getContainer();
        $em = $container->get(EntityManagerInterface::class);

        $this->connection = $em->getConnection();
        
        $this->connection->beginTransaction();

        $fixture = new UserFixtures(
            $container->get('security.user_password_hasher')
        );

        $fixture->load($em);

        $client->request(
            'POST',
            '/api/v1/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => 'user@example.com',
                'first_name' => 'user',
                'last_name' => 'user',
                'password' => 'test1234'
            ])
        );

        $this->assertResponseStatusCodeSame(409);

        $response = $client->getResponse();
        $this->assertJson($response->getContent());

        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('error', $data);
        $this->assertSame(
            'User already exists',
            $data['error']
        );
    }

    public function testCannotSetRoleDuringRegister(): void
    {
        $client = static::createClient();

        $container = self::getContainer();
        $em = $container->get(EntityManagerInterface::class);

        $this->connection = $em->getConnection();
        $this->connection->beginTransaction();

        $uniqueEmail = 'user_' . time() . '@example.com';
        $password = 'pass_' . time();
        $roles = ['ROLE_ADMIN'];

        $client->request(
            'POST',
            '/api/v1/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => $uniqueEmail,
                'first_name' => 'user',
                'last_name' => 'user',
                'password' => $password,
                'roles' => $roles
            ])
        );

        $this->assertResponseIsSuccessful();

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('token', $data);
        $this->assertArrayHasKey('user', $data);

        $user = self::getContainer()
            ->get(UserRepository::class)
            ->findOneBy(['email' => $uniqueEmail]);

        self::assertNotNull($user);
        self::assertNotEquals($roles, $user->getRoles());
    }

    public function testAuthenticatedUserCanAccessMeEndpoint(): void
    {
        $client = static::createClient();

        $container = self::getContainer();
        $em = $container->get(EntityManagerInterface::class);

        $this->connection = $em->getConnection();
        

        $fixture = new UserFixtures(
            $container->get('security.user_password_hasher')
        );

        $fixture->load($em);

        $client->request(
            'POST',
            '/api/v1/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => 'user@example.com',
                'password' => 'test1234'
            ])
        );

        $token = json_decode($client->getResponse()->getContent(), true)['token'];

        $client->request(
            'GET',
            '/api/v1/me',
            [],
            [],
            [
                'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            ],
        );

        self::assertResponseStatusCodeSame(200);
    }

    public function testCannotAccessWithInvalidToken(): void
    {
        $client = static::createClient();

        $container = self::getContainer();
        $em = $container->get(EntityManagerInterface::class);

        $this->connection = $em->getConnection();
        
        $client->request(
            'GET',
            '/api/v1/me',
            [],
            [],
            [
                'HTTP_AUTHORIZATION' => 'Bearer invalid.token',
            ],
        );

        self::assertResponseStatusCodeSame(401);
    }
}

