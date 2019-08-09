<?php
/**
 * Munee: Optimising Your Assets
 *
 * @copyright Cody Lundquist 2013
 * @license http://opensource.org/licenses/mit-license.php
 */

namespace Fourmation\Munee\Asset\Filter\JavaScript;

use \Fourmation\Munee\Asset\Filter;
use \JavaScriptPacker;

/**
 * Dean Edwards' Packer Filter for JavaScript
 *
 * @author Cody Lundquist
 */
class Packer extends Filter
{
    /**
     * List of allowed params for this particular filter
     *
     * @var array
     */
    protected $allowedParams = [
        'packer' => [
            'regex' => 'true|false|t|f|yes|no|y|n',
            'default' => 'false',
            'cast' => 'boolean',
        ]
    ];

    /**
     * Default options for the Packer library
     *
     * @var array
     */
    protected $_defaultPackerOptions = [
        'encoding' => 62,
        'fastDecode' => true,
        'specialChars' => false,
    ];

    /**
     * JavaScript Packer
     *
     * @param string $file
     * @param array $arguments
     * @param array $javaScriptOptions
     *
     * @return void
     */
    public function doFilter($file, $arguments, $javaScriptOptions)
    {
        $userOptions = isset($javaScriptOptions['packer']) ? $javaScriptOptions['packer'] : [];
        $options = array_merge($this->_defaultPackerOptions, $userOptions);

        if (! $arguments['packer']) {
            return;
        }

        $content = file_get_contents($file);
        $packer = new JavaScriptPacker($content, $options['encoding'], $options['fastDecode'], $options['specialChars']);
        file_put_contents($file, $packer->pack());
    }
}
