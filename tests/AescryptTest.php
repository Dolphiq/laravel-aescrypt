<?php

class AescryptTest extends AescryptTestCase
{
    /** @test */
    function attributes_defined_in_the_encrypts_property_should_be_encrypted()
    {
        $user = $this->createUser('John Doe', 'johndoe@example.com');

        $this->assertNotEquals('John Doe', $user->getOriginal('name'));
        $this->assertEquals('johndoe@example.com', $user->getOriginal('email'));
    }

    /** @test */
    function the_encrypted_value_will_be_prefixed()
    {
        $user = $this->createUser('John Doe', 'johndoe@example.com');

        $prefix = substr(
            $user->getOriginal('name'),
            0,
            strlen(config('aescrypt.prefix'))
        );

        $this->assertEquals('__AESCRYPT__:', $prefix);
    }

    /** @test */
    function a_custom_prefix_can_be_defined()
    {
        config()->set('aescrypt.prefix', 'my custom prefix');

        $user = $this->createUser('John Doe', 'johndoe@example.com');

        $prefix = substr(
            $user->getOriginal('name'),
            0,
            strlen(config('aescrypt.prefix'))
        );

        $this->assertEquals('my custom prefix', $prefix);
    }

    /** @test */
    function values_are_decrypted_automatically()
    {
        $user = $this->createUser('Jane Doe', 'janedoe@example.com');

        $this->assertEquals('Jane Doe', $user->getAttribute('name'));
        $this->assertEquals('janedoe@example.com', $user->getAttribute('email'));
    }

    /** @test */
    function encrypted_values_can_be_base64_encoded()
    {
        config()->set('aescrypt.base64_output', true);

        $user = $this->createUser('John Doe', 'johndoe@example.com');

        $base64 = str_replace(
            config('aescrypt.prefix'),
            '',
            $user->getOriginal('name')
        );

        $this->assertNotFalse(
            base64_decode($base64)
        );

        $this->assertEquals('John Doe', $user->decryptedAttribute($base64));
    }
}
