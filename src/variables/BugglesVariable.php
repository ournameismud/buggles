<?php
/**
 * Buggles plugin for Craft CMS 3.x
 *
 * Simple video fieldtype for Craft 3
 *
 * @link      http://ournameismud.co.uk/
 * @copyright Copyright (c) 2018 @cole007
 */

namespace ournameismud\buggles\variables;

use ournameismud\buggles\Buggles;
use ournameismud\buggles\records\Video as videoRecord;

use Craft;

/**
 * @author    @cole007
 * @package   Buggles
 * @since     0.0.1
 */
class BugglesVariable
{
    // Public Methods
    // =========================================================================

    /**
     * @param null $optional
     * @return string
     */
    public function getVideo($id)
    {
        
        $video = videoRecord::findOne( [
            'id' => $id
        ]);
        return $video;
    }
}
