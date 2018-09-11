<?php
/**
 * Buggles plugin for Craft CMS 3.x
 *
 * Simple video fieldtype for Craft 3
 *
 * @link      http://ournameismud.co.uk/
 * @copyright Copyright (c) 2018 @cole007
 */

namespace ournameismud\buggles\services;

use ournameismud\buggles\Buggles;
use ournameismud\buggles\records\Video as videoRecord;

use Craft;
use craft\base\Component;
use craft\elements\Asset;
use craft\fields\Assets as AssetsField;
use craft\helpers\Assets as AssetsHelper;


// use craft\elements\User;

/**
 * @author    @cole007
 * @package   Buggles
 * @since     0.0.1
 */
class Video extends Component
{
    // Public Methods
    // =========================================================================

    /*
     * @return mixed
     */
    
    protected $contentType = array(
        'image/jpeg' => '.jpg',
        'image/gif' => '.gif',
        'image/png' => '.png',
    );

    public function fetch($url, $headers = false)
    {
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);
        if ($headers) return $info;
        return $result;
    }

    public function saveRecord($id, $type, $url) {
        
        $record = new videoRecord();
        $record->videoId = $id;
        $record->type  = $type;
        $record->url = $url;
        $user = Craft::$app->getUser();
        $record->owner = $user->id;
        
        switch ($type) {
            case 'vimeo':
                $url = 'https://vimeo.com/api/v2/video/' . $id . '.json';
        
                $response = json_decode($this->fetch($url));
                $record->title = $response[0]->title;
                $record->user = $response[0]->user_name;
                $thumb = $response[0]->thumbnail_large;

                $record->thumbnail = $this->saveImage($id, $thumb);
                
            break;
            case 'youtube':
                $thumb = 'https://img.youtube.com/vi/' . $id . '/maxresdefault.jpg';
                $response = json_decode($this->fetch('http://www.youtube.com/oembed?url=http://www.youtube.com/watch?v=' . $id . '&format=json' ));
                $record->title = $response->title;
                $record->user = $response->author_name;
                $record->thumbnail = $this->saveImage($id, $thumb);
            break;
        }
        
        $save = $record->save( false );

        if ( ! $save ) {
            // TO DO: elegant error response/log 
            Craft::dd($record->getErrors());
        }
        
        return $record->id;
    }

    public function saveImage($id, $thumb) {
        
        $headers = $this->fetch($thumb, true);
        $ext = $this->contentType[$headers['content_type']];        

        $image = file_get_contents($thumb);

        $tmpfname = tempnam("/tmp", $id  . time());
        $handle = fopen($tmpfname, "w");
        fwrite($handle, $image);
        
        $thumb = new Asset();
        $thumb->tempFilePath = $tmpfname;

        $assets = Craft::$app->getAssets();
        // TO DO: get this from Plugin or Field settings
        $folderId = 1; //<- insert your folder id

        $folder = $assets->findFolder(['id' => $folderId]);

        $thumb->filename = $id . $ext;
        
        $thumb->newFolderId = $folder->id;
        $thumb->volumeId = $folder->volumeId;
        $thumb->avoidFilenameConflicts = true;
        $thumb->setScenario(Asset::SCENARIO_CREATE);

        $errors = $thumb->getErrorSummary( true );
        
        if (Craft::$app->getElements()->saveElement($thumb)) {
            
            fclose($handle);

            if(is_file($tmpfname)) unlink($tmpfname);
            return $thumb->id;
        } else {
            // TO DO: elegant error response/log 
            echo 'Failed to save …';
            $errors = $thumb->getErrorSummary( true );
            Craft::dd( $thumb );

        }        
        
    }

    public function getMeta( String $handle, $element )
    {
        
        $url = $element->$handle;
        $vimeoMatch = '/(http|https)?:\/\/(www\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|)(\d+)(?:|\/\?)/';
        $youtubeMatch = '/(?:youtube\.com\/\S*(?:(?:\/e(?:mbed))?\/|watch\?(?:\S*?&?v\=))|youtu\.be\/)([a-zA-Z0-9_-]{6,11})/';

        $id = false;
        $type = null;
        switch ($url) {
            case (preg_match($vimeoMatch,$url,$matches) ? true : false) :
                $id = array_pop($matches);
                $type = 'vimeo';
            break;
            case (preg_match($youtubeMatch,$url,$matches) ? true : false) :
                $id = array_pop($matches);
                $type = 'youtube';
            break;
        }
        if ($id === false) return false;
        
        // else check if record exists
        $videos = videoRecord::findOne( [
            'videoId' => $id,
            'type' => $type,
            'url' => $url
        ]);
        
        if (count($videos) == 0) {
            // create record
            $response = $this->saveRecord($id, $type, $url);
            return $response;
        } else {
             return $videos->id;
        }
        
    }
}
