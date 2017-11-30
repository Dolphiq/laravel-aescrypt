<?php

use Illuminate\Database\Eloquent\Model;
use Dolphiq\Aescrypt\Aescrypt;

class User extends Model
{
    use Aescrypt;

    /**
     * The attributes that are fillable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password'
    ];

    /**
     * The attributes that should be encrypted on save.
     *
     * @var array
     */
    protected $encrypts = [
        'name'
    ];
}
