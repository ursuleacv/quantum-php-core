<?php

namespace Quantum\Test\Unit;

use Mockery;
use PHPUnit\Framework\TestCase;
use Quantum\Libraries\Config\Config;

class ConfigTest extends TestCase
{

    private $loader;

    private $configData = [
        'langs' => ['en', 'es'],
        'lang_default' => 'en',
        'debug' => 'DEBUG',
        'test' => 'Testing'
    ];

    private $otherConfigData = [
        'more' => 'info',
        'preview' => 'yes',
    ];

    public function setUp(): void
    {
        $this->loader = Mockery::mock('Quantum\Loader\Loader');
    }

    public function tearDown(): void
    {
        Mockery::close();
    }

    public function testConfigLoad()
    {
        $this->assertEmpty(Config::getAll());

        $this->loader->shouldReceive('load')
            ->andReturn($this->configData);

        Config::load($this->loader);

        $this->assertNotEmpty(Config::getAll());

    }

    public function testConfigImport()
    {
        $this->loader->shouldReceive('load')
            ->andReturn($this->otherConfigData);

        Config::import($this->loader, 'other');

        $this->assertEquals('info', Config::get('other.more'));
    }

    public function testConfigHas()
    {
        $this->assertFalse(Config::has('foo'));

        $this->assertTrue(Config::has('test'));
    }

    public function testConfigGet()
    {
        $this->assertEquals('Testing', Config::get('test'));

        $this->assertEquals('Default Value', Config::get('not-exists', 'Default Value'));

        $this->assertNull(Config::get('not-exists'));

    }

    public function testConfigSet()
    {
        $this->assertNull(Config::get('new-value'));

        Config::set('new-value', 'New Value');

        $this->assertTrue(Config::has('new-value'));

        $this->assertEquals('New Value', Config::get('new-value'));

        Config::set('other.nested', 'Nested Value');

        $this->assertTrue(Config::has('other.nested'));

        $this->assertEquals('Nested Value', Config::get('other.nested'));
    }

    public function testConfigRemove()
    {
        $this->assertNotNull(Config::get('test'));

        Config::remove('test');

        $this->assertFalse(Config::has('test'));

        $this->assertNull(Config::get('test'));
    }


}