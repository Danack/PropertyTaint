<?php

namespace Danack\PropertyTaint\Tests;

use SimpleXMLElement;
use Danack\PropertyTaint\Plugin;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Psalm\Plugin\RegistrationInterface;

class PluginTest extends TestCase
{
    /**
     * @var ObjectProphecy
     */
    private $registration;

    public function setUp(): void
    {
        $this->registration = $this->prophesize(RegistrationInterface::class);
    }

    /**
     * @test
     * @return void
     */
    public function hasEntryPoint()
    {
        $this->expectNotToPerformAssertions();
        $plugin = new Plugin();
        $plugin($this->registration->reveal(), null);
    }

    /**
     * @test
     * @return void
     */
    public function acceptsConfig()
    {
        $this->expectNotToPerformAssertions();
        $plugin = new Plugin();
        $plugin($this->registration->reveal(), new SimpleXMLElement('<myConfig></myConfig>'));
    }

    public function providerValidCodeParse()
    {
        $contents = file_get_contents(__DIR__ . "/../demo/user_updater.php");
        $contents = substr($contents, 6);
        yield $contents;
    }

    /**
     * @dataProvider providerValidCodeParse
     *
     *
     */
    public function testValidCode(string $code): void
    {
        $this->fail("I have no idea how to run analyze that code.");
    }
}
