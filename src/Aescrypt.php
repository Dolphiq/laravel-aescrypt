<?php
/**
 * Trait Aescrypt.
 */
namespace Dolphiq\Aescrypt;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Contracts\Encryption\EncryptException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;

/**
 * Trait Aescrypt.
 * Based on delatbabel/elocryptfive
 */
trait Aescrypt
{
    //
    // Methods below here are native to the trait.
    //



    public function aes_encrypt($val, $key)
    {
        return openssl_encrypt($val, 'aes-128-ecb', $key);
    }

    public function aes_decrypt($val, $key)
    {
        return openssl_decrypt($val, 'aes-128-ecb', $key);
     }

    protected function getAescryptKey()
    {
        if(!Config::has('aescrypt.aeskey')) throw new \Exception('The .env value AESCRYPT_AESKEY has to be set!!');
        return substr(hash('sha256', Config::get('aescrypt.aeskey')), 0, 16);
    }

    /**
     * Get the configuration setting for the prefix used to determine if a string is encrypted.
     *
     * @return string
     */
    protected function getAesryptPrefix()
    {
        return Config::has('aescrypt.prefix') ? Config::get('aescrypt.prefix') : '__AESCRYPT__:';
    }

    /**
     * Determine whether an attribute should be encrypted.
     *
     * @param string $key
     *
     * @return bool
     */
    protected function shouldEncrypt($key)
    {
        $encrypt = isset($this->encrypts) ? $this->encrypts : $this->encryptable;

        return in_array($key, $encrypt);
    }

    /**
     * Determine whether a string has already been encrypted.
     *
     * @param mixed $value
     *
     * @return bool
     */
    protected function isEncrypted($value)
    {
        return strpos((string) $value, $this->getAesryptPrefix()) === 0;
    }

    /**
     * Return the encrypted value of an attribute's value.
     *
     * This has been exposed as a public method because it is of some
     * use when searching.
     *
     * @param string $value
     *
     * @return string
     */
    public function encryptedAttribute($value)
    {
        return $this->getAesryptPrefix() . $this->aes_encrypt($value, $this->getAescryptKey());
    }

    /**
     * Return the decrypted value of an attribute's encrypted value.
     *
     * This has been exposed as a public method because it is of some
     * use when searching.
     *
     * @param string $value
     *
     * @return string
     */
    public function decryptedAttribute($value)
    {
        return $this->aes_decrypt(str_replace($this->getAesryptPrefix(), '', $value), $this->getAescryptKey());
    }

    /**
     * Return an encrypted version of the value.
     *
     * @param string $value
     *
     * @return string
     */
    public function encryptValue($value) {
        return $this->getAesryptPrefix() . $this->aes_encrypt($value, $this->getAescryptKey());
    }

    /**
     * Encrypt a stored attribute.
     *
     * @param string $key
     *
     * @return void
     */
    protected function doEncryptAttribute($key)
    {
        if ($this->shouldEncrypt($key) && ! $this->isEncrypted($this->attributes[$key])) {
            try {
                $this->attributes[$key] = $this->encryptedAttribute($this->attributes[$key]);
            } catch (EncryptException $e) {
            }
        }
    }

    /**
     * Decrypt an attribute if required.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return mixed
     */
    protected function doDecryptAttribute($key, $value)
    {
        // print_r($this);
        if ($this->shouldEncrypt($key) && $this->isEncrypted($value)) {
            try {
                return $this->decryptedAttribute($value);
                // return $value;
            } catch (DecryptException $e) {
            }
        }

        return $value;
    }



    /**
     * Decrypt each attribute in the array as required.
     *
     * @param array $attributes
     *
     * @return array
     */
    public function doDecryptAttributes($attributes)
    {
        foreach ($attributes as $key => $value) {
            $attributes[$key] = $this->doDecryptAttribute($key, $value);
        }

        return $attributes;
    }

    //
    // Methods below here override methods within the base Laravel/Illuminate/Eloquent
    // model class and may need adjusting for later releases of Laravel.
    //


    public function getDirty()
    {
        $dirty = [];

        foreach ($this->attributes as $key => $value) {
            if (! $this->originalIsEquivalent($key, $value)) {
                $dirty[$key] = $value;
            }
        }

        return $dirty;
    }


    /**
     * Set a given attribute on the model.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return void
     */
    public function setAttribute($key, $value)
    {
        parent::setAttribute($key, $value);
        $this->doEncryptAttribute($key);
    }


    /**
     * Get an attribute from the $attributes array.
     *
     * @param string $key
     *
     * @return mixed
     */
    protected function getAttributeFromArray($key)
    {
        return $this->doDecryptAttribute($key, parent::getAttributeFromArray($key));
    }

    /**
     * Get an attribute array of all arrayable attributes.
     *
     * @return array
     */
    protected function getArrayableAttributes()
    {
        return $this->doDecryptAttributes(parent::getArrayableAttributes());
    }

    /**
     * Get all of the current attributes on the model.
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->doDecryptAttributes(parent::getAttributes());
    }
}
