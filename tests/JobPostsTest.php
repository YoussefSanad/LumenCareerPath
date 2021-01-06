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
    public function a_user_can_get_all_job_posts()
    {
        $factoryObjectsCount = 5;
        \App\Models\JobPost::factory()->count($factoryObjectsCount)->create();
        $payload = json_decode($this->get("/api/job_posts", $this->headers)->response->getContent())->payload;
        $this->assertCount($factoryObjectsCount, $payload);
    }

    /**
     * @test
     */
    public function a_user_can_view_a_job_post()
    {
        $factoryObjectsCount = 1;
        \App\Models\JobPost::factory()->count($factoryObjectsCount)->create();
        $payload = json_decode(
            $this->get("/api/job_posts/1", $this->headers)->seeStatusCode(200)->response->getContent()
        )->payload;
        $this->assertEquals(1, $payload->id);
    }


    /**
     * @test
     */
    public function a_user_can_add_a_job_post_with_token()
    {
        $this->createJobPost()->assertResponseStatus(200);
        $this->assertCount(1, \App\Models\JobPost::all());
    }

    /**
     * @test
     */
    public function a_user_can_update_job_with_token()
    {
        \App\Models\JobPost::factory()->create();
        $this->updateJobPost()->assertResponseStatus(200);
        $this->assertCount(1, \App\Models\JobPost::all());
        $this->seeInDatabase('job_posts',[
            'title'                     => "Updated Title",
            'required_experience_level' => "Updated Years",
            'job_requirements'          => "Updated Requirements"]);
    }

    /**
     * @test
     */
    public function a_user_can_delete_job_with_token()
    {
        \App\Models\JobPost::factory()->create();
        $this->deleteJobPost()->assertResponseStatus(200);
        $this->assertCount(0, \App\Models\JobPost::all());
    }


    private function createJobPost()
    {
        return $this->post('/api/job_posts', [
            'user_id'                   => $this->user->id,
            'title'                     => "Test Title",
            'required_experience_level' => "Test Years",
            'job_requirements'          => "Test Requirements",
            'start_date'                => \Carbon\Carbon::now()->toString(),
            'end_date'                  => \Carbon\Carbon::now()->toString(),
        ], $this->headers);
    }

    private function updateJobPost()
    {
        $jobPost = \App\Models\JobPost::all()->first();
        return $this->put("/api/job_posts/$jobPost->id", [
            'user_id'                   => $this->user->id,
            'title'                     => "Updated Title",
            'required_experience_level' => "Updated Years",
            'job_requirements'          => "Updated Requirements",
            'start_date'                => \Carbon\Carbon::now()->toString(),
            'end_date'                  => \Carbon\Carbon::now()->toString(),
        ], $this->headers);
    }

    private function deleteJobPost()
    {
        $jobPost = \App\Models\JobPost::all()->first();
        return $this->delete("/api/job_posts/$jobPost->id", [], $this->headers);
    }

    private function getAllJobs()
    {
        return $this->get("/api/job_posts", $this->headers);
    }


}
