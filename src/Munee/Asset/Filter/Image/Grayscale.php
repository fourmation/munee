<?php
/**
 * Munee: Optimising Your Assets
 *
 * @copyright Cody Lundquist 2013
 * @license http://opensource.org/licenses/mit-license.php
 */

namespace Fourmation\Munee\Asset\Filter\Image;

use \Fourmation\Munee\Asset\Filter;
use \Imagine\Gd\Imagine;

/**
 * Grayscale Filter for Images
 *
 * @author Cody Lundquist
 */
class Grayscale extends Filter
{
    /**
     * List of allowed params for this particular filter
     *
     * @var array
     */
    protected $allowedParams = [
        'grayscale' => [
            'regex' => 'true|false|t|f|yes|no|y|n',
            'default' => 'false',
            'cast' => 'boolean',
        ]
    ];

    /**
     * Turn an image Grayscale
     *
     * @param string $file
     * @param array $arguments
     * @param array $typeOptions
     *
     * @return void
     */
    public function doFilter($file, $arguments, $typeOptions)
    {
        if (! $arguments['grayscale']) {
            return;
        }

        $Imagine = new Imagine();
        $image = $Imagine->open($file);
        $image->effects()->grayscale();
        $image->save($file);
    }
}
