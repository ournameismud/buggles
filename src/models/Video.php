<?php
/**
 * Buggles plugin for Craft CMS 3.x
 *
 * Simple video fieldtype for Craft 3
 *
 * @link      http://ournameismud.co.uk/
 * @copyright Copyright (c) 2018 @cole007
 */

namespace ournameismud\buggles\models;

use ournameismud\buggles\Buggles;

use Craft;
use craft\base\Model;

/**
 * @author    @cole007
 * @package   Buggles
 * @since     0.0.1
 */
class Video extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $someAttribute = 'Some Default';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['url', 'string'],
            ['type', 'string'],
            ['videoId', 'string'],
        ];
    }
}
