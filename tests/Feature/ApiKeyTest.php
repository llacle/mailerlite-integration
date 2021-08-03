<?php

namespace Tests\Feature;

use App\Models\ApiKey;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Crypt;
use Tests\TestCase;

class ApiKeyTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * A test to add an api key.
     *
     * @return void
     */
    public function test_add_new_api_key()
    {
        $response = $this->json('POST', '/apikey/add', [
            'api_key' => env('MAILERLITE_KEY')
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'error' => false,
            'message' => ''
        ]);
    }

    /**
     * A test to add more than one api key.
     *
     * @return void
     */
    public function test_duplicate_entry_api_key()
    {
        $response = $this->json('POST', '/apikey/add', [
            'api_key' => env('MAILERLITE_KEY')
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'error' => false,
            'message' => ''
        ]);

        $response = $this->json('POST', '/apikey/add', [
            'api_key' => env('MAILERLITE_KEY')
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'error' => true,
            'message' => 'A key already exists.'
        ]);
    }

    /**
     * A test to add a api key without the required parameter.
     *
     * @return void
     */
    public function test_missing_field_api_key()
    {
        $response = $this->json('POST', '/apikey/add', []);

        $response->assertStatus(422);
        $response->assertJson([
            'errors' => [
                'api_key' => ['The api key field is required.']
            ]
        ]);
    }

    /**
     * A test to add an invalid api key.
     *
     * @return void
     */
    public function test_bad_api_key()
    {
        $response = $this->json('POST', '/apikey/add', [
            'api_key' => 'bad_token'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'error' => true
        ]);
    }

    /**
     * A test to delete an api key.
     *
     * @return void
     */
    public function test_delete_api_key()
    {
        ApiKey::create(['apikey_value' => Crypt::encryptString(env('MAILERLITE_KEY'))]);

        $response = $this->json('DELETE', '/apikey');

        $response->assertStatus(200);
        $response->assertJson([
            'error' => false,
            'message' => ''
        ]);
    }
}
