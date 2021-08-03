<?php

namespace App\Http\Controllers;

use App\Models\ApiKey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class ApiKeyController extends Controller
{

  /**
   * index ApiKey
   *
   * @param Request $request
   * @return view
   */
  public function index(Request $request)
  {
      return view('apikey', ['apikey_object' => ApiKey::whereNotNull('id')->first()]);
  }

  /**
   * add an api key
   *
   * @param Request $request
   * @return void
   */
  public function add(Request $request)
  {
      $request->validate([
        'api_key' => 'required'
      ]);

      $api_key = $request->get('api_key');

      if(ApiKey::all()->count() > 0)
      {
          return [
              'error' => true,
              'message' => 'A key already exists.'
          ];
      }

      $content = '';

      try
      {
          $endpoint = "https://api.mailerlite.com/api/v2/me";
          $client = new \GuzzleHttp\Client();

          $response = $client->request('GET', $endpoint, [
              'headers' => [
                  'X-MailerLite-ApiKey' => $api_key
              ]
          ]);

          $statusCode = $response->getStatusCode();
      } catch(\GuzzleHttp\Exception\ClientException $e) {
          $statusCode = $e->getResponse()->getStatusCode();
          $content = $e->getResponse()->getBody();
      } catch(\GuzzleHttp\Exception\RequestException $e) {
          $statusCode = $e->getResponse()->getStatusCode();
          $content = $e->getResponse()->getBody();
      }

      if ($statusCode != 200) {
        $msg = json_decode($content);

        if (isset($msg->error)) {
          return [
              'error' => true,
              'message' => $msg->error->code.': '.$msg->error->message
          ];
        }
      }

      ApiKey::create(['apikey_value' => Crypt::encryptString($api_key)]);

      return [
          'error' => false,
          'message' => ''
      ];
  }

  /**
   * delete an api key
   *
   * @param Request $request
   * @return void
   */
  public function delete(Request $request)
  {
      ApiKey::whereNotNull('id')->delete();

      return [
          'error' => false,
          'message' => ''
      ];
  }
}
