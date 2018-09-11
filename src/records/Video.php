<?php
/**
 * Buggles plugin for Craft CMS 3.x
 *
 * Simple video fieldtype for Craft 3
 *
 * @link      http://ournameismud.co.uk/
 * @copyright Copyright (c) 2018 @cole007
 */

namespace ournameismud\buggles\records;

use ournameismud\buggles\Buggles;

use Craft;
use craft\db\ActiveRecord;

/**
 * @author    @cole007
 * @package   Buggles
 * @since     0.0.1
 */
class Video extends ActiveRecord
{
    // Public Static Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%buggles_video}}';
    }
}
