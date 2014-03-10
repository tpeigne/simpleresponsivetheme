<?php
/**
 * ResponsiveLinks module for Prestashop, responsivelinks.php
 *
 * Created by Thomas Peigné (thomas.peigne@gmail.com)
 */

if (!defined('_PS_VERSION_'))
    exit;

class ResponsiveLinks extends Module
{
    public $_html = '';

    public function __construct()
    {
        $this->name = 'responsivelinks';
        $this->tab = 'front_office_features';
        $this->version = '2.5';
        $this->author = 'Thomas Peigné';
        $this->need_instance = 0;

        parent::__construct();

        $this->displayName = $this->l('Responsive links block');
        $this->description = $this->l('Adds a block with additional links for your responsive theme');

        include_once($this->local_path.'/classes/ResponsiveLinksClass.php');
    }

    public function install()
    {
        if (!parent::install() OR !$this->registerHook('top')
            OR !$this->registerHook('header') OR !$this->registerHook('footer'))
            return false;

        if (!Db::getInstance()->Execute('
        CREATE TABLE `'._DB_PREFIX_.'responsivelinks` (
        `id_responsivelinks` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
        `position` int(10) UNSIGNED NOT NULL,
        `id_category` int(10) UNSIGNED NOT NULL,
        `id_cms` int(10) UNSIGNED NOT NULL,
        `id_cms_category` int(10) UNSIGNED NOT NULL,
        `id_product` int(10) UNSIGNED NOT NULL,
        `id_parent` int(10) UNSIGNED NOT NULL,
        `page_category` VARCHAR(50) NOT NULL,
        `page_category_column` int(10) UNSIGNED NOT NULL,
        `date_add` datetime NOT NULL,
        `date_upd` datetime NOT NULL,
        PRIMARY KEY (`id_responsivelinks`))
        ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8'))
            return false;

        if (!Db::getInstance()->Execute('
        CREATE TABLE `'._DB_PREFIX_.'responsivelinks_lang` (
        `id_responsivelinks` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
        `id_lang` int(10) UNSIGNED NOT NULL,
        `title` VARCHAR(255) NOT NULL,
        `url` VARCHAR(255) NOT NULL,
        PRIMARY KEY (`id_responsivelinks`, `id_lang`))
        ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8'))
            return false;

        //responsive links configuration array
        $responsiveLinksConfiguration = array();

        //basic configuration
        $responsiveLinksConfiguration['FOLLOWFACEBOOK'] = array(
            'option' => 1,
            'value'  => 'http://www.facebook.com'
        );
        $responsiveLinksConfiguration['FOLLOWYOUTUBE'] = array(
            'option' => 1,
            'value'  => 'http://www.youtube.com'
        );
        $responsiveLinksConfiguration['FOLLOWTWITTER'] = array(
            'option' => 1,
            'value'  => 'http://www.twitter.com'
        );
        $responsiveLinksConfiguration['FOLLOWLINKEDIN'] = array(
            'option' => 1,
            'value'  => 'http://www.linkedin.com'
        );

        //configuration update
        if (!Configuration::updateValue('RESPONSIVELINKS_CONFIGURATION', serialize($responsiveLinksConfiguration)))
            return false;

        $this->installDemoLinks();

        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall())
            return false;

        if (!Db::getInstance()->Execute('DROP TABLE `'._DB_PREFIX_.'responsivelinks`'))
            return false;

        if (!Db::getInstance()->Execute('DROP TABLE `'._DB_PREFIX_.'responsivelinks_lang`'))
            return false;

        Configuration::deleteByName('RESPONSIVELINKS_CONFIGURATION');

        return true;
    }

    public function getContent()
    {
        $this->_html = '<h2>'.$this->displayName.'</h2>';
        $this->session();
        $this->displaySessionMessage();

        // Check if we are deleting the link
        if (Tools::getIsset('action') && Tools::getValue('action') == 'delete') {
            $link = new ResponsiveLinksClass((int)Tools::getValue('id'));

            // Delete all the sub links for this link
            if (!$link->deleteSubLinks()) {
                $_SESSION[$this->name]['message'] = $this->l('An error has occurred while deleting sublinks of the link');
                $_SESSION[$this->name]['type'] = 'error';

                Tools::redirectAdmin($this->getPageUrl());
            } else {
                // After deleting sublinks, we delete the link
                if ($link->delete()) {
                    $_SESSION[$this->name]['message'] = $this->l('The link has been deleted');
                    $_SESSION[$this->name]['type'] = 'confirm';

                    Tools::redirectAdmin($this->getPageUrl());
                } else {
                    $_SESSION[$this->name]['message'] = $this->l('An error has occurred while deleting the link');
                    $_SESSION[$this->name]['type'] = 'error';

                    Tools::redirectAdmin($this->getPageUrl());
                }
            }
        }

        // Check if we are editing the link
        if (Tools::isSubmit('editLink')) {
            $linkResponsive = new ResponsiveLinksClass(Tools::getValue('id'));
            $linkResponsive->copyFromPost();

            if ((int)Tools::getValue('iscategory') == 1) {
                $linkResponsive->id_category = (int)$_POST['category'];
            }

            if ((int)Tools::getValue('iscms') == 1) {
                $linkResponsive->id_cms = (int)$_POST['cms'];
            }

            if ((int)Tools::getValue('iscmscategory') == 1) {
                $linkResponsive->id_cms_category = (int)$_POST['cmscategory'];
            }

            if ((int)Tools::getValue('isproduct') == 1) {
                $linkResponsive->id_product = (int)$_POST['product'];
            }

            if ((int)Tools::getValue('isparent') == 1) {
                $linkResponsive->id_parent = (int)$_POST['parent'];
            }

            if ($linkResponsive->save()) {
                $_SESSION[$this->name]['message'] = $this->l('The link has been updated');
                $_SESSION[$this->name]['type'] = 'confirm';

                Tools::redirectAdmin($this->getPageUrl());
            } else {
                $_SESSION[$this->name]['message'] = $this->l('An error has occurred during the update of the link');
                $_SESSION[$this->name]['type'] = 'error';

                Tools::redirectAdmin($this->getPageUrl());
            }
        }

        // Check if we are adding the link
        if (Tools::isSubmit('addLink')) {
            $linkResponsive = new ResponsiveLinksClass();
            $linkResponsive->copyFromPost();
            $linkResponsive->position = ResponsiveLinksClass::getMaxPosition();

            if ((int)Tools::getValue('iscategory') == 1) {
                $linkResponsive->id_category = (int)$_POST['category'];
            }

            if ((int)Tools::getValue('iscms') == 1) {
                $linkResponsive->id_cms = (int)$_POST['cms'];
            }

            if ((int)Tools::getValue('iscmscategory') == 1) {
                $linkResponsive->id_cms_category = (int)$_POST['cmscategory'];
            }

            if ((int)Tools::getValue('isproduct') == 1) {
                $linkResponsive->id_product = (int)$_POST['product'];
            }

            if ((int)Tools::getValue('isparent') == 1) {
                $linkResponsive->id_parent = (int)$_POST['parent'];
            }

            if ($linkResponsive->save()) {
                $_SESSION[$this->name]['message'] = $this->l('The link has been added');
                $_SESSION[$this->name]['type'] = 'confirm';

                Tools::redirectAdmin($this->getPageUrl());
            } else {
                $_SESSION[$this->name]['message'] = $this->l('An error has occurred during the addition of the link');
                $_SESSION[$this->name]['type'] = 'error';

                Tools::redirectAdmin($this->getPageUrl());
            }
        }

        // Check if we are editing the follow links
        if (Tools::isSubmit('editFollow')) {
            //responsive links configuration array
            $responsiveLinksConfiguration = array();
            
            $responsiveLinksConfiguration['FOLLOWFACEBOOK'] = array(
                'option' => (int)Tools::getValue('isfacebook'),
                'value'  => Tools::getValue('facebookcontent')
            );
            $responsiveLinksConfiguration['FOLLOWYOUTUBE'] = array(
                'option' => (int)Tools::getValue('isyoutube'),
                'value'  => Tools::getValue('youtubecontent')
            );
            $responsiveLinksConfiguration['FOLLOWTWITTER'] = array(
                'option' => (int)Tools::getValue('istwitter'),
                'value'  => Tools::getValue('twittercontent')
            );
            $responsiveLinksConfiguration['FOLLOWLINKEDIN'] = array(
                'option' => (int)Tools::getValue('islinkedin'),
                'value'  => Tools::getValue('linkedincontent')
            );

            //configuration update
            if (Configuration::updateValue('RESPONSIVELINKS_CONFIGURATION', serialize($responsiveLinksConfiguration))) {
                $_SESSION[$this->name]['message'] = $this->l('Follow links configuration has been updated');
                $_SESSION[$this->name]['type'] = 'confirm';

                Tools::redirectAdmin($this->getPageUrl());
            } else {
                $_SESSION[$this->name]['message'] = $this->l('An error has occurred during the update of follow links');
                $_SESSION[$this->name]['type'] = 'error';

                Tools::redirectAdmin($this->getPageUrl());
            }
        }

        $this->_displayForm();

        return $this->_html;
    }

    private function _displayForm()
    {
        $responsiveLinksConfiguration = unserialize(Configuration::get('RESPONSIVELINKS_CONFIGURATION'));
        $responsiveLink = null;
        $category = null;
        $cms = null;
        $product = null;
        $custom = null;

        if (Tools::getIsset('action') && Tools::getValue('action') == 'edit') {
            $responsiveLink = new ResponsiveLinksClass((int)Tools::getValue('id'));

            if($responsiveLink->id_category <> 0)
                $category = new Category((int)$responsiveLink->id_category, $this->context->cookie->id_lang);
            if($responsiveLink->id_cms <> 0)
                $cms = new CMS((int)$responsiveLink->id_cms, $this->context->cookie->id_lang);
            if($responsiveLink->id_cms_category <> 0)
                $cmsCategory = new CMSCategory((int)$responsiveLink->id_cms_category, $this->context->cookie->id_lang);
            if($responsiveLink->id_product <> 0)
                $product = new Product((int)$responsiveLink->id_product, $this->context->cookie->id_lang);
            if($responsiveLink->id_category == 0 && $responsiveLink->id_cms == 0 && $responsiveLink->id_cms_category == 0 && $responsiveLink->id_product == 0)
                $custom = true;
        }

        /* Languages preliminaries */
        $defaultLanguage = (int)(Configuration::get('PS_LANG_DEFAULT'));
        $languages = Language::getLanguages(false);
        $divLangName = 'title¤url';

        $this->_html .= '
        <script type="text/javascript">
            var urlAjaxModule = "'._PS_BASE_URL_.$this->_path.'ajax.php";
        </script>
        <link type="text/css" rel="stylesheet" href="'.$this->_path.'../responsiveextension/stylesheets/admin-common.css" />
        <link type="text/css" rel="stylesheet" href="'.$this->_path.'stylesheets/jquery.treeTable.css" />
        <link type="text/css" rel="stylesheet" href="'.$this->_path.'stylesheets/responsivelinks.css" />

        <script type="text/javascript" src="'.$this->_path.'../responsiveextension/javascripts/admin-common.js"></script>
        <script type="text/javascript" src="'._PS_JS_DIR_.'jquery/plugins/jquery.tablednd.js"></script>
        <script type="text/javascript" src="'.$this->_path.'javascripts/jquery.treeTable.js"></script>
        <script type="text/javascript" src="'.$this->_path.'javascripts/responsivelinks.js"></script>
        <script type="text/javascript">id_language = Number('.$defaultLanguage.');</script>';

        if(isset($responsiveLink)) {
            $this->_html .= '
            <a href="'.$this->getPageUrl(array('action=newlink')).'" class="button"><span>'.$this->l('New link').'</span></a>
            <form id="link-form" style="display:block;"';
        } else {
            $this->_html .= '
            <button class="button dropdown" section="link-form"><span>'.$this->l('Add a link').'</span></button>
            <form id="link-form" '.((Tools::getIsset('action') && Tools::getValue('action') == 'newlink') ? 'style="display:block;"' : '' ).'';
        }

        $this->_html .= '
            class="dropdown-content" action="'.$this->getPageUrl().'" method="post" enctype="multipart/form-data">
            <fieldset>
                <legend><img src="../img/admin/information.png" class="middle"> '.$this->l('Link configuration').'</legend>
                ';

        // Where the link will be displayed
        $this->_html .= '
            <div class="page_category step-1'.(isset($responsiveLink) ? ' hidden' : '' ).'">
                <h3>'.$this->l('Step 1 : Choose where you want to display the link').'.</h3>
                <div>
                    <label for="page_category">'.$this->l('Choose link location').' : </label>
                    <div class="margin-form">
                        <select name="page_category" id="page_category">
                            <option value="header" '.((isset($responsiveLink) && $responsiveLink->page_category == 'header') ? 'selected="selected"' : '' ).'>'.$this->l('Header').'</option>
                            <option value="footer" '.((isset($responsiveLink) && $responsiveLink->page_category == 'footer') ? 'selected="selected"' : '' ).'>'.$this->l('Footer').'</option>
                        </select>
                    </div>
                </div>';

        // If footer is the choice, select the column
        $this->_html .= '
                <div class="hide page_category_column_choice">
                    <label for="page_category_column">'.$this->l('Choose your column').' : </label>
                    <div class="margin-form">
                        <select name="page_category_column" id="page_category_column">
                            <option value="1" '.((isset($responsiveLink) && $responsiveLink->page_category_column == '1') ? 'selected="selected"' : '' ).'>'.$this->l('Browse').'</option>
                            <option value="2" '.((isset($responsiveLink) && $responsiveLink->page_category_column == '2') ? 'selected="selected"' : '' ).'>'.$this->l('Site info').'</option>
                        </select>
                    </div>
                </div>
            </div>';

        // parent ou non
        $this->_html .= '
            <div class="link-type'.(isset($responsiveLink) ? ' link-type-edition' : '' ).'">
                <div class="option-parent step-2'.(isset($responsiveLink) ? ' hidden' : '' ).'">
                <h3>'.$this->l('Step 2 : Choose is the link has a parent or not').'.</h3>
                    <div class="option">
                        <label for="isparent">'.$this->l('Has a parent link?').'</label>
                        <div class="margin-form">
                            <input type="radio" name="isparent" class="link-option" value="1">
                            <label class="t"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Yes').'" title="'.$this->l('Yes').'"></label>
                            <input type="radio" name="isparent" class="link-option" value="0" checked="checked">
                            <label class="t"> <img src="../img/admin/disabled.gif" alt="'.$this->l('No').'" title="'.$this->l('No').'"></label>
                        </div>
                    </div>

                    <div class="option-block">
                        <label for="parent">'.$this->l('Choose your Parent link :').'</label>
                        <div class="margin-form">
                            <select name="parent" id="parent" size="5">';
        /** @var $parentTemp ResponsiveLinksClass */
        foreach(ResponsiveLinksClass::findAll($this->context->cookie->id_lang, false) as $parentTemp)
        {
            $parentCategory = null;
            $parentCms = null;
            $parentProduct = null;

            if ($parentTemp->id_category <> 0) {
                $parentCategory = new Category((int)$parentTemp->id_category, $this->context->cookie->id_lang);
                $this->_html .= '<option value="'.$parentTemp->id.'">'.$parentTemp->id.' - '.$parentCategory->name.'</option>';
            } elseif($parentTemp->id_cms <> 0) {
                $parentCms = new CMS((int)$parentTemp->id_cms, $this->context->cookie->id_lang);
                $this->_html .= '<option value="'.$parentTemp->id.'">'.$parentTemp->id.' - '.$parentCms->meta_title.'</option>';
            } elseif($parentTemp->id_cms_category <> 0) {
                $parentCmsCategory = new CMSCategory((int)$parentTemp->id_cms_category, $this->context->cookie->id_lang);
                $this->_html .= '<option value="'.$parentTemp->id.'">'.$parentTemp->id.' - '.$parentCmsCategory->name.'</option>';
            } elseif($parentTemp->id_product <> 0) {
                $parentProduct = new Product((int)$parentTemp->id_product, false, $this->context->cookie->id_lang);
                $this->_html .= '<option value="'.$parentTemp->id.'">'.$parentTemp->id.' - '.$parentProduct->name.'</option>';
            } else {
                $this->_html .= '<option value="'.$parentTemp->id.'">'.$parentTemp->id.' - '.$parentTemp->title.'</option>';
            }
        }

        $this->_html .= '
                            </select>
                            <p class="clear">'.$this->l('Choose a parent link for your link and a dropdown will appear in the nav bar for the parent').'.</p>
                        </div>
                    </div>
                </div>';

        // category ou non
        $this->_html .= '
                <div class="step-3">
                    <h3 class="'.(isset($responsiveLink) ? 'hidden' : '' ).'">'.$this->l('Step 3 : Choose your link type').'.</h3>
                    <div class="option '.(isset($responsiveLink) ? 'hidden' : '' ).'">
                        <label for="iscategory">'.$this->l('Is a category link?').'</label>
                        <div class="margin-form">
                            <input type="radio" name="iscategory" class="link-choice" value="1" '.(isset($category) ? 'checked="checked"' : '' ).'>
                            <label class="t"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Yes').'" title="'.$this->l('Yes').'"></label>
                            <input type="radio" name="iscategory" class="link-choice" value="0" '.(isset($category) ? '' : 'checked="checked"' ).'>
                            <label class="t"> <img src="../img/admin/disabled.gif" alt="'.$this->l('No').'" title="'.$this->l('No').'"></label>
                        </div>
                    </div>

                    <div class="option-block '.(isset($category) ? 'active' : '' ).'">
                        <label for="category">'.$this->l('Choose your category page :').'</label>
                        <div class="margin-form">
                            <select name="category" id="category" size="5">';
        foreach(Category::getSimpleCategories($this->context->cookie->id_lang) as $categoryTemp)
        {
            $this->_html .= '
                                <option value="'.$categoryTemp['id_category'].'" '.(isset($category) && $category->id == $categoryTemp['id_category'] ? 'selected="selected"' : '').'>'.$categoryTemp['name'].'</option>';
        }

        $this->_html .= '
                            </select>
                        </div>
                    </div>';

        // cms ou non
        $this->_html .= '
                    <div class="option '.(isset($responsiveLink) ? 'hidden' : '' ).'">
                        <label for="iscms">'.$this->l('Is a CMS link?').'</label>
                        <div class="margin-form">
                            <input type="radio" name="iscms" class="link-choice" value="1" '.(isset($cms) ? 'checked="checked"' : '' ).'>
                            <label class="t"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Yes').'" title="'.$this->l('Yes').'"></label>
                            <input type="radio" name="iscms" class="link-choice" value="0" '.(isset($cms) ? '' : 'checked="checked"' ).'>
                            <label class="t"> <img src="../img/admin/disabled.gif" alt="'.$this->l('No').'" title="'.$this->l('No').'"></label>
                        </div>
                    </div>

                    <div class="option-block '.(isset($cms) ? 'active' : '' ).'">
                        <label for="cms">'.$this->l('Choose your CMS page :').'</label>
                        <div class="margin-form">
                            <select name="cms" id="cms"  size="5">';
        foreach(CMS::listCms($this->context->cookie->id_lang) as $cmsTemp)
        {
            $this->_html .= '
                                <option value="'.$cmsTemp['id_cms'].'" '.(isset($cms) && $cms->id == $cmsTemp['id_cms'] ? 'selected="selected"' : '').'>'.$cmsTemp['meta_title'].'</option>';
        }

        $this->_html .= '
                            </select>
                        </div>
                    </div>';

        // cms category or not
        $this->_html .= '
                    <div class="option '.(isset($responsiveLink) ? 'hidden' : '' ).'">
                        <label for="iscmscategory">'.$this->l('Is a CMS category link?').'</label>
                        <div class="margin-form">
                            <input type="radio" name="iscmscategory" class="link-choice" value="1" '.(isset($cmsCategory) ? 'checked="checked"' : '' ).'>
                            <label class="t"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Yes').'" title="'.$this->l('Yes').'"></label>
                            <input type="radio" name="iscmscategory" class="link-choice" value="0" '.(isset($cmsCategory) ? '' : 'checked="checked"' ).'>
                            <label class="t"> <img src="../img/admin/disabled.gif" alt="'.$this->l('No').'" title="'.$this->l('No').'"></label>
                        </div>
                    </div>

                    <div class="option-block '.(isset($cmsCategory) ? 'active' : '' ).'">
                        <label for="cms">'.$this->l('Choose your CMS Category page :').'</label>
                        <div class="margin-form">
                            <select name="cmscategory" id="cmscategory" size="5">';
        foreach(CMSCategory::getSimpleCategories($this->context->cookie->id_lang) as $cmsCategoryTemp)
        {
            $this->_html .= '
                                <option value="'.$cmsCategoryTemp['id_cms_category'].'" '.(isset($cmsCategory) && $cmsCategory->id == $cmsCategoryTemp['id_cms_category'] ? 'selected="selected"' : '').'>'.$cmsCategoryTemp['name'].'</option>';
        }

        $this->_html .= '
                            </select>
                        </div>
                    </div>';

        // product ou non
        $this->_html .= '
                    <div class="option '.(isset($responsiveLink) ? 'hidden' : '' ).'">
                        <label for="isproduct">'.$this->l('Is a product link?').'</label>
                        <div class="margin-form">
                            <input type="radio" name="isproduct" class="link-choice" value="1" '.(isset($product) ? 'checked="checked"' : '' ).'>
                            <label class="t"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Yes').'" title="'.$this->l('Yes').'"></label>
                            <input type="radio" name="isproduct" class="link-choice" value="0" '.(isset($product) ? '' : 'checked="checked"' ).'>
                            <label class="t"> <img src="../img/admin/disabled.gif" alt="'.$this->l('No').'" title="'.$this->l('No').'"></label>
                        </div>
                    </div>

                    <div class="option-block '.(isset($product) ? 'active' : '' ).'">
                        <label for="product">'.$this->l('Choose your product page :').'</label>
                        <div class="margin-form">
                            <input type="text" id="product_auto" name="product_auto" size="50"/>
                            <input type="hidden" id="product" name="product" />
                            <p class="clear">'.$this->l('Type a word to search products').'.</p>
                        </div>
                    </div>';

        // custom ou non
        $this->_html .= '
                    <div class="option '.(isset($responsiveLink) ? 'hidden' : '' ).'">
                        <label for="iscustom">'.$this->l('Is a custom link?').'</label>
                        <div class="margin-form">
                            <input type="radio" name="iscustom" class="link-choice" value="1" '.(isset($custom) ? 'checked="checked"' : '' ).'>
                            <label class="t"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Yes').'" title="'.$this->l('Yes').'"></label>
                            <input type="radio" name="iscustom" class="link-choice" value="0" '.(isset($custom) ? '' : 'checked="checked"' ).'>
                            <label class="t"> <img src="../img/admin/disabled.gif" alt="'.$this->l('No').'" title="'.$this->l('No').'"></label>
                        </div>
                    </div>';

        //title field
        $this->_html .= '
                    <div class="option-block '.(isset($custom) ? 'active' : '' ).'">
                        <label>'.$this->l('Title :').'</label>
                        <div class="margin-form">';
        foreach ($languages as $language)
        {
            $this->_html .= '
                            <div id="title_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';float: left;">
                                <input class="required" type="text" name="title_'.$language['id_lang'].'" id="title_'.$language['id_lang'].'" size="35" value="'.(isset($responsiveLink->title[$language['id_lang']]) ? $responsiveLink->title[$language['id_lang']] : '').'" />
                            </div>';
        }
        $this->_html .= $this->displayFlags($languages, $defaultLanguage, $divLangName, 'title', true);
        $this->_html .= '
                            <p class="clear">'.$this->l('Link title').'.</p>
                        </div>';

        //champ url
        $this->_html .= '
                        <label>'.$this->l('Url :').'</label>
                        <div class="margin-form">';

        foreach ($languages as $language)
        {
            $this->_html .= '
                            <div id="url_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';float: left;">
                                <input class="required" type="text" name="url_'.$language['id_lang'].'" id="url_'.$language['id_lang'].'" size="70" value="'.(isset($responsiveLink->url[$language['id_lang']]) ? $responsiveLink->url[$language['id_lang']] : '').'" />
                            </div>';
        }
        $this->_html .= $this->displayFlags($languages, $defaultLanguage, $divLangName, 'url', true);
        $this->_html .= '
                            <p class="clear">'.$this->l('Url of the link (leave blank is no url)').'.</p>
                        </div>
                        </div>';

        $this->_html .= '
                        <div class="margin-form">';
        if(Tools::getIsset('action') && Tools::getIsset('action') == 'edit')
            $this->_html .= '<input type="submit" value="'.$this->l('Save').'" name="editLink" class="button">
                                    <input type="hidden" value="'.$responsiveLink->id.'" name="id" class="button">';
        else
            $this->_html .= '<input type="submit" value="'.$this->l('Save').'" name="addLink" class="button">';

        $this->_html .= '
                    </div>
                </div>
                </div>
            </fieldset>
        </form>';

        $this->_html .= '
        <fieldset>
            <legend><img src="../img/admin/tab-preferences.gif" class="middle"> '.$this->l('Nav bar links').'</legend>
            <p>'.$this->l('Edit your main nav links with the edit button and save it.').'</p>
            <hr>
            <table id="links" class="table tableDnD updatePosition" cellpadding="0" cellspacing="0" style="width: 100%;">
                <thead>
                    <tr>
                        <th class="center">'.$this->l('Expand').'</th>
                        <th class="center">'.$this->l('Id').'</th>
                        <th class="center">'.$this->l('Position').'</th>
                        <th>'.$this->l('Title').'</th>
                        <th class="center">'.$this->l('Actions').'</th>
                    </tr>
                </thead>
                <tbody>';

        /** @var $responsiveLink ResponsiveLinksClass */
        foreach(ResponsiveLinksClass::findAll($this->context->cookie->id_lang, true) as $responsiveLink)
        {
            $category = null;
            $cms = null;
            $product = null;

            if ($responsiveLink->id_category <> 0) {
                $category = new Category((int)$responsiveLink->id_category, $this->context->cookie->id_lang);
            } elseif($responsiveLink->id_cms <> 0) {
                $cms = new CMS((int)$responsiveLink->id_cms, $this->context->cookie->id_lang);
            } elseif($responsiveLink->id_cms_category <> 0) {
                $cmsCategory = new CMSCategory((int)$responsiveLink->id_cms_category, $this->context->cookie->id_lang);
            } elseif($responsiveLink->id_product <> 0) {
                $product = new Product((int)$responsiveLink->id_product, false, $this->context->cookie->id_lang);
            }

            $this->_html .= '
                    <tr id="node-'.$responsiveLink->id.'">
                        <td class="center"><img src="'.$this->_path.'img/folder.png" alt="" /></td>
                        <td class="center">'.$responsiveLink->id.'</td>
                        <td class="center position"></td>
                        <td>';

            if (isset($category)) {
                $this->_html .= $category->name;
            } elseif(isset($cms)) {
                $this->_html .= $cms->meta_title;
            } elseif(isset($cmsCategory)) {
                $this->_html .= $cmsCategory->name;
            } elseif(isset($product)) {
                $this->_html .= $product->name;
            } else {
                $this->_html .= $responsiveLink->title;
            }

            $this->_html .= '
                        </td>
                        <td class="center">
                            <a href="'.$this->getPageUrl(array('id='.$responsiveLink->id, 'action=edit')).'" title="'.$this->l('Edit').'">
                                <img src="../img/admin/edit.gif" alt="'.$this->l('Edit').'" alt="'.$this->l('Edit').'">
                            </a>
                            <a class="delete" href="'.$this->getPageUrl(array('id='.$responsiveLink->id, 'action=delete')).'" id="'.$responsiveLink->id.'" title="'.$this->l('Delete the link ?').'">
                                <img src="../img/admin/delete.gif" alt="'.$this->l('Delete').'" alt="'.$this->l('Delete').'">
                            </a>
                        </td>
                    </tr>';

            $this->getSubLinks($responsiveLink->id, $this->context->cookie->id_lang);
        }

        $this->_html .= '
                </tbody>
            </table>
        </fieldset>

        <fieldset>
            <legend><img src="../img/admin/tab-preferences.gif" class="middle"> '.$this->l('Footer links').'</legend>
            <p>'.$this->l('Edit your footer links and save it').'.</p>
            <hr>
            <h3>'.$this->l('Browse column').'</h3>
            <table id="footer-browse-links" class="table tableDnD" cellpadding="0" cellspacing="0" style="width: 100%;">
                <thead>
                    <tr>
                        <th class="center">'.$this->l('Id').'</th>
                        <th class="center">'.$this->l('Position').'</th>
                        <th>'.$this->l('Title').'</th>
                        <th class="center">'.$this->l('Actions').'</th>
                    </tr>
                </thead>
                <tbody>';

        /** @var $responsiveLink ResponsiveLinksClass */
        foreach(ResponsiveLinksClass::findAll($this->context->cookie->id_lang, true, 'footer', 1) as $responsiveLink)
        {
            $category = null;
            $cms = null;
            $product = null;

            if($responsiveLink->id_category <> 0)
                $category = new Category((int)$responsiveLink->id_category, $this->context->cookie->id_lang);
            elseif($responsiveLink->id_cms <> 0)
                $cms = new CMS((int)$responsiveLink->id_cms, $this->context->cookie->id_lang);
            elseif($responsiveLink->id_cms_category <> 0)
                $cmsCategory = new CMSCategory((int)$responsiveLink->id_cms_category, $this->context->cookie->id_lang);
            elseif($responsiveLink->id_product <> 0)
                $product = new Product((int)$responsiveLink->id_product, false, $this->context->cookie->id_lang);

            $this->_html .= '
                    <tr id="node-'.$responsiveLink->id.'">
                        <td class="center">'.$responsiveLink->id.'</td>
                        <td class="center position"></td>
                        <td>';
            if (isset($category)) {
                $this->_html .= $category->name;
            } elseif(isset($cms)) {
                $this->_html .= $cms->meta_title;
            } elseif(isset($cmsCategory)) {
                $this->_html .= $cmsCategory->name;
            } elseif(isset($product)) {
                $this->_html .= $product->name;
            } else {
                $this->_html .= $responsiveLink->title;
            }

            $this->_html .= '
                        </td>
                        <td class="center">
                            <a href="'.$this->getPageUrl(array('id='.$responsiveLink->id, 'action=edit')).'" title="'.$this->l('Edit').'">
                                <img src="../img/admin/edit.gif" alt="'.$this->l('Edit').'" alt="'.$this->l('Edit').'">
                            </a>
                            <a class="delete" href="'.$this->getPageUrl(array('id='.$responsiveLink->id, 'action=delete')).'" id="'.$responsiveLink->id.'" title="'.$this->l('Delete the link ?').'">
                                <img src="../img/admin/delete.gif" alt="'.$this->l('Delete').'" alt="'.$this->l('Delete').'">
                            </a>
                        </td>
                    </tr>';
        }

        $this->_html .= '
                </tbody>
            </table>
            <h3 style="margin-top: 10px;">'.$this->l('Site info column').'</h3>
            <table id="footer-siteinfo-links" class="table tableDnD" cellpadding="0" cellspacing="0" style="width: 100%;">
                <thead>
                    <tr>
                        <th class="center">'.$this->l('Id').'</th>
                        <th class="center">'.$this->l('Position').'</th>
                        <th>'.$this->l('Title').'</th>
                        <th class="center">'.$this->l('Actions').'</th>
                    </tr>
                </thead>
                <tbody>';

        /** @var $responsiveLink ResponsiveLinksClass */
        foreach(ResponsiveLinksClass::findAll($this->context->cookie->id_lang, true, 'footer', 2) as $responsiveLink)
        {
            $category = null;
            $cms = null;
            $product = null;

            if ($responsiveLink->id_category <> 0) {
                $category = new Category((int)$responsiveLink->id_category, $this->context->cookie->id_lang);
            } elseif($responsiveLink->id_cms <> 0) {
                $cms = new CMS((int)$responsiveLink->id_cms, $this->context->cookie->id_lang);
            } elseif($responsiveLink->id_cms_category <> 0) {
                $cmsCategory = new CMSCategory((int)$responsiveLink->id_cms_category, $this->context->cookie->id_lang);
            } elseif($responsiveLink->id_product <> 0) {
                $product = new Product((int)$responsiveLink->id_product, false, $this->context->cookie->id_lang);
            }

            $this->_html .= '
                    <tr id="node-'.$responsiveLink->id.'">
                        <td class="center">'.$responsiveLink->id.'</td>
                        <td class="center position"></td>
                        <td>';
            if (isset($category)) {
                $this->_html .= $category->name;
            } elseif(isset($cms)) {
                $this->_html .= $cms->meta_title;
            } elseif(isset($cmsCategory)) {
                $this->_html .= $cmsCategory->name;
            } elseif(isset($product)) {
                $this->_html .= $product->name;
            } else {
                $this->_html .= $responsiveLink->title;
            }

            $this->_html .= '
                        </td>
                        <td class="center">
                            <a href="'.$this->getPageUrl(array('id='.$responsiveLink->id, 'action=edit')).'" title="'.$this->l('Edit').'">
                                <img src="../img/admin/edit.gif" alt="'.$this->l('Edit').'" alt="'.$this->l('Edit').'">
                            </a>
                            <a class="delete" href="'.$this->getPageUrl(array('id='.$responsiveLink->id, 'action=delete')).'" id="'.$responsiveLink->id.'" title="'.$this->l('Delete the link ?').'">
                                <img src="../img/admin/delete.gif" alt="'.$this->l('Delete').'" alt="'.$this->l('Delete').'">
                            </a>
                        </td>
                    </tr>';
        }

        $this->_html .= '
                </tbody>
            </table>
        </fieldset>

        <form action="'.$this->getPageUrl().'" method="post" enctype="multipart/form-data">
        <fieldset class="follow-type">
            <legend><img src="../img/admin/tab-preferences.gif" class="middle"> '.$this->l('Follow us links').'</legend>
            <p>'.$this->l('Edit your social links and save it').'.</p>
            <hr>';

        // cms ou non
        $this->_html .= '
            <div class="option">
                <label for="isfacebook">'.$this->l('Facebook').'</label>
                <div class="margin-form">
                    <input type="radio" name="isfacebook" class="link-option" value="1" '.((isset($responsiveLinksConfiguration['FOLLOWFACEBOOK']) && $responsiveLinksConfiguration['FOLLOWFACEBOOK']['option'] == 1) ? 'checked="checked"' : '' ).'>
                    <label class="t"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Yes').'" title="'.$this->l('Yes').'"></label>
                    <input type="radio" name="isfacebook" class="link-option" value="0" '.((!isset($responsiveLinksConfiguration['FOLLOWFACEBOOK']) || $responsiveLinksConfiguration['FOLLOWFACEBOOK']['option'] == 0) ? 'checked="checked"' : '' ).'>
                    <label class="t"> <img src="../img/admin/disabled.gif" alt="'.$this->l('No').'" title="'.$this->l('No').'"></label>
                </div>
            </div>

            <div class="option-block '.((isset($responsiveLinksConfiguration['FOLLOWFACEBOOK']) && $responsiveLinksConfiguration['FOLLOWFACEBOOK']['option'] == 1) ? 'active' : '' ).'">
                <label for="facebookcontent">'.$this->l('Facebook page').' : </label>
                <div class="margin-form">
                    <input type="text" name="facebookcontent" size="150" value="'.$responsiveLinksConfiguration['FOLLOWFACEBOOK']['value'].'"/>
                    <p class="clear">'.$this->l('Enter your Facebook page').'</p>
                </div>
            </div>

            <div class="option">
                <label for="isyoutube">'.$this->l('Youtube').' : </label>
                <div class="margin-form">
                    <input type="radio" name="isyoutube" class="link-option" value="1" '.((isset($responsiveLinksConfiguration['FOLLOWYOUTUBE']) && $responsiveLinksConfiguration['FOLLOWYOUTUBE']['option'] == 1) ? 'checked="checked"' : '' ).'>
                    <label class="t"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Yes').'" title="'.$this->l('Yes').'"></label>
                    <input type="radio" name="isyoutube" class="link-option" value="0" '.((!isset($responsiveLinksConfiguration['FOLLOWYOUTUBE']) || $responsiveLinksConfiguration['FOLLOWYOUTUBE']['option'] == 0) ? 'checked="checked"' : '' ).'>
                    <label class="t"> <img src="../img/admin/disabled.gif" alt="'.$this->l('No').'" title="'.$this->l('No').'"></label>
                </div>
            </div>

            <div class="option-block '.((isset($responsiveLinksConfiguration['FOLLOWYOUTUBE']) && $responsiveLinksConfiguration['FOLLOWYOUTUBE']['option'] == 1) ? 'active' : '' ).'">
                <label for="youtubecontent">'.$this->l('Youtube page ').' : </label>
                <div class="margin-form">
                    <input type="text" name="youtubecontent" size="150" value="'.$responsiveLinksConfiguration['FOLLOWYOUTUBE']['value'].'"/>
                    <p class="clear">'.$this->l('Enter your Youtube page').'</p>
                </div>
            </div>

            <div class="option">
                <label for="istwitter">'.$this->l('Twitter').' : </label>
                <div class="margin-form">
                    <input type="radio" name="istwitter" class="link-option" value="1" '.((isset($responsiveLinksConfiguration['FOLLOWTWITTER']) && $responsiveLinksConfiguration['FOLLOWTWITTER']['option'] == 1) ? 'checked="checked"' : '' ).'>
                    <label class="t"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Yes').'" title="'.$this->l('Yes').'"></label>
                    <input type="radio" name="istwitter" class="link-option" value="0" '.((!isset($responsiveLinksConfiguration['FOLLOWTWITTER']) || $responsiveLinksConfiguration['FOLLOWTWITTER']['option'] == 0) ? 'checked="checked"' : '' ).'>
                    <label class="t"> <img src="../img/admin/disabled.gif" alt="'.$this->l('No').'" title="'.$this->l('No').'"></label>
                </div>
            </div>

            <div class="option-block '.((isset($responsiveLinksConfiguration['FOLLOWTWITTER']) && $responsiveLinksConfiguration['FOLLOWTWITTER']['option'] == 1) ? 'active' : '' ).'">
                <label for="twittercontent">'.$this->l('Twitter page').' : </label>
                <div class="margin-form">
                    <input type="text" name="twittercontent" size="150" value="'.$responsiveLinksConfiguration['FOLLOWTWITTER']['value'].'"/>
                    <p class="clear">'.$this->l('Enter your Twitter page').'</p>
                </div>
            </div>

            <div class="option">
                <label for="islinkedin">'.$this->l('LinkedIn').' : </label>
                <div class="margin-form">
                    <input type="radio" name="islinkedin" class="link-option" value="1" '.((isset($responsiveLinksConfiguration['FOLLOWLINKEDIN']) && $responsiveLinksConfiguration['FOLLOWLINKEDIN']['option'] == 1) ? 'checked="checked"' : '' ).'>
                    <label class="t"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Yes').'" title="'.$this->l('Yes').'"></label>
                    <input type="radio" name="istwitter" class="link-option" value="0" '.((!isset($responsiveLinksConfiguration['FOLLOWLINKEDIN']) || $responsiveLinksConfiguration['FOLLOWLINKEDIN']['option'] == 0) ? 'checked="checked"' : '' ).'>
                    <label class="t"> <img src="../img/admin/disabled.gif" alt="'.$this->l('No').'" title="'.$this->l('No').'"></label>
                </div>
            </div>

            <div class="option-block '.((isset($responsiveLinksConfiguration['FOLLOWLINKEDIN']) && $responsiveLinksConfiguration['FOLLOWLINKEDIN']['option'] == 1) ? 'active' : '' ).'">
                <label for="linkedincontent">'.$this->l('LinkedIn page').' : </label>
                <div class="margin-form">
                    <input type="text" name="linkedincontent" size="150" value="'.$responsiveLinksConfiguration['FOLLOWLINKEDIN']['value'].'"/>
                    <p class="clear">'.$this->l('Enter your LinkedIn page').'</p>
                </div>
            </div>

            <div class="margin-form">
                <input type="submit" value="'.$this->l('Save').'" name="editFollow" class="button">
            </div>
        </fieldset>
        </form>';
    }

    /**
     * Prepare responsive links for administration module
     *
     * @param int $idParent
     * @param int $idLang
     */
    private function getSubLinks($idParent, $idLang)
    {
        /**
         * @var $responsiveSubLink ResponsiveLinksClass
         */
        foreach(ResponsiveLinksClass::findSub($idParent, $idLang) as $responsiveSubLink)
        {
            $category = null;
            $cms = null;
            $product = null;

            if ($responsiveSubLink->id_category <> 0) {
                $category = new Category((int)$responsiveSubLink->id_category, $idLang);
            } elseif($responsiveSubLink->id_cms <> 0) {
                $cms = new CMS((int)$responsiveSubLink->id_cms, $idLang);
            } elseif($responsiveSubLink->id_cms_category <> 0) {
                $cmsCategory = new CMSCategory((int)$responsiveSubLink->id_cms_category, $idLang);
            } elseif($responsiveSubLink->id_product <> 0) {
                $product = new Product((int)$responsiveSubLink->id_product, false, $idLang);
            }

            $this->_html .= '
                <tr id="node-'.$responsiveSubLink->id.'" class="child-of-node-'.$idParent.'">
                    <td class="center"><img src="'.$this->_path.'img/page_white_text.png" alt="" /></td>
                    <td class="center">'.$responsiveSubLink->id.'</td>
                    <td class="center position"></td>
                    <td>';
            if (isset($category)) {
                $this->_html .= $category->name;
            } elseif(isset($cms)) {
                $this->_html .= $cms->meta_title;
            } elseif(isset($cmsCategory)) {
                $this->_html .= $cmsCategory->name;
            } elseif(isset($product)) {
                $this->_html .= $product->name;
            } else {
                $this->_html .= $responsiveSubLink->title;
            }

            $this->_html .= '
                    </td>
                    <td class="center">
                            <a href="'.$this->getPageUrl(array('id='.$responsiveSubLink->id, 'action=edit')).'" title="'.$this->l('Edit').'">
                                <img src="../img/admin/edit.gif" alt="'.$this->l('Edit').'" alt="'.$this->l('Edit').'">
                            </a>
                            <a class="delete" href="'.$this->getPageUrl(array('id='.$responsiveSubLink->id, 'action=delete')).'" id="'.$responsiveSubLink->id.'" title="'.$this->l('Delete the link ?').'">
                                <img src="../img/admin/delete.gif" alt="'.$this->l('Delete').'" alt="'.$this->l('Delete').'">
                            </a>
                        </td>
                </tr>
            ';

            $this->getSubLinks($responsiveSubLink->id, $idLang);
        }
    }

    public function hookTop($params)
    {
        $this->_hookCommon($params);

        $this->context->smarty->assign(
            array(
                'logo_name', Configuration::get('PS_LOGO_NAME'),
                'responsiveLinks' => $this->getLinksForFront(true, true),
                'branche_tpl_path' => $this->local_path.'/views/templates/hook/responsivesublinks.tpl'
            )
        );
        return $this->display(__FILE__, 'responsivelinkstop.tpl');
    }

    /**
     * Prepare responsive links for front office
     *
     * @param bool $getSubLinks if we want sublinks or not
     * @param bool $hasParent if we only want parent links
     * @param int $idParent if we want sub links
     * @param string $pageCategory
     * @param int $pageCategoryColumn
     *
     * @return array
     */
    public function getLinksForFront($getSubLinks = false, $hasParent = false, $idParent = 0, $pageCategory = 'header', $pageCategoryColumn = null)
    {
        $i = 0;
        $links = array();

        if ($getSubLinks) {
            $responsiveLinks = ResponsiveLinksClass::findSub($idParent, $this->context->cookie->id_lang);
        } else {
            $responsiveLinks = ResponsiveLinksClass::findAll(
                $this->context->cookie->id_lang,
                $hasParent,
                $pageCategory,
                $pageCategoryColumn
            );
        }

        if(count($responsiveLinks) > 0) {
            /**
             * @var $responsiveLink ResponsiveLinksClass
             */
            foreach ($responsiveLinks as $responsiveLink)
            {
                $category = null;
                $cms = null;
                $cmsCategory = null;
                $product = null;

                //check if it's a category, a cms page, a cms category or a product
                if($responsiveLink->id_category <> 0){
                    $category = new Category($responsiveLink->id_category, $this->context->cookie->id_lang);
                    $links[$i]['objectLink'] = $category;
                }
                elseif($responsiveLink->id_cms <> 0){
                    $cms = new CMS($responsiveLink->id_cms, $this->context->cookie->id_lang);
                    $links[$i]['objectLink'] = $cms;
                }
                elseif($responsiveLink->id_cms_category <> 0){
                    $cmsCategory = new CMSCategory($responsiveLink->id_cms_category, $this->context->cookie->id_lang);
                    $links[$i]['objectLink'] = $cmsCategory;
                }
                elseif($responsiveLink->id_product <> 0){
                    $product = new Product((int)$responsiveLink->id_product, false, $this->context->cookie->id_lang);
                    $links[$i]['objectLink'] = $product;
                }

                if ($getSubLinks) {
                    $links[$i]['subLinks'] = $this->getLinksForFront(true, true, $responsiveLink->id);
                }
                $links[$i]['responsiveLinkObject'] = $responsiveLink;

                $i++;
            }
        }

        return $links;
    }

    public function hookFooter()
    {
        $this->context->smarty->assign(
            array(
                'responsiveLinksConfiguration' => unserialize(Configuration::get('RESPONSIVELINKS_CONFIGURATION')),
                'footerLinks' => $this->getLinksForFront(false, false, 0, 'footer')
            )
        );
        return $this->display(__FILE__, 'responsivelinksfooter.tpl');
    }

    public function hookHeader()
    {
        if (Configuration::get('PS_SEARCH_AJAX'))
        {
            $this->context->controller->addCSS(_PS_JS_DIR_.'jquery/plugins/autocomplete/jquery.autocomplete.css');
            $this->context->controller->addJS(_PS_JS_DIR_.'jquery/plugins/autocomplete/jquery.autocomplete.js');
        }

        $this->context->controller->addCSS(($this->_path).'responsivelinks.css', 'all');
    }

    /**
     * _hookAll has to be called in each hookXXX methods. This is made to avoid code duplication.
     *
     * @return bool
     */
    private function _hookCommon()
    {
        $this->context->smarty->assign('ENT_QUOTES', ENT_QUOTES);
        $this->context->smarty->assign('search_ssl', (int)Tools::usingSecureMode());

        $this->context->smarty->assign('ajaxsearch', Configuration::get('PS_SEARCH_AJAX'));
        $this->context->smarty->assign('instantsearch', Configuration::get('PS_INSTANT_SEARCH'));

        return true;
    }


    public function installDemoLinks()
    {
        $languages = Language::getLanguages(true);

        // Header links
        // Home link
        $indexLink = new ResponsiveLinksClass();
        $indexLink->position = 1;
        $indexLink->id_category = 0;
        $indexLink->id_cms = 0;
        $indexLink->id_product = 0;
        $indexLink->id_parent = 0;
        foreach ($languages as $language){
            $indexLink->title[(int)($language['id_lang'])] = 'Home ('.$language['iso_code'].')';
            $indexLink->url[(int)($language['id_lang'])]   = $this->context->link->getPageLink('index', false, (int)($language['id_lang']));
        }

        $indexLink->save();

        //3 : Ipods category
        $i = 2;
        if(Category::categoryExists(3)) {
            //one category link with some products
            $ipodsLink = new ResponsiveLinksClass();
            $ipodsLink->position = 2;
            $ipodsLink->id_category = 3;
            $ipodsLink->id_cms = 0;
            $ipodsLink->id_product = 0;
            $ipodsLink->id_parent = 0;

            $ipodsLink->save();

            //and add some products
            $results = Db::getInstance()->executeS('
                SELECT `id_product`
                FROM `'._DB_PREFIX_.'product`
                WHERE `id_category_default` = 3
                LIMIT 0,3
            ');
            foreach($results as $product) {
                $productLink = new ResponsiveLinksClass();
                $productLink->position = $i;
                $productLink->id_category = 0;
                $productLink->id_cms = 0;
                $productLink->id_product = (int)$product['id_product'];
                $productLink->id_parent = $ipodsLink->id;

                $productLink->save();

                $i++;
            }

            // Accessory link
            if(Category::categoryExists(4)) {
                $accessoryLink = new ResponsiveLinksClass();
                $accessoryLink->position = $i;
                $accessoryLink->id_category = 4;
                $accessoryLink->id_cms = 0;
                $accessoryLink->id_product = 0;
                $accessoryLink->id_parent = $ipodsLink->id;

                $accessoryLink->save();

                //and add some products
                $results = Db::getInstance()->executeS('
                    SELECT `id_product`
                    FROM `'._DB_PREFIX_.'product`
                    WHERE `id_category_default` = 4
                    LIMIT 0,2
                ');
                foreach($results as $product) {
                    $productLink = new ResponsiveLinksClass();
                    $productLink->position = $i;
                    $productLink->id_category = 0;
                    $productLink->id_cms = 0;
                    $productLink->id_product = (int)$product['id_product'];
                    $productLink->id_parent = $accessoryLink->id;

                    $productLink->save();

                    $i++;
                }
            }
        }

        if(Category::categoryExists(5)) {
            //one category link with some products
            $laptopLink = new ResponsiveLinksClass();
            $laptopLink->position = $i;
            $laptopLink->id_category = 5;
            $laptopLink->id_cms = 0;
            $laptopLink->id_product = 0;
            $laptopLink->id_parent = 0;

            $laptopLink->save();
            $i++;

            //and add some products
            $results = Db::getInstance()->executeS('
                SELECT `id_product`
                FROM `'._DB_PREFIX_.'product`
                WHERE `id_category_default` = 5
                LIMIT 0,2
            ');
            foreach($results as $product) {
                $productLink = new ResponsiveLinksClass();
                $productLink->position = $i;
                $productLink->id_category = 0;
                $productLink->id_cms = 0;
                $productLink->id_product = (int)$product['id_product'];
                $productLink->id_parent = $laptopLink->id;

                $productLink->save();

                $i++;
            }
        }

        //second link
        $contactLink = new ResponsiveLinksClass();
        $contactLink->position = $i;
        $contactLink->id_category = 0;
        $contactLink->id_cms = 0;
        $contactLink->id_product = 0;
        $contactLink->id_parent = 0;

        foreach ($languages as $language){
            $contactLink->title[(int)($language['id_lang'])] = 'Contact ('.$language['iso_code'].')';
            $contactLink->url[(int)($language['id_lang'])]   = $this->context->link->getPageLink('contact', false, (int)($language['id_lang']));
        }

        $contactLink->save();

        // Footer links

    }

    /**
     * Init session for the module
     *
     * @return void
     */
    protected function session() {
        if(!session_id()) {
            session_start();
        }

    }

    /**
     * Add to the html message session if they exists
     *
     * @return void
     */
    protected function displaySessionMessage()
    {
        if (isset($_SESSION[$this->name]) && $_SESSION[$this->name]['message'] != '') {
            $this->_html .= '
                <div class="conf '.$_SESSION[$this->name]['type'].'">'.$_SESSION[$this->name]['message'].'</div>
            ';

            $_SESSION[$this->name]['message'] = '';
            $_SESSION[$this->name]['type'] = '';
        }
    }

    /**
     * Generate the page url
     *
     * @param $params array of params
     * @return string
     */
    function getPageUrl($params = array())
    {
        $moduleLink = 'index.php?controller=AdminModules&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&module_name='.$this->name.'';

        if (!empty($params)) {
            $moduleLink .= '&'.implode('&', $params);
        }

        return $moduleLink;
    }
}