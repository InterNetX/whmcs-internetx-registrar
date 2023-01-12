<?php
/*
 *  * ******************************************************************
 * 
 *    CREATED BY MODULESGARDEN       ->        http://modulesgarden.com
 *    AUTHOR                         ->        Dariusz Bijoś <dariusz.bi@modulesgarden.com>
 *    CONTACT                        ->        contact@modulesgarden.com
 * 
 *   This software is furnished under a license and may be used and copied
 *   only  in  accordance  with  the  terms  of such  license and with the
 *   inclusion of the above copyright notice.  This software  or any other
 *   copies thereof may not be provided or otherwise made available to any
 *   other person.  No title to and  ownership of the  software is  hereby
 *   transferred.
 * 
 *  * ******************************************************************
 */

namespace MGModule\InterNetX\Addon\Core;

use MGModule\InterNetX as main;
use MGModule\InterNetX\Addon\Core\process\AbstractMainDriver;
use MGModule\InterNetX\Addon\Core\extensions\Smarty;

/**
 * Application Core
 *
 * @author Dariusz Bijoś <dariusz.bi@modulesgarden.com>
 */
class App extends AbstractMainDriver
{

    public function __construct()
    {
        parent::__construct("Addon");
    }

    /**
     * Application Root directorey
     * @author Dariusz Bijoś <dariusz.bi@modulesgarden.com>
     * @since 1.0.0
     * @return string
     */
    public static function getMainDIR()
    {
        return dirname(__DIR__);
    }

    /**
     * Tempalates Path
     * @author Dariusz Bijoś <dariusz.bi@modulesgarden.com>
     * @since 1.0.0
     * @param boolean $relative
     * @return string
     */
    static function getModuleTemplatesDir($relative = false)
    {

        $dir = ($relative) ? 'app' : self::getMainDIR();
        $dir .= DS.'Templates'.DS;

        if (self::I()->isAdmin()) {
            return $dir.'Admin';
        } else {
            $template = $GLOBALS['CONFIG']['Template'];
            if (file_exists(__DIR__.DS.'Templates'.DS.'ClientArea'.DS.$template)) {
                return $dir.'ClientArea'.DS.$template;
            } else {
                return $dir.'ClientArea'.DS.'default';
            }
        }
    }

    /**
     * Application Assets Url
     * @author Dariusz Bijoś <dariusz.bi@modulesgarden.com>
     * @since 1.0.0
     * @return string
     */
    public function getAssetsURL()
    {
        if ($this->isAdmin()) {
            return '../modules/addons/'.$this->configuration()->systemName.'/app/Assets';
        } else {
            return 'modules/addons/'.$this->configuration()->systemName.'/app/Assets';
        }
    }

    static function getUrl($page = null, $action = null, $params = array())
    {
        if (self::I()->isAdmin()) {
            $url = 'addonmodules.php?module='.self::I()->configuration()->systemName;
        } else {
            $url = 'index.php?m='.self::I()->configuration()->systemName;
        }

        if ($page) {
            $url .= '&mg-page='.$page;
            if ($action) {
                $url .= '&mg-action='.$action;
            }

            if ($params) {
                $url .= '&'.http_build_query($params);
            }
        }
        return $url;
    }

    /**
     * Application JSON request Url
     * @author Dariusz Bijoś <dariusz.bi@modulesgarden.com>
     * @since 1.0.0
     * @return string
     */
    public static function genJSONUrl($page)
    {
        if (self::I()->isAdmin()) {
            return 'addonmodules.php?module='.self::I()->configuration()->systemName.'&json=1&mg-page='.$page;
        } else {
            return 'index.php?m='.self::I()->configuration()->systemName.'&json=1&mg-page='.$page;
        }
    }

    /**
     * Application request Url
     * @author Dariusz Bijoś <dariusz.bi@modulesgarden.com>
     * @since 1.0.0
     * @return string
     */
    public static function genAppUrl($page)
    {
        if (self::I()->isAdmin()) {
            return 'addonmodules.php?module='.self::I()->configuration()->systemName.'&mg-page='.$page;
        } else {
            return 'index.php?m='.self::I()->configuration()->systemName.'&mg-page='.$page;
        }
    }

    /**
     * Application Configuration
     * @author Dariusz Bijoś <dariusz.bi@modulesgarden.com>
     * @since 1.0.0
     * @return array
     */
    public static function config()
    {
        return [
            'name' => self::I()->configuration()->name,
            'description' => self::I()->configuration()->description,
            'version' => self::I()->configuration()->version,
            'author' => self::I()->configuration()->author,
            'language' => self::I()->configuration()->language,
            'fields' => self::I()->configuration()->getAddonWHMCSConfig(),
        ];
    }

    /**
     * Application Activation
     * @author Dariusz Bijoś <dariusz.bi@modulesgarden.com>
     * @since 1.0.0
     * @return array
     */
    public static function activate()
    {
        try {
            return self::I()->configuration()->activate();
        } catch (\Exception $ex) {
            return [
                'status' => 'error',
                'description' => $ex->getMessage()
            ];
        }
    }

