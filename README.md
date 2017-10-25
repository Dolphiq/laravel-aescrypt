# Eloquent Encryption/Decryption for Laravel 5

## READ THIS FIRST

This package uses the Openssl AES ECB encrypth method with a key length of 128bit.
The result is default compatible with the AES_ENCRYPT functions and can be reproduces by running the query:
```
set @salt = SUBSTRING(SHA2('My secret pass phrase',256), 1, 16);
SELECT @salt as salt, AES_ENCRYPT('text to encrypt', @salt);

```

With the AESCRYPT_BASE64_OUTPUT setting on true, the query will be:

```
set @salt = SUBSTRING(SHA2('My secret pass phrase',256), 1, 16);
SELECT @salt as salt, TO_BASE64(AES_ENCRYPT('text to encrypt', @salt));

```

The package uses a blank IV (just like Mysql) as default. The result hash is the same with the same input text under this conditions.
This is less save than using a random IV and NOT recommended for use with data with many repetitions or higly secure data but give one big advantage:
You can search the database rather fast for matching text (e.g. email address) without decrypting all records.


Note:
Encrypted values are usually longer than plain text values.  Sometimes much longer.  You
may find that the column widths in your database tables need to be extended to store the
encrypted values. The package stores the data as Raw hash by default this can be switched to ``` AESCRYPT_BASE64_OUTPUT = true ``` in the .env file

If you are using the default encrypthion method, you should change your database columns to VARBINARY (length of 300 will be safe for former 256 varchar),
If you turn on AES_BASE64_OUTPUT, you can use VARCHAR (length: +/- (original text + 13 + 16) * 1.3 or TEXT or LONGTEXT.

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

## Installation

This package can be installed via Composer by running following command:

```
    composer require dolphiq/laravel-aescrypt
```

Once `composer` has finished, then add the service provider in Laravel to the `providers` array in your
application's `config/app.php` file:

```php
    'providers' => [
        ...
        Dolphiq\Aescrypt\AescryptServiceProvider::class,
    ],
```

In Lumen, you can add it to `bootstrap/app.php` file:
```php
// Aescrypt provider
$app->register(\Dolphiq\Aescrypt\AescryptServiceProvider::class);
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

## Contributors

This is based on the original Darren Taylor's Laravel 4 "elocrypt" package With changes from Delatbabel.

Developers in our team:
Johan Zandstra - info@dolphiq.nl
Brought to you by [Dolphiq](https://dolphiq.nl)

