<?php
/**
 * Description of EventDebugger
 *
 * @version  1.0
 * @author Daniel Eliasson (joomla@stilero.com)
 * @copyright  (C) 2012-nov-29 Stilero Webdesign (www.stilero.com)
 * @category Plugins
 * @license	GPLv2
 */

// no direct access
defined('_JEXEC') or die('Restricted access'); 

// import library dependencies
jimport('joomla.plugin.plugin');
$classes = dirname(__FILE__).DS.'eventdebugger'.DS.'classes'.DS;
JLoader::register('JArticleErrors', $classes.'jarticle-errors.php');
JLoader::register('JArticleImage', $classes.'jarticle-image.php');
JLoader::register('JArticleUrl', $classes.'jarticle-url.php');
JLoader::register('JArticleUrl15', $classes.'jarticle-url15.php');
JLoader::register('K2JArticleUrl15', $classes.'k2jarticle-url15.php');
JLoader::register('JArticle', $classes.'jarticle.php');
JLoader::register('JArticle15', $classes.'jarticle15.php');
JLoader::register('JArticle16', $classes.'jarticle16.php');
JLoader::register('JArticle17', $classes.'jarticle17.php');
JLoader::register('JArticle30', $classes.'jarticle30.php');
JLoader::register('K2JArticle15', $classes.'k2jarticle15.php');


class plgContentEventdebugger extends JPlugin {
    var $config;

    function plgContentEventdebugger ( &$subject, $config ) {
        parent::__construct( $subject, $config );
    }
    
    public function debugArticle(&$article){
        $JA = new JArticle15($article);
        if($JA->isArticle()){
            $JAUrl = new JArticleUrl15($JA);
            $JAImage = new JArticleImage($JA);
            print "<pre>";
            print $JAUrl->url();
            //var_dump($article);
            //var_dump($JAImage->src());
            //print $JA->getArticle();exit;
            //var_dump($jarticle);exit;
            print "</pre>";
            return '';
        }
    }
    
    public function debugK2Article(&$article){
        $JA = new K2JArticle15($article);
        if($JA->isArticle()){
            $jarticle = $JA->getArticle();
            $JAUrl = new K2JArticleUrl15($JA);
            $JAImage = new JArticleImage($JA);
            print "<pre>";
            print $JAUrl->url();
            //var_dump($article);
            //var_dump($JAImage->src());
            var_dump($jarticle);
            //exit;
            //var_dump($jarticle);exit;
            print "</pre>";
            return '';
        }
    }
    // ---------- Joomla 1.6+ methods ------------------
    
    /**
     * Method is called before the content is deleted
     * 
     * @var string  $context    The context of the content passed to the plugin
     * @var object  $data       Data relating to the content deleted
     * @return boolean
     * @since 1.6 
     */
    public function onContentAfterDelete($context, $data){
        return true;
    }
    
    /**
     * Method is called by the view and the results are imploded and displayed in a placeholder
     * 
     * @var string  $context    The context of the content passed to the plugin
     * @var object  $article    content object. Note $article->text is also available
     * @var object  $params     content params
     * @var integer $limitstart The 'page' number
     * @return String
     * @since 1.6
     */
    public function onContentAfterDisplay($context, $article, &$params, $limitstart=0){
        $this->debugArticle($article);
            return '';
    }
    
    /**
     * Method is called right after the content is saved
     * 
     * @var string  $context    The context of the content passed to the plugin
     * @var object  $article    JTableContent object
     * @var bool    $isNew      If the content is just about to be created
     * @return void
     * @since 1.6
     */
    public function onContentAfterSave($context, &$article, $isNew){
        $this->debugArticle($article);
    }
    
    /**
     * Method is called by the view and the results are imploded and displayed in a placeholder
     * 
     * @var string  $context    The context of the content passed to the plugin
     * @var object  $article    content object. Note $article->text is also available
     * @var object  $params     content params
     * @var integer $limitstart The 'page' number
     * @return String
     * @since 1.6
     */
    public function onContentAfterTitle($context, &$article, &$params, $limitstart=0){
        //print __FUNCTION__;exit;
        return '';
    }
    
    /**
     * Method is called before the content is deleted
     * 
     * @var string  $context    The context of the content passed to the plugin
     * @var object  $data       Data relating to the content deleted
     * @return boolean
     * @since 1.6 
     */
    public function onContentBeforeDelete($context, $data){
        print __FUNCTION__;exit;
        return true;
    }
    
    /**
     * Method is called by the view and the results are imploded and displayed in a placeholder
     * 
     * @var string  $context    The context of the content passed to the plugin
     * @var object  $article    content object. Note $article->text is also available
     * @var object  $params     content params
     * @var integer $limitstart The 'page' number
     * @return String
     * @since 1.6
     */
    public function onContentBeforeDisplay($context, &$article, &$params, $limitstart=0){
        //print __FUNCTION__;exit;
        return '';
    }
    
