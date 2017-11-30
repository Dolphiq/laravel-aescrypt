<?php

use Orchestra\Testbench\TestCase;

class AescryptTestCase extends TestCase
{
    /**
     * Load the package service provider.
     *
     * @param $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return ['Dolphiq\Aescrypt\AescryptServiceProvider'];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }

    /**
     * Set up the test case.
     *
     * @return void
     */
    function setUp()
    {
        parent::setUp();

        $this->loadLaravelMigrations(['--database' => 'testbench']);
    }

    /**
     * Create a test user.
     *
     * @param $name
     * @param $email
     * @return User
     */
    public function createUser($name, $email)
    {
        $password = bcrypt('test');

        return User::create(compact('name', 'email', 'password'));
    }
}
