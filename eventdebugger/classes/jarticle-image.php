<?php
/**
 * JArticle_Class
 *
 * @version  1.0
 * @package Stilero
 * @subpackage JArticle_Class
 * @author Daniel Eliasson (joomla@stilero.com)
 * @copyright  (C) 2012-nov-29 Stilero Webdesign (www.stilero.com)
 * @license	GNU General Public License version 2 or later.
 * @link http://www.stilero.com
 */

// no direct access
defined('_JEXEC') or die('Restricted access'); 

class JArticleImage{
    
    private $JArticle;
    protected $imageFirstInContent;
    protected $imageFullText;
    protected $imageIntro;
    protected $image;
    protected $images;
    
    public function __construct($JArticle) {
        $this->JArticle = $JArticle;
    }
    
    protected function articleImages(){
        if(isset($this->images)){
            return $this->images;
        }
        $imageJSON = $this->JArticle->images;
        $obj = json_decode($imageJSON);
        $introImage = '';
        $fullImage = '';
        if(isset($obj->{'image_intro'})){
            $introImage = $obj->{'image_intro'};
        }
        if(isset($obj->{'image_fulltext'})){
            $fullImage = $obj->{'image_fulltext'};
        }
        $this->images = array(
            'intro' => $introImage,
            'full'  => $fullImage
        );
        return $this->images;
    }

    protected function extractImage($imgType){
        $images = $this->articleImages();
        $image = '';
        if(isset($images[$imgType])){
            $image = $images[$imgType];
        }
        if($image != ""){
            $image = preg_match('/http/', $image)? $image : JURI::root().$image;
        }
        return $image;
    }
    
    protected function introImage(){
        if(isset($this->imageIntro)){
            return $this->imageIntro;
        }
        $this->imageIntro = $this->extractImage('intro');
        return $this->imageIntro;
    }
    
    protected function fullTextImage(){
        if(isset($this->imageFullText)){
            return $this->imageFullText;
        }
        $this->imageFullText = $this->extractImage('full');
        return $this->imageFullText;
    }
    
    public function imagesInContent(){
        if(isset($this->images)){
            return $this->images;
        }
        $content = $this->JArticle->text;
        if($content == ''){
            $content = $this->JArticle->fulltext;
        }
        if($content == ''){
            $content = $this->JArticle->introtext;
        }
         if( ($content == '') || (!class_exists('DOMDocument')) ){
            return;
        }
        $html = new DOMDocument();
        $html->recover = true;
        $html->strictErrorChecking = false;
        $html->loadHTML($content);
        $images = array();
        foreach($html->getElementsByTagName('img') as $image) {
            $images[] = array(
                'src' => $image->getAttribute('src'),
                'class' => $image->getAttribute('class'),
            );
        }
        $this->images = $images;
        return $this->images;
    }
    
    public function firstImageInContent(){
        if(isset($this->imageFirstInContent)){
            return $this->imageFirstInContent;
        }
        $content = $this->JArticle->text;
        if($content == ''){
            $content = $this->JArticle->fulltext;
        }
        if($content == ''){
            $content = $this->JArticle->introtext;
        }
        if($content == ''){
            return;
        }
        $images = $this->imagesInContent();
        $image = '';
        if(isset($images[0]['src'])){
            $image = $images[0]['src'];
        }
        if($image != ""){
            $image = preg_match('/http/', $image)? $image : JURI::root().$image;
        }
        $this->imageFirstInContent = $image;
        return $this->imageFirstInContent;
    }
    
    public function src(){
        if(isset($this->image)){
            return $this->image;
        }
        $image = $this->introImage();
        if ($image == '' ){
            $image = $this->fullTextImage();
        }
        if($image == ''){
            $image = $this->firstImageInContent();
        }
        $this->image = $image;
        return $image;
    }
    
}