    /**
     * Application Deactivation
     * @author Dariusz Bijoś <dariusz.bi@modulesgarden.com>
     * @since 1.0.0
     * @return array
     */
    public static function deactivate()
    {
        try {
            return self::I()->configuration()->deactivate();
        } catch (\Exception $ex) {
            return [
                'status' => 'error',
                'description' => $ex->getMessage()
            ];
        }
    }

    /**
     * Application Upgrade
     * @author Dariusz Bijoś <dariusz.bi@modulesgarden.com>
     * @since 1.0.0
     * @return array
     */
    public static function upgrade($vars)
    {
        try {
            return self::I()->configuration()->upgrade($vars);
        } catch (\Exception $ex) {
            return array("error" => $ex->getMessage());
        }
    }

    /**
     * Application Admin Area HTML Page
     * @author Dariusz Bijoś <dariusz.bi@modulesgarden.com>
     * @since 1.0.0
     * @param array $input
     * @return HTML
     */
    public static function getHTMLAdminPage($input, $vars = array())
    {
        global $_LANG;

        try {
            self::I()->isAdmin(true);

            if (self::I()->isDebug()) {
                self::I()->configuration()->activate();
            }

            $menu = [];
            foreach (self::I()->configuration()->getAddonMenu() as $catName => $category) {
                // display the page or not
                if (strpos($catName, "documentation") === false) {
                    $className = self::I()->getMainNamespace()."\\Controllers\\Admin\\".ucfirst($catName);
                    $controller = new $className ();
                    if (method_exists($controller, "isActive") && !$controller->{"isActive"}()) {
                        continue;
                    }
                }


                if (isset($category['submenu'])) {
                    foreach ($category['submenu'] as $subName => &$subPage) {
                        if (empty($subPage['url'])) {
                            $subPage['url'] = self::getUrl($catName, $subName);
                        }
                    }
                }
                $category['url'] = self::getUrl($catName);

                $menu[$catName] = $category;
            }

            if (empty($input['mg-page'])) $input['mg-page'] = key($menu);

            $LANG = array_merge($_LANG, $vars['_lang']);

            if ($input['mg-page']) {
                $breadcrumb[0] = [
                    'name' => (isset($LANG['pagesLabels']['label'][$input['mg-page']])) ? $LANG['pagesLabels']['label'][$input['mg-page']] : ucfirst($input['mg-page']),
                    'url' => $menu[$input['mg-page']]['url'],
                    'icon' => $menu[$input['mg-page']]['icon']
                ];
                if ($input['mg-action']) {
                    $breadcrumb[1] = [
                        'name' => ucfirst($input['mg-action']),
                        'url' => $menu[$input['mg-page']]['submenu'][$input['mg-action']]['url'],
                        'icon' => $menu[$input['mg-page']]['submenu'][$input['mg-action']]['icon']
                    ];
                }
            }

            $page = $input['mg-page'];
            $action = empty($input['mg-action']) ? 'index' : $input['mg-action'];
            $page = ucfirst($page);

            $vars = [
                'LANG' => $LANG,
                'assetsURL' => self::I()->getAssetsURL(),
                'mainURL' => self::I()->getUrl(),
                'mainName' => self::I()->configuration()->name,
                'menu' => $menu,
                'breadcrumbs' => $breadcrumb,
                'JSONCurrentUrl' => self::I()->genJSONUrl($page),
                'currentPageName' => $page,
                'Addon' => self::I(),
                'isWHMCS6' => version_compare($GLOBALS['CONFIG']['Version'], '6.0.0', '>=')
            ];

            try {
                list($content, $success, $error) = self::I()->runControler($page, $action, $input, 'HTML', $vars);
                $vars['content'] = $content;
                $vars['success'] = $success;
                $vars['error'] = $error;
            } catch (\Exception $ex) {
                self::dump($ex);
                //main\mgLibs\error\Register::register($ex);
                $vars['error'] = $ex->getMessage();
                //if (method_exists($ex, 'getToken')) {
                //    $vars['error'] .= mgLibs\Lang::absoluteT('token').$ex->getToken();
                //}
            }

            Smarty::I()->setTemplateDir(self::I()->getModuleTemplatesDir());
            $html = Smarty::I()->view('main', $vars);

            if (self::I()->isDebug()) {
                $tmp = '<div style="color: #a94442;background-color: #f2dede;border-color: #dca7a7;font-size:20px;padding:10px;"><strong>Module is under development Mode!!!!!!!!!!!!!!!</strong></div>';
                $html = $tmp.$html;
            }

            return $html;
        } catch (\Exception $ex) {
            //self::dump($ex);
            //main\mgLibs\error\Register::register($ex);
            Smarty::I()->setTemplateDir(self::I()->getModuleTemplatesDir());
            $message = $ex->getMessage();
            //if (method_exists($ex, 'getToken')) {
            //    $message .= mgLibs\Lang::absoluteT('token').$ex->getToken();
            //}

            return Smarty::I()->view('fatal', ['message' => $message, 'assetsURL' => self::I()->getAssetsURL()]);
        }
    }

