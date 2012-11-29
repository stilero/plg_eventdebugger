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

class JArticle15 extends JArticle{
    
    public static $JOOMLA_VERSION = '1.5';
    public static $ACCESS_PUBLIC = '0';
    public static $ACCESS_REGISTRED = '1';
    public static $ACCESS_SPECIAL = '2';
    public static $STATE_ARCHIVED = '-1';
    
    public function __construct($article) {
        parent::__construct($article);
    }
    
    protected function loadDependencies(){
        return;
    }
    
    public function categoryTitle($article){
        if(isset($article->category)){
            return $article->category;
        }else{
            $this->setError(self::$ERROR_NO_CATEGORY, 'No Category specified');
            return '';
        }
    }
    
    public static function truncate($text, $length = 0){
        // Truncate the item text if it is too long.
        if ($length > 0 && JString::strlen($text) > $length){
            // Find the first space within the allowed length.
            $tmp = JString::substr($text, 0, $length);
            $offset = JString::strrpos($tmp, ' ');
            if(JString::strrpos($tmp, '<') > JString::strrpos($tmp, '>')){
                $offset = JString::strrpos($tmp, '<');
            }
            $tmp = JString::substr($tmp, 0, $offset);
            // If we don't have 3 characters of room, go to the second space within the limit.
            if (JString::strlen($tmp) >= $length - 3) {
                $tmp = JString::substr($tmp, 0, JString::strrpos($tmp, ' '));
            }
            //put all opened tags into an array
            preg_match_all ( "#<([a-z][a-z0-9]?)( .*)?(?!/)>#iU", $tmp, $result );
            $openedtags = $result[1];
            $openedtags = array_diff($openedtags, array("img", "hr", "br"));
            $openedtags = array_values($openedtags);
            //put all closed tags into an array
            preg_match_all ( "#</([a-z]+)>#iU", $tmp, $result );
            $closedtags = $result[1];
            $len_opened = count ( $openedtags );
            //all tags are closed
            if( count ( $closedtags ) == $len_opened ){
                return $tmp.'...';
            }
            $openedtags = array_reverse ( $openedtags );
            // close tags
            for( $i = 0; $i < $len_opened; $i++ ){
                if ( !in_array ( $openedtags[$i], $closedtags ) ){
                    $tmp .= "</" . $openedtags[$i] . ">";
                } else {
                    unset ( $closedtags[array_search ( $openedtags[$i], $closedtags)] );
                }
            }
            $text = $tmp.'...';
        }
        return $text;
    }
    
    public function description($article, $limit=250){
        $desc = $article->text;
         if(isset($article->introtext) && $article->introtext!=""){
             $desc = $article->introtext;
         }elseif (isset($article->metadesc) && $article->metadesc!="" ) {
            $desc = $article->metadesc;
        }
        $description = self::truncate($desc, $limit);
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
        $now = JFactory::getDate()->toMySQL();
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
        return parent::tags($article);
    } 
}
