# Eloquent Encryption/Decryption for Laravel 5

## READ THIS FIRST

Encrypted values are usually longer than plain text values.  Sometimes much longer.  You
may find that the column widths in your database tables need to be extended to store the
encrypted values.

If you are encrypting long strings such as JSON blobs then the encrypted values may be
longer than a VARCHAR field can support, and you may need to extend your column types to
TEXT or LONGTEXT.

## What Does This Do?

This encrypts and decrypts columns stored in database tables in Laravel applications
transparently, by encrypting data as it is stored in the model attributes and decrypting
data as it is recalled from the model attributes.

All data that is encrypted is prefixed with a tag (default `__AESCRYPT__:`) so that
encrypted data can be easily identified.

This supports columns that store either encrypted or non-encrypted data to make migration
easier.  Data can be read from columns correctly regardless of whether it is encrypted or
not but will be automatically encrypted when it is saved back into those columns.

## Requirements and Recommendations

* Laravel 5.5 LTS (untested other versions)
* PHP > 7
* PHP [openssl extension](http://php.net/manual/en/book.openssl.php).
* A working OpenSSL implementation on your OS.  OpenSSL comes pre-built with most Linux distributions and other forms of Unix such as *BSD.  There may or may not be a working OpenSSL implementation on a Windows system depending on how your LA?P stack was built.  I cannot offer support for installing or using ElocryptFive on systems that do not have an OpenSSL library.

## Contributors

This is based on the original Darren Taylor's Laravel 4 "elocrypt" package With changes from Delatbabel

## Installation

This package can be installed via Composer by running following command:

```
    composer require dolphiq/laravel-aescrypt
```

Once `composer` has finished, then add the service provider to the `providers` array in your
application's `config/app.php` file:

```php
    'providers' => [
        ...
        Dolphiq\Aescrypt\AescryptServiceProvider::class,
    ],
```


## Configuration

Publish the config file with:

```
    php artisan vendor:publish --provider='Dolphiq\Aescrypt\AescryptServiceProvider'
```

Than you have to add an encryption key`.env` config file:

```
    AESCRYPT_AESKEY=xxxx
```
## Usage

Simply reference the Aescrypt trait in any Eloquent Model you wish to apply encryption to and define
an `$encrypts` array containing a list of the attributes to encrypt.

For example:

```php
    use Dolphiq\Aescrypt\Aescrypt;

    class User extends Eloquent {

        use Aescrypt;

        /**
         * The attributes that should be encrypted on save.
         *
         * @var array
         */
        protected $encrypts = [
            'username', 'email'
        ];
    }
```

