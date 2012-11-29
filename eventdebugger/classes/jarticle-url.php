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

class JArticleUrl{
    
    public function __construct($JArticle) {
        $this->Article->url = $this->url($JArticle);
    }
    
    protected function isExtensionInstalled($option){
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from($query->quoteName('#__extensions'));
        $query->where($query->quoteName('type') . ' = ' . $db->quote('component'));
        $query->where($query->quoteName('element').' = '.$db->quote($option));
        $db->setQuery($query);
        $result = $db->loadObject();
        if(!$result){
            return FALSE;
        }
        return TRUE;
    }
    
    private function _categoryAlias($article){
        jimport( 'joomla.filter.output' );
        $db =& JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select('alias');
        $query->from($db->quoteName('#__categories'));
        $query->where('id = '.$db->quote($article->catid));
        $db->setQuery($query);
        $result = $db->loadObject();
        $alias = JFilterOutput::stringURLSafe($result->alias);
        return $alias;
    }
    
    private function _articleAlias($article){
        jimport( 'joomla.filter.output' );
        $alias = $article->alias;
        if(empty($alias)) {
            $db =& JFactory::getDBO();
            $query = $db->getQuery(true);
            $query->select('alias');
            $query->from($db->quoteName('#__content'));
            $query->where('id='.$db->quote($article->id));
            $db->setQuery($query);
            $result = $db->loadObject();
            $alias = $article->title;
            if(!empty($result->alias)){
                $alias = $result->alias;
            }
        }
        $filteredAlias = JFilterOutput::stringURLSafe($alias);
        return $filteredAlias;
    }
    
    private function _articleSlug($article){
        $slug = $article->id.':'.$this->_articleAlias($article);
        return $slug;
    }
    
    private function _initSh404SefUrls(){
        $app = &JFactory::getApplication();
        require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_sh404sef'.DS.'sh404sef.class.php');
        $sefConfig = & Sh404sefFactory::getConfig();

        // hook to be able to install other SEF extension plugins
        Sh404sefHelperExtplugins::loadInstallAdapters();

        // another hook to allow other SEF extensions language file to be loaded
        Sh404sefHelperExtplugins::loadLanguageFiles();

        if (!$sefConfig->Enabled) {
            // go away if not enabled
            return;
        }
        $joomlaRouter = $app->getRouter();
        $pageInfo = & Sh404sefFactory::getPageInfo();
        $pageInfo->router = new Sh404sefClassRouter();
        $joomlaRouter->attachParseRule( array( $pageInfo->router, 'parseRule'));
        $joomlaRouter->attachBuildRule( array( $pageInfo->router, 'buildRule'));
    }
    
    private function _attachSh404SefRouting(){
         if(!$this->isExtensionInstalled('com_sh404sef')){
             return;
        }
        $isSh404SefExtensionEnabled = JComponentHelper::isEnabled('com_sh404sef', true);
        if(!$isSh404SefExtensionEnabled && JPATH_BASE != JPATH_ADMINISTRATOR){
            return;
        }
        $this->_initSh404SefUrls();
        
    }
    
    private function _joomlaSefUrlFromRoute($article){
        require_once(JPATH_SITE.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php');
        $siteURL = substr(JURI::root(), 0, -1);
        if(JPATH_BASE == JPATH_ADMINISTRATOR) {
            // In the back end we need to set the application to the site app instead
            JFactory::$application = JApplication::getInstance('site');
        }
        $catAlias = $this->_categoryAlias($article);
        $articleSlug = $this->_articleSlug($article);
        $catSlug = $article->catid.':'.$catAlias;
        $this->_attachSh404SefRouting();
        $articleRoute = JRoute::_( ContentHelperRoute::getArticleRoute($articleSlug, $catSlug) );
        $sefURI = str_replace(JURI::base(true), '', $articleRoute);
        if(JPATH_BASE == JPATH_ADMINISTRATOR) {
            $siteURL = str_replace($siteURL.DS.'administrator', '', $siteURL);
            JFactory::$application = JApplication::getInstance('administrator');
        }
        $sefURL = $siteURL.$sefURI;
        return $sefURL;
    }
        
    public function url($article){
        return $this->_joomlaSefUrlFromRoute($article);
    }
}
