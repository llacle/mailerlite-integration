<?php

namespace App\RemoteModels;

use App\Models\ApiKey;
use Carbon\Carbon;
use MailerLiteApi\MailerLite;

class Subscriber
{
    private $mailerlite;
    private $totalRecords;
    private $subscribers;

    public function __construct()
    {
        $api_key = ApiKey::whereNotNull('id')->first();
        $this->mailerlite = new MailerLite($api_key->apikey_value);
        $this->subscribers = $this->mailerlite->subscribers();
    }

    /**
     * list or search subscribers
     *
     * @param String $search
     * @param String $columns
     * @param array $order
     * @param int $offset
     * @param int $length
     * @return array
     */
    public function listSubscribers($search, $columns, $order, $offset, $length)
    {
        $this->subscribers->limit($length)->offset($offset);

        foreach ($order as $order_item) {
          $this->subscribers->orderBy($columns[(int)$order_item['column']]['data'], $order_item['dir']);
        }

        if (isset($search['value'])) {
            $subs = $this->subscribers->search($search['value']);
            $this->totalRecords = count($subs);
        } else {
          $subs = $this->subscribers->get(['name', 'country'])->toArray();
          $this->totalRecords = $this->subscribers->count()->count;
        }

      $data = [];

      foreach ($subs as $subscriber) {
          $data[] = [
              'id' => $subscriber->id,
              'name' => $subscriber->name,
              'email' => $subscriber->email,
              'country' => $this->searchFieldValue($subscriber->fields, 'country'),
              'subscribed_date' => Carbon::parse($subscriber->date_created)->format('d/m/Y'),
              'subscribed_time' => Carbon::parse($subscriber->date_created)->format('H:i')
          ];
      }

      return $data;
    }

    /**
     * get total records of subscribers
     *
     * @return int
     */
    public function getTotalRecords()
    {
        return $this->totalRecords;
    }

    /**
     * delete a subscriber
     *
     * @param String $email
     * @return object
     */
    public function deleteSubscriber($email)
    {
        return $this->subscribers->delete($email);
    }

    /**
     * find a subscriber by email
     *
     * @param String $email
     * @return object
     */
    public function findSubscriberEmail($email)
    {
        return $this->subscribers->find($email);
    }

    /**
     * add a subscriber
     *
     * @param array $data
     * @return object
     */
    public function addSubscriber($data)
    {
        return $this->subscribers->create([
            'email' => $data['email'],
            'name' => $data['name'],
            'fields' => [
                'country' => $data['country']
            ]
        ]);
    }

    /**
     * update a subscriber by email
     *
     * @param String $email
     * @param array $data
     * @return object
     */
    public function updateSubscriber($email, $data)
    {
        return $this->subscribers->update($email, [
            'name' => $data['name'],
            'fields' => [
                'country' => $data['country']
            ]
        ]);
    }

    /**
     * a function to get the value of a specific
     * key inside an array
     *
     * @param array subFields
     * @param String $fieldName
     * @return String
     */
    private function searchFieldValue($subFields, $fieldName)
    {
        foreach ($subFields as $field) {
            if ($field->key == $fieldName) {
                return $field->value;
            }
        }

        return '';
    }
}