    /**
     * Method Description
     * @author Dariusz Bijoś <dariusz.bi@modulesgarden.com>
     * @since 1.0.0
     * @param string $username  user name
     * @return Example
     * @throws Exception - fatal error
     * @throws UserException - username or password is not set
     */
    public static function getJSONAdminPage($input, $vars = array())
    {
        $page = 'Home';
        if (!empty($input['mg-page'])) {
            $page = $input['mg-page'];
        }
        $page = ucfirst($page);
        $action = empty($input['mg-action']) ? 'index' : $input['mg-action'];
        try {
            self::I()->isAdmin(true);

            list($result, $success, $error) = self::I()->runControler($page, $action, $input, 'JSON');
            if ($error) {
                $content['error'] = $error;
                $content['result'] = 'error';
            } elseif ($success) {
                $content['success'] = $success;
                $content['result'] = 'success';
            }

            $content['data'] = $result;
        } catch (\Exception $ex) {
            $content['result'] = 'error';
            $content['error'] = $ex->getMessage();
        }

        return '<JSONRESPONSE#'.json_encode($content).'#ENDJSONRESPONSE>';
    }

    /**
     * Application Clietn Area HTML Page
     * @author Dariusz Bijoś <dariusz.bi@modulesgarden.com>
     * @since 1.0.0
     * @param array $input
     * @return HTML
     */
    public static function getHTMLClientAreaPage($input, $vars = array())
    {
        global $_LANG;

        $menu = [];
        foreach (self::I()->configuration()->getClienMenu() as $catName => $category) {
            // display the page or not
            if (strpos($catName, "documentation") === false) {
                $className = self::I()->getMainNamespace()."\\Controllers\\ClientArea\\".ucfirst($catName);
                $controller = new $className ();
                if (method_exists($controller, "isActive") && !$controller->{"isActive"}()) continue;
            }
            if (isset($category['submenu'])) {
                foreach ($category['submenu'] as $subName => &$subPage) {
                    if (empty($subPage['url'])) {
                        $subPage['url'] = self::getUrl($catName, $subName);
                    }
                }
            }
            $category['url'] = self::getUrl($catName);
            $menu[$catName] = $category;
        }
        if (empty($input['mg-page'])) $input['mg-page'] = key($menu);

        $output = array(
            'pagetitle' => self::I()->configuration()->clientareaName,
            'templatefile' => self::I()->getModuleTemplatesDir(true).'/main',
            'requirelogin' => isset($_SESSION['uid']) ? false : true,
        );

        $breadcrumb = array(self::I()->getUrl() => self::I()->configuration()->clientareaName);
        try {

            $page = ucfirst($input['mg-page']);
            if (!empty($input['mg-page'])) {
                $url = self::I()->getUrl($input['mg-page']);
                $breadcrumb[$url] = $input['mg-page'];
            }
            $action = empty($input['mg-action']) ? 'index' : $input['mg-action'];

            $vars = [
                'LANG' => array_merge($_LANG, $vars['_lang']),
                'assetsURL' => self::I()->getAssetsURL(),
                'mainURL' => self::I()->getUrl(),
                'mainName' => self::I()->configuration()->clientareaName,
                'JSONCurrentUrl' => self::I()->genJSONUrl($page),
                'currentPageName' => strtolower($page),
                'menu' => $menu,
                'breadcrumbs' => $breadcrumb,
                'isWHMCS6' => version_compare($GLOBALS['CONFIG']['Version'], '6.0.0', '>=')
            ];
            try {
                list($content, $success, $error) = self::I()->runControler($page, $action, $input, 'HTML');

                if (self::I()->isDebug()) {
                    $html = '<div style="color: #a94442;background-color: #f2dede;border-color: #dca7a7;font-size:20px;padding:10px;"><strong>Module is under development Mode!!!!!!!!!!!!!!!</strong></div>';
                    $content = $html.$content;
                }
                $vars['content'] = $content;
                $vars['success'] = $success;
                $vars['error'] = $error;
            } catch (\Exception $ex) {
                //self::dump($ex);
                //main\mgLibs\error\Register::register($ex);
                $vars['error'] = $ex->getMessage();
                //if (method_exists($ex, 'getToken')) {
                //    $vars['error'] .= mgLibs\Lang::absoluteT($ex->getMessage());
                //}
            }
        } catch (\Exception $ex) {
            //self::dump($ex);
            //main\mgLibs\error\Register::register($ex);
            $vars['error'] = $ex->getMessage();
            //if (method_exists($ex, 'getToken')) {
            //    $vars['error'] .= mgLibs\Lang::absoluteT('token').$ex->getToken();
            //}
        }

        $output['breadcrumb'] = $breadcrumb;
        $output['vars'] = $vars;

        return $output;
    }
    
    public static function cron($input){       
        try{
            self::I()->isAdmin(true);
            //self::I()->setMainLangContext();
            
            self::I()->runControler('Cron','index',$input,'CRON');
        } catch (\Exception $ex) {
            self::dump($ex);
            //main\mgLibs\error\Register::register($ex);
        }
    }
}