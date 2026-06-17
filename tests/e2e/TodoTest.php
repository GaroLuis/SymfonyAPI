<?php declare(strict_types=1);

namespace App\Tests\e2e;

use App\Todo\Domain\Todo;
use App\Todo\Domain\TodoRepositoryInterface;
use App\User\Data\UserEntityMapper;
use App\User\Domain\UserRepositoryInterface;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TodoTest extends WebTestCase
{
    use RefreshDatabaseTrait;

    private const TODO_ID = 'b9c7e8c1-822a-4550-af44-85e6c8ae36cd';
    private const USERNAME = 'User';

    /** @var JWTTokenManagerInterface $jwtManager */
    private $jwtManager;

    /** @var UserRepositoryInterface $userRepository */
    private $userRepository;

    /** @var \Symfony\Bundle\FrameworkBundle\KernelBrowser $client */
    private $client;

    /** @var TodoRepositoryInterface $todoRepository */
    private $todoRepository;

    protected function setUp(): void
    {
        $this->client = self::createClient();

        $container = static::getContainer();
        $this->jwtManager = $container->get(JWTTokenManagerInterface::class);
        $this->userRepository = $container->get(UserRepositoryInterface::class);
        $this->todoRepository = $container->get(TodoRepositoryInterface::class);
    }

    public function testCreateTodo(): void
    {
        $user = $this->userRepository->findOneBy(['username' => self::USERNAME]);
        $token = $this->jwtManager->createFromPayload($user, ['username' => $user->getUserIdentifier()]);

        $this->client->request(
            'POST',
            '/todos',
            ['id' => self::TODO_ID, 'text' => 'Text'],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer ' . $token],
        );

        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());

        $todo = $this->todoRepository->getById(self::TODO_ID);
        $this->assertNotNull($todo);
        $this->assertNull($todo->getCompletedAt());
        $this->assertEquals('Text', $todo->getText());
    }

    public function testGetTodos(): void
    {
        $user = $this->userRepository->findOneBy(['username' => self::USERNAME]);
        $token = $this->jwtManager->createFromPayload($user, ['username' => $user->getUserIdentifier()]);

        $domainUser = UserEntityMapper::map($user);

        $todo = new Todo();
        $todo->setId(self::TODO_ID)->setText('Text')->setUser($domainUser);
        $this->todoRepository->add($todo);

        $this->client->request(
            'GET',
            '/todos',
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer ' . $token],
        );

        $response = $this->client->getResponse();
        $content = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertEquals(200, $response->getStatusCode());

        $this->assertCount(1, $content);
        $this->assertEquals(self::TODO_ID, $content[0]['id']);
        $this->assertEquals('Text', $content[0]['text']);
    }

    public function testEditTodo(): void
    {
        $user = $this->userRepository->findOneBy(['username' => self::USERNAME]);
        $token = $this->jwtManager->createFromPayload($user, ['username' => $user->getUserIdentifier()]);

        $domainUser = UserEntityMapper::map($user);

        $todo = new Todo();
        $todo->setId(self::TODO_ID)->setText('Text')->setUser($domainUser);
        $this->todoRepository->add($todo);

        $this->assertCount(1, $this->todoRepository->getTodosByUser($domainUser));

        $this->client->request(
            'PATCH',
            sprintf('/todos/%s', self::TODO_ID),
            ['text' => 'Edited', 'completed' => true],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer ' . $token],
        );

        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());

        $todo = $this->todoRepository->getById(self::TODO_ID);
        $this->assertNotNull($todo);
        $this->assertNotNull($todo->getCompletedAt());
        $this->assertEquals('Edited', $todo->getText());
    }

    public function testDeleteTodo(): void
    {
        $user = $this->userRepository->findOneBy(['username' => self::USERNAME]);
        $token = $this->jwtManager->createFromPayload($user, ['username' => $user->getUserIdentifier()]);

        $domainUser = UserEntityMapper::map($user);

        $todo = new Todo();
        $todo
            ->setId(self::TODO_ID)
            ->setText('Text')
            ->setUser($domainUser)
        ;

        $this->todoRepository->add($todo);
        $this->assertNotNull($this->todoRepository->getById(self::TODO_ID));

        $this->client->request(
            'DELETE',
            sprintf('/todos/%s', self::TODO_ID),
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer ' . $token],
        );

        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());

        $this->assertNull($this->todoRepository->getById(self::TODO_ID));
    }
}
