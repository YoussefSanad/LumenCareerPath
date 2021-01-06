<?php

use Laravel\Lumen\Testing\DatabaseMigrations;

class ApplicationsTest extends TestCase
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
    public function a_user_can_get_all_applications()
    {
        $factoryObjectsCount = 5;
        \App\Models\Application::factory()->count($factoryObjectsCount)->create();
        $payload = json_decode($this->get("/api/applications", $this->headers)->response->getContent())->payload;
        $this->assertCount($factoryObjectsCount, $payload);
    }

    /**
     * @test
     */
    public function a_user_can_get_an_application()
    {
        $factoryObjectsCount = 1;
        \App\Models\Application::factory()->count($factoryObjectsCount)->create();
        $payload = json_decode($this->get("/api/applications/1", $this->headers)->response->getContent())->payload;
        $this->assertEquals(1, $payload->id);
    }

    /**
     * @test
     */
    public function user_can_delete_an_application()
    {
        $factoryObjectsCount = 1;
        \App\Models\Application::factory()->count($factoryObjectsCount)->create();
        $this->delete("/api/applications/1", $this->headers)->seeStatusCode(200);
        self::assertCount(0, \App\Models\Application::all());
    }

    /**
     * @test
     */
    public function user_can_add_an_application()
    {
        $this->addApplication()->seeStatusCode(200);
        self::assertCount(1, \App\Models\Application::all());
        \Illuminate\Support\Facades\Storage::disk('CVs')->assertExists('cv.pdf');

    }

    private function addApplication()
    {
        \Illuminate\Support\Facades\Storage::fake('CVs');
        return $this->post('/api/applications', [
            'user_id' => $this->user->id,
            'job_post_id' => 1,
            'first_name' => 'test',
            'last_name' => 'test',
            'university_name' => 'test',
            'date_of_birth' => Carbon::now(),
            'email' => 'test@test.com',
            'notes' => 'test',
            'cv' => \Illuminate\Http\UploadedFile::fake()->image('cv.jpg')
        ], $this->headers);

    }


}
