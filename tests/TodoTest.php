<?php

namespace App\Tests;

use App\Entity\User;
use App\Repository\TodoRepository;
use App\Repository\UserRepository;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TodoTest extends WebTestCase
{
    use RefreshDatabaseTrait;

    private const TODO_ID = 'b9c7e8c1-822a-4550-af44-85e6c8ae36cd';

    /** @var JWTTokenManagerInterface $jwtManager */
    private $jwtManager;

    /** @var UserRepository $userRepository */
    private $userRepository;

    /** @var \Symfony\Bundle\FrameworkBundle\KernelBrowser $client */
    private $client;

    /** @var TodoRepository $todoRepository */
    private $todoRepository;

    protected function setUp(): void
    {
        $this->client = self::createClient();

        $container = static::getContainer();
        $this->jwtManager = $container->get(JWTTokenManagerInterface::class);
        $this->userRepository = $container->get(UserRepository::class);
        $this->todoRepository = $container->get(TodoRepository::class);
    }

    public function testCreateTodo(): void
    {
        /** @var User $user */
        $user = $this->userRepository->findOneByUsername('User');
        $token = $this->jwtManager->createFromPayload($user, ['username' => $user->getUserIdentifier()]);

        $this->client->request(
            'POST',
            '/todos',
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer ' . $token],
            json_encode([
                'id' => self::TODO_ID,
                'text' => 'Text'
            ], JSON_THROW_ON_ERROR),
        );

        $todo = $this->todoRepository->find(self::TODO_ID);
        $this->assertNotNull($todo);
        $this->assertNull($todo->getCompletedAt());
        $this->assertEquals('Text', $todo->getText());
        $this->assertEquals('User', $todo->getUser()->getUserIdentifier());
    }

    public function testGetTodos(): void
    {
        /** @var User $user */
        $user = $this->userRepository->findOneByUsername('User');
        $token = $this->jwtManager->createFromPayload($user, ['username' => $user->getUserIdentifier()]);

        $this->todoRepository->add(self::TODO_ID, 'Text', $user);

        $this->client->request(
            'GET',
            '/todos',
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer ' . $token],
        );

        $content = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertCount(1, $content);
        $this->assertEquals(self::TODO_ID, $content[0]['id']);
        $this->assertEquals('Text', $content[0]['text']);
    }

    public function testEditTodo(): void
    {
        /** @var User $user */
        $user = $this->userRepository->findOneByUsername('User');
        $token = $this->jwtManager->createFromPayload($user, ['username' => $user->getUserIdentifier()]);

        $this->todoRepository->add(self::TODO_ID, 'Text', $user);
        $this->assertCount(1, $this->todoRepository->getTodosByUser($user));

        $this->client->request(
            'PATCH',
            sprintf('/todos/%s', self::TODO_ID),
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer ' . $token],
            json_encode([
                'text' => 'Edited',
                'completed' => true
            ], JSON_THROW_ON_ERROR),
        );

        $todo = $this->todoRepository->find(self::TODO_ID);
        $this->assertNotNull($todo);
        $this->assertEquals('Edited', $todo->getText());
        $this->assertNotNull($todo->getCompletedAt());
    }

    public function testDeleteTodo(): void
    {
        /** @var User $user */
        $user = $this->userRepository->findOneByUsername('User');
        $token = $this->jwtManager->createFromPayload($user, ['username' => $user->getUserIdentifier()]);

        $this->todoRepository->add(self::TODO_ID, 'Text', $user);
        $this->assertNotNull($this->todoRepository->find(self::TODO_ID));

        $this->client->request(
            'DELETE',
            sprintf('/todos/%s', self::TODO_ID),
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer ' . $token],
        );

        $this->assertNull($this->todoRepository->find(self::TODO_ID));
    }
}
