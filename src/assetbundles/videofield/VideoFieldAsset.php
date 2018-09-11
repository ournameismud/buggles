<?php
/**
 * Buggles plugin for Craft CMS 3.x
 *
 * Simple video fieldtype for Craft 3
 *
 * @link      http://ournameismud.co.uk/
 * @copyright Copyright (c) 2018 @cole007
 */

namespace ournameismud\buggles\assetbundles\videofield;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * @author    @cole007
 * @package   Buggles
 * @since     0.0.1
 */
class VideoFieldAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = "@ournameismud/buggles/assetbundles/videofield/dist";

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/Video.js',
        ];

        $this->css = [
            'css/Video.css',
        ];

        parent::init();
    }
}
