<?php
/**
 * JArticle_Class for J2.5
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
jimport('joomla.utilities.date');
//jimport( 'joomla.html.html.string' );

class JArticle extends JArticleErrors{
    
    public $Article;
    public static $JOOMLA_VERSION = '2.5';
    public static $ACCESS_PUBLIC = '1';
    public static $ACCESS_REGISTRED = '2';
    public static $ACCESS_SPECIAL = '3';
    public static $ACCESS_CUSTOM = '4';
    public static $STATE_PUBLISHED = '1';
    public static $STATE_UNPUBLISHED = '0';
    public static $STATE_ARCHIVED = '2';
    public static $STATE_TRASHED = '-2';


    public function __construct($article) {
        $this->loadDependencies();
        $tempClass = new stdClass();
        foreach ($article as $property => $value) {
            $tempClass->$property = $value;
        }
        $tempClass->category_title = $this->categoryTitle($article);
        $tempClass->description = $this->description($article);
        $tempClass->isPublished = $this->isPublished($article);
        $tempClass->isPublic = $this->isPublic($article);
        $tempClass->tags = $this->tags($article);
        $this->Article = $tempClass;
    }
    
    protected function loadDependencies(){
        JLoader::register('JHtmlString', JPATH_ROOT.'/libraries/joomla/html/html/string.php');
    }
    
    public function categoryTitle($article){
        if(isset($article->category_title)){
            return $article->category_title;
        }else{
            $this->setError(self::$ERROR_NO_CATEGORY, 'No Category specified');
            return '';
        }
    }
    
    public function description($article, $limit=250){
        $desc = $article->text;
         if(isset($article->introtext) && $article->introtext!=""){
             $desc = $article->introtext;
         }elseif (isset($article->metadesc) && $article->metadesc!="" ) {
            $desc = $article->metadesc;
        }
        $description = JHtmlString::truncate($desc, $limit, true, false);
        return $description;
    }
    
    public function isPublished($article){
        $isPublished = $article->state == self::$STATE_PUBLISHED ? true : false;
        if(!$isPublished){
            return FALSE;
        }
        $publishUp = isset($article->publish_up) ? $article->publish_up : '';
        $publishDown = isset($article->publish_down) ? $article->publish_down : '';
        if($publishUp == '' ){
            return false;
        }
        $now = JFactory::getDate()->toSql();
        if ( ($publishUp > $now) ){
            return FALSE;
        }else if($publishDown < $now && $publishDown != '0000-00-00 00:00:00' && $publishDown!=""){
            return FALSE;
        }else {
            return TRUE;
        }
    }
    
    public function isPublic($article){
        if(!isset($article->access)){
            return FALSE;
        }
        $isPublic = $article->access == self::$ACCESS_PUBLIC ? TRUE : FALSE;
        return $isPublic;
    }
    
    public function tags($article) {
        $metaKeysString = isset($article->metakey) ? $article->metakey : '';
        if($metaKeysString == ""){
            return;
        }
       $metas = explode(",", $metaKeysString);
       $tags = array();
       foreach ($metas as $key => $value) {
           $tags[] = trim(str_replace(" ", "", $value));
       }
       return $tags;
    } 
    
//    public function __get($name) {
//        return $this->$name;
//    }
}