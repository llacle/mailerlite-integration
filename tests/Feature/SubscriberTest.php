<?php

namespace Tests\Feature;

use App\Models\ApiKey;
use MailerLiteApi\MailerLite;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Crypt;
use Tests\TestCase;

class SubscriberTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * A test to add a subscriber.
     *
     * @return void
     */
    public function test_add_subscriber()
    {
        ApiKey::create(['apikey_value' => Crypt::encryptString(env('MAILERLITE_KEY'))]);

        $response = $this->json('POST', '/subscribers', [
            'email' => $this->faker->unique()->safeEmail(),
            'name' => $this->faker->name(),
            'country' => 'Fake Country'
        ]);

        $response->assertStatus(200);
    }

    /**
     * A test to update a subscriber.
     *
     * @return void
     */
    public function test_update_subscriber()
    {
        ApiKey::create(['apikey_value' => Crypt::encryptString(env('MAILERLITE_KEY'))]);

        $email = $this->faker->unique()->safeEmail();

        $response = $this->json('POST', '/subscribers', [
            'email' => $email,
            'name' => $this->faker->name(),
            'country' => 'Fake Country'
        ]);

        $response->assertStatus(200);

        $new_name = 'Updated Name';
        $new_country = 'Updated Country';

        $response = $this->json('PUT', '/subscribers', [
            'email' => $email,
            'name' => $new_name,
            'country' => $new_country
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'error' => false,
            'message' => ''
        ]);

        $api_key = ApiKey::whereNotNull('id')->first();
        $mailerlite = new MailerLite($api_key->apikey_value);

        $response = $mailerlite->subscribers()->find($email);
        $name_check = $response->name;

        $this->assertSame($name_check, $new_name);

        $country_check = '';

        foreach ($response->fields as $field) {
            if ($field->key == 'country') {
                $country_check = $field->value;
                break;
            }
        }

        $this->assertSame($country_check, $new_country);
    }

    /**
     * A test to delete a subscriber.
     *
     * @return void
     */
    public function test_delete_subscriber()
    {
        ApiKey::create(['apikey_value' => Crypt::encryptString(env('MAILERLITE_KEY'))]);

        $email = $this->faker->unique()->safeEmail();

        $response = $this->json('POST', '/subscribers', [
            'email' => $email,
            'name' => $this->faker->name(),
            'country' => 'Fake Country'
        ]);

        $response->assertStatus(200);

        $response = $this->json('DELETE', '/subscribers', [
            'email' => $email
        ]);

        $response->assertStatus(200);

        $response->assertJson([
            'error' => false,
            'message' => ''
        ]);
    }

    /**
     * A test to check if you can add duplicate subscribers.
     *
     * @return void
     */
    public function test_duplicate_subscriber()
    {
        ApiKey::create(['apikey_value' => Crypt::encryptString(env('MAILERLITE_KEY'))]);

        $email = $this->faker->unique()->safeEmail();
        $name  = $this->faker->name();

        $response = $this->json('POST', '/subscribers', [
            'email' => $email,
            'name' => $name,
            'country' => 'Fake Country'
        ]);

        $response->assertStatus(200);

        $response = $this->json('POST', '/subscribers', [
            'email' => $email,
            'name' => $name,
            'country' => 'Fake Country'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'error' => true,
            'message' => 'A subscriber with the same email exists.'
        ]);
    }
}
