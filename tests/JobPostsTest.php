<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class JobPostsTest extends TestCase
{
    use DatabaseMigrations;


    private $headers;
    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->register();
        $token = json_decode($this->login()->response->getContent())->token;
        $this->user = auth()->user();
        $this->headers = [
            "Content-Type" => "multipart/form-data",
            "Authorization" => "Bearer $token",
        ];
    }


    /**
     * @test
     */
    public function a_user_can_add_a_job_post_with_token()
    {
        $this->post('/api/job_posts', [

        ], $this->headers);
    }


    private function register()
    {
        return $this->post('/api/register', [
            'name' => 'test',
            'email' => 'test@mail.com',
            'password' => 'testtest',
        ]);
    }


    public function login()
    {
        return $this->post('/api/login', [
            'email' => 'test@mail.com',
            'password' => 'testtest',
        ]);
    }


}
