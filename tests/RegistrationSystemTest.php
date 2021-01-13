<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class RegistrationSystemTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
    }


    /**
     * @test
     */
    public function a_user_can_register()
    {
        $response = $this->register();
        $response->seeJson(['success' => true]);
        $this->assertCount(1, \App\Models\User::all());
    }

    /**
     * @test
     */
    public function a_user_can_log_in()
    {
        $this->register();
        $response = $this->login();
        $contents = json_decode($response->seeStatusCode(200)->response->getContent());
        $this->assertEquals(auth()->user()->email, 'test@mail.com');
        $response->seeJsonContains(['token' => $contents->token]);
    }

    /**
     * @test
     */
    public function no_access_without_token()
    {
        $this->get('/api')->assertResponseStatus(401);
    }

    /**
     * @test
     */
    public function a_user_can_access_with_token()
    {
        $this->register();
        $contents = json_decode($this->login()->response->getContent());
        $this->get('/api', [
            "Authorization" => "Bearer $contents->token",
        ])->assertResponseStatus(200);
    }

    /**
     * @test
     */
    public function a_user_can_logout()
    {
        $this->register();
        $contents = json_decode($this->login()->response->getContent());
        $this->post('/api/logout', [], [
            "Authorization" => "Bearer $contents->token",
        ]);
        $this->assertNull(auth()->user());
    }


}
