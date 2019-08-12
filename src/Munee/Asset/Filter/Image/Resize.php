<?php
/**
 * Munee: Optimising Your Assets
 *
 * @copyright Cody Lundquist 2013
 * @license http://opensource.org/licenses/mit-license.php
 */

namespace Fourmation\Munee\Asset\Filter\Image;

use \Fourmation\Munee\Asset\Filter;
use \Fourmation\Munee\ErrorException;
use \Imagine\Gd\Imagine as GdImagine;
use \Imagine\Gmagick\Imagine as GmagickImagine;
use \Imagine\Image\Box;
use \Imagine\Image\ImageInterface;
use \Imagine\Image\Palette\RGB;
use \Imagine\Image\Point;
use \Imagine\Imagick\Imagine as ImagickImagine;

/**
 * Resize Filter to resize/crop/fill/stretch images
 *
 * @author Cody Lundquist
 */
class Resize extends Filter
{
    /**
     * List of allowed params for this particular filter
     *
     * @var array
     */
    protected $allowedParams = [
        'resize' => [
            'arguments' => [
                'width' => [
                    'alias' => 'w',
                    'regex' => '\d+',
                    'cast' => 'integer',
                ],
                'height' => [
                    'alias' => 'h',
                    'regex' => '\d+',
                    'cast' => 'integer',
                ],
                'quality' => [
                    'alias' => [ 'q', 'qlty', 'jpeg_quality' ],
                    'regex' => '\d{1,2}(?!\d)|100',
                    'default' => 75,
                    'cast' => 'integer',
                ],
                'exact' => [
                    'alias' => 'e',
                    'regex' => 'true|false|t|f|yes|no|y|n',
                    'default' => 'false',
                    'cast' => 'boolean',
                ],
                'stretch' => [
                    'alias' => 's',
                    'regex' => 'true|false|t|f|yes|no|y|n',
                    'default' => 'false',
                    'cast' => 'boolean',
                ],
                'fill' => [
                    'alias' => 'f',
                    'regex' => 'true|false|t|f|yes|no|y|n',
                    'default' => 'false',
                    'cast' => 'boolean',
                ],
                'fillColour' => [
                    'alias' => [
                        'fc',
                        'fillColor',
                        'fillcolor',
                        'fill_color',
                        'fill-color',
                        'fillcolour',
                        'fill_colour',
                        'fill-colour'
                    ],
                    'regex' => '[A-Fa-f0-9]{3}$|^[A-Fa-f0-9]{6}',
                    'default' => 'ffffff',
                    'cast' => 'string',
                ],
            ],
        ],
    ];

    /**
     * Use Imagine to resize an image and return it's new path
     *
     * @param string $originalImage
     * @param array $arguments
     * @param array $imageOptions
     *
     * @return void
     *
     * @throws ErrorException
     */
    public function doFilter($originalImage, $arguments, $imageOptions)
    {
        // Need at least a height or a width
        if (empty($arguments['height']) && empty($arguments['width'])) {
            throw new ErrorException('You must set at least the height (h) or the width (w)');
        }
        switch (strtolower($imageOptions['imageProcessor'])) {
            case 'gd':
                $Imagine = new GdImagine();
                break;
            case 'imagick':
                $Imagine = new ImagickImagine();
                break;
            case 'gmagick':
                $Imagine = new GmagickImagine();
                break;
            default:
                throw new ErrorException('Unsupported imageProcessor config value: ' . $imageOptions['imageProcessor']);
        }
        $image = $Imagine->open($originalImage);

        $size = $image->getSize();
        $originalWidth = $size->getWidth();
        $originalHeight = $size->getHeight();
        $width = $originalWidth;
        $height = $originalHeight;

        if (! empty($arguments['height'])) {
            if ($originalHeight > $arguments['height'] || $arguments['stretch']) {
                $height = $arguments['height'];
            }
        }
        if (! empty($arguments['width'])) {
            if ($originalWidth > $arguments['width'] || $arguments['stretch']) {
                $width = $arguments['width'];
            }
        }

        /**
         * Prevent from someone from creating huge images
         */
        if ($width > $imageOptions['maxAllowedResizeWidth']) {
            $width = $imageOptions['maxAllowedResizeWidth'];
        }

        if ($height > $imageOptions['maxAllowedResizeHeight']) {
            $height = $imageOptions['maxAllowedResizeHeight'];
        }

        $mode = $arguments['exact'] ?
            ImageInterface::THUMBNAIL_OUTBOUND :
            ImageInterface::THUMBNAIL_INSET;

        $newSize = new Box($width, $height);

        $newImage = $image->thumbnail($newSize, $mode);
        if ($arguments['fill']) {
            $adjustedSize = $newImage->getSize();
            $canvasWidth = isset($arguments['width']) ? $arguments['width'] : $adjustedSize->getWidth();
            $canvasHeight = isset($arguments['height']) ? $arguments['height'] : $adjustedSize->getHeight();
            /**
             * Prevent from someone from creating huge images
             */
            if ($canvasWidth > $imageOptions['maxAllowedResizeWidth']) {
                $canvasWidth = $imageOptions['maxAllowedResizeWidth'];
            }

            if ($canvasHeight > $imageOptions['maxAllowedResizeHeight']) {
                $canvasHeight = $imageOptions['maxAllowedResizeHeight'];
            }

            $palette = new RGB();
            $canvas = $Imagine->create(
                new Box($canvasWidth, $canvasHeight),
                $palette->color($arguments['fillColour'])
            );

            // Put image in the middle of the canvas
            $newImage = $canvas->paste($newImage, new Point(
                (int) (($canvasWidth - $adjustedSize->getWidth()) / 2),
                (int) (($canvasHeight - $adjustedSize->getHeight()) / 2)
            ));
        }

        $newImage->save($originalImage, [ 'jpeg_quality' => $arguments['quality'] ]);
    }
}
