<?php
/**
 * Buggles plugin for Craft CMS 3.x
 *
 * Simple video fieldtype for Craft 3
 *
 * @link      http://ournameismud.co.uk/
 * @copyright Copyright (c) 2018 @cole007
 */

namespace ournameismud\buggles\fields;

use ournameismud\buggles\Buggles;
use ournameismud\buggles\services\Video as VideoService;
use ournameismud\buggles\records\Video as videoRecord;
use ournameismud\buggles\assetbundles\videofield\VideoFieldAsset;

use Craft;
use craft\base\ElementInterface;

// use craft\base\SavableComponentInterface;
// use craft\elements\db\ElementQuery;
// use craft\elements\db\ElementQueryInterface;
use craft\base\Field;
use craft\base\FieldInterface;
use craft\helpers\Db;
use yii\db\Schema;
use craft\helpers\Json;

/**
 * @author    @cole007
 * @package   Buggles
 * @since     0.0.1
 */
class Video extends Field
{
    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $someAttribute = 'Some Default';

    // Static Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('buggles', 'Video');
    }

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = parent::rules();
        // $rules = array_merge($rules, [
        //     ['someAttribute', 'string'],
        //     ['someAttribute', 'default', 'value' => 'Some Default'],
        // ]);
        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function getContentColumnType(): string
    {
        return Schema::TYPE_STRING;
    }

    /**
     * @inheritdoc
     */
    public function normalizeValue($value, ElementInterface $element = null)
    {
        return $value;
    }

    public function afterElementSave(ElementInterface $element, bool $isNew)
    {
        static $recursionLevel = 0; // is set only once
        $handle = $this->handle;
        if ($recursionLevel == 0 && $element->$handle) 
        {
            $recursionLevel++;
            $record = Buggles::getInstance()->video->getMeta( $handle, $element );
            $element->$handle = (string)$record;
            $success = Craft::$app->getElements()->saveElement( $element );
            
            if (!$success) {
                // TO DO: elegant error response/log 
                echo 'couldn\'t save element ' . $element->id ;
                Craft::dd( $element->getErrors() );                
            } 
        }
        
    }

    /**
     * @inheritdoc
     */
    public function serializeValue($value, ElementInterface $element = null)
    {
        return parent::serializeValue($value, $element);
    }

    /**
     * @inheritdoc
     */
    public function getSettingsHtml()
    {
        // Render the settings template
        // TO DO: save preferred location for thumbnails?
        return Craft::$app->getView()->renderTemplate(
            'buggles/_components/fields/Video_settings',
            [
                'field' => $this,
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function getInputHtml($value, ElementInterface $element = null): string
    {
        // Register our asset bundle
        Craft::$app->getView()->registerAssetBundle(VideoFieldAsset::class);

        // Get our id and namespace
        $id = Craft::$app->getView()->formatInputId($this->handle);
        $namespacedId = Craft::$app->getView()->namespaceInputId($id);
        $image = null;
        
        if ($value) {
            $video = videoRecord::findOne( [
                'id' => $value
            ]);
            $value = $video->url;
            $image = $video->thumbnail;   
        }
        
        // Variables to pass down to our field JavaScript to let it namespace properly
        $jsonVars = [
            'id' => $id,
            'name' => $this->handle,
            'namespace' => $namespacedId,
            'prefix' => Craft::$app->getView()->namespaceInputId(''),
            ];
        $jsonVars = Json::encode($jsonVars);
        Craft::$app->getView()->registerJs("$('#{$namespacedId}-field').BugglesVideo(" . $jsonVars . ");");

        // Render the input template
        return Craft::$app->getView()->renderTemplate(
            'buggles/_components/fields/Video_input',
            [
                'thumbnail' => $image,
                'name' => $this->handle,
                'value' => $value,
                'field' => $this,
                'id' => $id,
                'namespacedId' => $namespacedId,
            ]
        );
    }
}
