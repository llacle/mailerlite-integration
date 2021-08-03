<?php

namespace App\Http\Controllers;

use App\RemoteModels\Subscriber;
use Illuminate\Http\Request;


class SubscriberController extends Controller
{

  /**
   * index Subscriber
   * @param Request $request
   * @param Subscriber $subscriber
   * @return view
   */
    public function index(Request $request, Subscriber $subscriber)
    {
        return view('subscribers');
    }

    /**
     * list subscribers
     * @param Request $request
     * @param Subscriber $subscriber
     * @return void
     */
    public function list(Request $request, Subscriber $subscriber)
    {
        $search = $request->input('search');
        $columns = $request->input('columns');
        $order = $request->input('order');
        $offset = $request->input('start');
        $length = $request->input('length');

        $subscribers = $subscriber->listSubscribers($search, $columns, $order, $offset, $length);
        $totalRecords = $subscriber->getTotalRecords();

        return [
          'data' => $subscribers,
          'recordsTotal' => $totalRecords,
          'recordsFiltered' => $totalRecords,
        ];
    }

    /**
     * add subscriber
     * @param Request $request
     * @param Subscriber $subscriber
     * @return void
     */
    public function add(Request $request, Subscriber $subscriber)
    {
        $error = false;

        $validated = $request->validate([
            'email' => 'required|email',
            'name' => 'required',
            'country' => 'required',
        ]);

        // check if subscriber exists
        $response = $subscriber->findSubscriberEmail($validated['email']);

        // subscriber exist in system
        if (!isset($response->error)) {
            $error = "A subscriber with the same email exists.";
        } else if (isset($response->error) && $response->error->code == 123) {
            $response = $subscriber->addSubscriber([
                'email' => $validated['email'],
                'name' => $validated['name'],
                'country' => $validated['country'],
            ]);
        }

        if (isset($response->error)) {
            $error = $response->error->message;
        }

        return [
            'error' => $error ? true : false,
            'message' => $error ? $error : '',
        ];
    }

    /**
     * delete subscriber
     * @param Request $request
     * @param Subscriber $subscriber
     * @return void
     */
    public function delete(Request $request, Subscriber $subscriber)
    {
        $validated = $request->validate([
            'email' => 'required|email'
        ]);

        $email = $validated['email'];

        $response = $subscriber->deleteSubscriber($email);

        $error = false;

        if (isset($response->error)) {
          $error = $response->error->message;
        }

        return [
          'error' => $error ? true : false,
          'message' => $error ? $error : '',
        ];
    }

    /**
     * update subscriber
     * @param Request $request
     * @param Subscriber $subscriber
     * @return void
     */
    public function update(Request $request, Subscriber $subscriber)
    {
        $error = false;

        $validated = $request->validate([
            'email' => 'required|email',
            'name' => 'required',
            'country' => 'required',
        ]);

        $response = $subscriber->updateSubscriber($validated['email'], [
            'name' => $validated['name'],
            'country' => $validated['country']
        ]);

        if (isset($response->error)) {
            $error = $response->error->message;
        }

        return [
            'error' => $error ? true : false,
            'message' => $error ? $error : '',
        ];
    }
}
