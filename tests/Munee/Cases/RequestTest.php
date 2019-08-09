<?php
/**
 * Munee: Optimising Your Assets
 *
 * @copyright Cody Lundquist 2013
 * @license http://opensource.org/licenses/mit-license.php
 */

namespace Fourmation\Munee\Cases;

use \Fourmation\Munee\ErrorException;
use \Fourmation\Munee\Request;
use \Fourmation\Munee\Utils;
use \PHPUnit\Framework\TestCase;

/**
 * Tests for the \Munee\Request Class
 *
 * @author Cody Lundquist
 */
class RequestTest extends TestCase
{
    /**
     * Set Up
     *
     * Create some tmp asset files
     */
    protected function setUp(): void
    {
        $jsDir = WEBROOT . DS . 'js';
        Utils::createDir($jsDir);

        file_put_contents($jsDir . DS . 'foo.js', '//Temp foo.js File');
        file_put_contents($jsDir . DS . 'bar.js', '//Temp bar.js File');
    }

    /**
     * Tear Down
     *
     * Remove the tmp asset files
     */
    protected function tearDown(): void
    {
        Utils::removeDir(WEBROOT . DS . 'js');
    }

    /**
     * Make sure there is at least a files query string parameter
     */
    public function testNoFilesQueryStringParam()
    {
        $Request = new Request();

        $this->expectException(ErrorException::class);
        $Request->init();
    }

    /**
     * Constructor Test
     */
    public function testConstructor()
    {
        $Request = new Request([ 'foo' => 'bar' ]);
        $this->assertSame([ 'foo' => 'bar' ], $Request->options);
    }

    /**
     * Make sure files are parsed properly and the extension is set
     */
    public function testInit()
    {
        $_GET = [ 'files' => '/js/foo.js,/js/bar.js' ];

        $Request = new Request();

        $Request->init();

        $this->assertSame('js', $Request->ext);
        $this->assertSame([ WEBROOT . '/js/foo.js', WEBROOT . '/js/bar.js' ], $Request->files);
    }

    /**
     * Make sure all passed in files can be handled by the asset type class
     */
    public function testExtensionNotSupported()
    {
        $_GET = [ 'files' => '/js/foo.jpg,/js/bar.js' ];
        
        $Request = new Request();

        $this->expectException(ErrorException::class);
        $Request->init();
    }

    /**
     * Make sure they can not go above webroot when requesting a file
     */
    public function testGoingAboveWebroot()
    {
        $_GET = [ 'files' => '/../..././js/bad.js,/js/bar.js' ];

        $Request = new Request();

        $Request->init();
        $this->assertSame([ WEBROOT . '/js/bad.js', WEBROOT . '/js/bar.js' ], $Request->files);
    }

    /**
     * Make legacy code is still being supported
     */
    public function testLegacyCode()
    {
        $_GET = [ 'files' => '/minify/js/foo.js' ];

        $Request = new Request();

        $Request->init();

        $this->assertSame([ WEBROOT . '/js/foo.js' ], $Request->files);
        $this->assertSame([ 'minify' => 'true' ], $Request->getRawParams());
    }

    /**
     * Make sure you are getting the correct raw parameters
     */
    public function testGetRawParams()
    {
        $_GET = [
            'files' => '/js/foo.js,/js/bar.js',
            'minify' => 'true',
            'notAllowedParam' => 'foo',
        ];

        $Request = new Request();

        $Request->init();
        $rawParams = $Request->getRawParams();
        $this->assertSame([ 'minify' => 'true', 'notAllowedParam' => 'foo' ], $rawParams);
    }

    /**
     * Make sure the Parameter Parser is doing it's job correctly
     */
    public function testParseParams()
    {
        $_GET = [
            'files' => '/js/foo.js,/js/bar.js',
            'foo' => 'true',
            'b' => 'ba[42]',
            'notAllowedParam' => 'foo',
        ];

        $Request = new Request();

        $Request->init();

        $this->assertEquals([], $Request->params);

        $allowedParams = [
            'foo' => [
                'alias' => 'f',
                'default' => 'false',
                'cast' => 'boolean'
            ],
            'bar' => [
                'alias' => 'b',
                'arguments' => [
                    'baz' => [
                        'alias' => 'ba',
                        'regex' => '\d+',
                        'cast' => 'integer',
                        'default' => 24,
                    ],
                ]
            ],
            'qux' => [ 'default' => 'no' ],
        ];

        $Request->parseParams($allowedParams);
        $assertedParams = [
            'foo' => true,
            'bar' => [ 'baz' => 42 ],
            'qux' => 'no',
        ];

        $this->assertSame($assertedParams, $Request->params);
    }

    /**
     * Make sure the param validation is working properly
     */
    public function testWrongParamValue()
    {
        $_GET = [
            'files' => '/js/foo.js',
            'foo' => 'not good',
        ];

        $Request = new Request();

        $Request->init();

        $allowedParams = [
            'foo' => [
                'alias' => 'f',
                'regex' => 'true|false',
                'default' => 'false',
                'cast' => 'boolean'
            ]
        ];

        $this->expectException(ErrorException::class);
        $Request->parseParams($allowedParams);
    }
}