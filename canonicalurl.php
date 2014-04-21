<?php
/**
 * @package     Canonical URL
 * @copyright   Copyright (c) 2014 Hans Kuijpers - HKweb
 * @license     GNU General Public License version 3 or later
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
 
class plgSystemCanonicalurl extends JPlugin
{

    /**
     * Load the language file on instantiation. Note this is only available in Joomla 3.1 and higher.
     * If you want to support 3.0 series you must override the constructor
     *
     * @var    boolean
     * @since  3.1
     */
    protected $autoloadLanguage = true;

    /**
     * Replace Canonical URL set by Joomla!
     * This plugin should be loaded after Joomla! SEF plugin
     * Order can be set in Joomla! backend
     *
     * Using trigger onAfterRoute since Joomla! SEF plugin uses this one too
     */
    function onContentBeforeDisplay($article, $params, $limitstart)
    {
        $app     = JFactory::getApplication();
        $jinput  = $app->input;
        $option  = $jinput->get('option', null);
        $view    = $jinput->get('view', null);
        $doc     = JFactory::getDocument();

        // stop this plugin when you are in admin
        if ($app->isAdmin()) {
            return;
        }

        // stop this plugin when you are not in one of these components
        $allowedcomponents = array('com_content');
        if(!in_array($option,$allowedcomponents))
        {
            return;
        }

        if($article == 'com_content.article' && ($option == "com_content" && $view == "article"))
        {
            // remove existing Canonical URL
            $this->removeCanonical();

            // create new Canonical URL
            $canonicalurl = JRoute::_( ContentHelperRoute::getArticleRoute( (int)$params->id, $params->catslug),false,-1);

            // set new Canonical URL
            $doc->addHeadLink(htmlspecialchars($canonicalurl), 'canonical');
        }
    }

    private function removeCanonical()
    {
        $doc = JFactory::getDocument();

        foreach ( $doc->_links as $k => $array )
        {
            if ( $array['relation'] == 'canonical' )
            {
                unset($doc->_links[$k]);
            }
        }
    }
}