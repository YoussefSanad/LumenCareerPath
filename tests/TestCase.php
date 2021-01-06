<?php

use Laravel\Lumen\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__.'/../bootstrap/app.php';
    }

    protected function register()
    {
        return $this->post('/api/register', [
            'name' => 'test',
            'email' => 'test@mail.com',
            'password' => 'testtest',
        ]);
    }


    protected function login()
    {
        return $this->post('/api/login', [
            'email' => 'test@mail.com',
            'password' => 'testtest',
        ]);
    }
}
