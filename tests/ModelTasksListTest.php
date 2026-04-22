<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;

final class ModelTasksListTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
        require_once dirname(__DIR__) . '/application/core/model.php';
        require_once dirname(__DIR__) . '/application/class/DB.php';
        require_once dirname(__DIR__) . '/application/models/model_taskslist.php';
    }

    public function testCreateAndListTask(): void
    {
        $m = new \Model_TasksList();
        $ok = $m->set_data([
            'user_name' => 'Tester',
            'email'     => 't@example.com',
            'text'      => 'Hello world',
            'text_old'  => '',
            'statuses'  => [],
        ]);
        self::assertTrue($ok);

        $data = $m->get_data(['page' => 1, 'limit' => 10, 'order' => 'id', 'by' => 'DESC']);
        self::assertArrayHasKey('tasks', $data);
        self::assertNotEmpty($data['tasks']);
        $last = end($data['tasks']);
        self::assertSame('Tester', $last['user_name']);
        self::assertSame('t@example.com', $last['email']);
    }

    public function testInvalidEmailRejected(): void
    {
        $m = new \Model_TasksList();
        $ok = $m->set_data([
            'user_name' => 'Tester',
            'email'     => 'not-an-email',
            'text'      => 'x',
            'text_old'  => '',
            'statuses'  => [],
        ]);
        self::assertFalse($ok);
    }
}