    /**
     * Method is called right after the content is saved
     * 
     * @var string  $context    The context of the content passed to the plugin
     * @var object  $article    JTableContent object
     * @var bool    $isNew      If the content is just about to be created
     * @return boolean          If false, abort the save
     * @since 1.6
     */
    public function onContentBeforeSave($context, &$article, $isNew){
        return true;
    }
    
    /**
     * Called after Change state initiated
     * 
     * @var string  $context    The context of the content passed to the plugin
     * @var array   $pks        A list of primary key ids of the content that has changed state.
     * @var integer $value      The value of the state that the content has been changed to.
     * @return boolean
     * @since 1.6
     */
    public function onContentChangeState($context, $pks, $value){
        return true;
    }
    
    /**
     * Method is called by the view
     * 
     * @var string  $context    The context of the content passed to the plugin
     * @var object  $article    content object. Note $article->text is also available
     * @var object  $params     content params
     * @var integer $limitstart The 'page' number
     * @return void
     * @since 1.6
     */        
    public function onContentPrepare($context, &$article, &$params, $limitstart=0){
        
    }
    
    // ---------- Joomla 1.5 methods ------------------
    
    /**
     * Method is called right before the content is saved
     * 
     * @var object  $article    Reference to JTableContent object
     * @var bool    $isNew      If the content is just about to be created
     * @return boolean          If false, abort the save
     * @since 1.5
     */
    public function onBeforeContentSave(&$article, $isNew ){
        global $mainframe;
        return true;
    }
    
    /**
     * Method is called right after the content is saved
     * 
     * @var object  $article    Reference to JTableContent object
     * @var bool    $isNew      If the content is just about to be created
     * @return boolean          If false, abort the save
     * @since 1.5
     */
    public function onAfterContentSave(&$article, $isNew ){
        global $mainframe;
        $option = JRequest::getCmd('option');
        if($option == 'com_content'){
            $this->debugArticle($article);
        }elseif($option == 'com_k2'){
            $this->debugK2Article($article);
        }
        return true;
    }
    
    /**
     * The first stage in preparing content for output and is the most common point for content orientated plugins to do their work.
     * 
     * @var object  $article    A reference to the article that is being rendered by the view.
     * @var array   $params     A reference to an associative array of relevant parameters.
     * @var integer $limitstart An integer that determines the "page" of the content that is to be generated.
     * @return void
     * @since 1.5
     */
    public function onPrepareContent( &$article, &$params, $limitstart=0 ){
        global $mainframe;
    }
    
    /**
     * This is a request for information that should be placed between the content title and the content body.
     * 
     * @var object  $article    A reference to the article that is being rendered by the view.
     * @var array   $params     A reference to an associative array of relevant parameters.
     * @var integer $limitstart An integer that determines the "page" of the content that is to be generated.
     * @return string           Returned value from this event will be displayed in a placeholder.
     * @since 1.5
     */
    public function onAfterDisplayTitle( &$article, &$params, $limitstart=0 ){
        global $mainframe;
        //print __FUNCTION__;exit;
        return '';
    }
    
    /**
     * This is a request for information that should be placed immediately before the generated content.
     * 
     * @var object  $article    A reference to the article that is being rendered by the view.
     * @var array   $params     A reference to an associative array of relevant parameters.
     * @var integer $limitstart An integer that determines the "page" of the content that is to be generated.
     * @return string           Returned value from this event will be displayed in a placeholder.
     * @since 1.5
     */
    public function onBeforeDisplayContent( &$article, &$params, $limitstart=0 ){
        global $mainframe;
        //print __FUNCTION__;exit;
        return '';
    }
    
    /**
     * This is a request for information that should be placed immediately after the generated content.
     * 
     * @var object  $article    A reference to the article that is being rendered by the view.
     * @var array   $params     A reference to an associative array of relevant parameters.
     * @var integer $limitstart An integer that determines the "page" of the content that is to be generated.
     * @return string           Returned value from this event will be displayed in a placeholder.
     * @since 1.5
     */
    public function onAfterDisplayContent( $article, &$params, $limitstart=0 ){
        global $mainframe;
        $this->debugArticle($article);
        return '';
    }
    
    public function onK2AfterDisplayContent(&$item, &$params, $limitstart=0){
        $this->debugK2Article($item);
    }
    
    public function onAfterK2Save(&$row, $isNew){
        print __FUNCTION__;exit;
         $this->debugK2Article($row);
    }
    
    public function onK2AfterSave(&$row, $isNew){
        var_dump($row);exit;
         $this->debugK2Article($row);
    }

} //End Class