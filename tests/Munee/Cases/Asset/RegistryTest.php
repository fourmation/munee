<?php
/**
 * Munee: Optimising Your Assets
 *
 * @copyright Cody Lundquist 2013
 * @license http://opensource.org/licenses/mit-license.php
 */

namespace Fourmation\Munee\Cases\Asset;

use \Fourmation\Munee\Asset\Registry;
use \Fourmation\Munee\ErrorException;
use \Fourmation\Munee\Mocks\MockAssetType;
use \Fourmation\Munee\Mocks\MockRequest;
use \PHPUnit\Framework\TestCase;

/**
 * Tests for the \Munee\asset\Registry Class
 *
 * @author Cody Lundquist
 */
class RegistryTest extends TestCase
{
    /**
     * Register some extensions for each test
     */
    protected function setUp(): void
    {
        Registry::register(array('foo', 'bar'), function ($Request) {
            return new MockAssetType($Request);
        });
    }

    /**
     * Remove the extensions after each test
     */
    protected function tearDown(): void
    {
        Registry::unRegister(array('foo', 'bar'));
    }

    /**
     * Check to make sure we get the correct AssetType based on the extension in the Mock Request
     */
    public function testGetAssetType()
    {

        $MockAssetType = Registry::getClass(new MockRequest());
        $CheckMockAssetType = new MockAssetType(new MockRequest());

        $this->assertSame(get_class($CheckMockAssetType), get_class($MockAssetType));
    }

    /**
     * Make sure we are getting ErrorExceptions when extensions are not supported
     */
    public function testExtensionNotSupported()
    {
        $this->expectException(ErrorException::class);
        Registry::getSupportedExtensions('nope');
    }

    /**
     * Lets see if we get an exception when an extension is not registered
     */
    public function testExtensionNotRegistered()
    {
        $MockRequest = new MockRequest();
        $MockRequest->ext = 'nope';
        $this->expectException(ErrorException::class);
        Registry::getClass($MockRequest);
    }

    /**
     * Check to make sure all extensions are supported when registered
     */
    public function testSupportedExtensions()
    {
        $checkExtensions = array('foo', 'bar');
        $supportedExtensions = Registry::getSupportedExtensions('bar');

        $this->assertSame($checkExtensions, $supportedExtensions);
    }

    /**
     * Make sure we can un-register extensions
     */
    public function testUnRegisterExtension()
    {
        Registry::unRegister('bar');

        $checkExtensions = array('foo');
        $supportedExtensions = Registry::getSupportedExtensions('foo');

        $this->assertSame($checkExtensions, $supportedExtensions);
    }
}