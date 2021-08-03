<?php

namespace App\Models;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiKey extends Model
{
    use HasFactory;

    protected $fillable = [
      'apikey_value'
    ];

    /**
     * decrypt apikey_value
     *
     * @param String $value
     * @return String
     */
    public function getApiKeyValueAttribute($value)
    {
      try {
        return Crypt::decryptString($value);
      } catch (DecryptException $e) {
        //
      }
    }
}
