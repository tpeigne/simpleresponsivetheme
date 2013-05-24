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
        $this->version = '2.2';
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
        `id_responsivelinks` int(10) unsigned NOT NULL AUTO_INCREMENT,
        `id_shop` int(10) unsigned NOT NULL,
        `id_category` int(10) unsigned NOT NULL,
        `id_cms` int(10) unsigned NOT NULL,
        `id_product` int(10) unsigned NOT NULL,
        `id_parent` int(10) unsigned NOT NULL,
        `id_child` int(10) unsigned NOT NULL,
        `position` int(10) unsigned NOT NULL,
        PRIMARY KEY (`id_responsivelinks`))
        ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8'))
            return false;

        if (!Db::getInstance()->Execute('
        CREATE TABLE `'._DB_PREFIX_.'responsivelinks_lang` (
        `id_responsivelinks` int(10) unsigned NOT NULL AUTO_INCREMENT,
        `id_lang` int(10) unsigned NOT NULL,
        `title` varchar(255) NOT NULL,
        `url` varchar(255) NOT NULL,
        PRIMARY KEY (`id_responsivelinks`, `id_lang`))
        ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8'))
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

        return true;
    }

    public function getContent()
    {

        $this->_html = '<h2>'.$this->displayName.'</h2><div style="display:none;" id="ajax_response"></div>';

        $this->context->customer->firstname;

        /* LINK EDITION */
        if (Tools::isSubmit('submitEditlink'))
        {
            $linkResponsive = new ResponsiveLinksClass(Tools::getValue('idLink'));
            $linkResponsive->copyFromPost();

            //if category
            if((int)Tools::getValue('iscategory') == 1)
            {
                $linkResponsive->id_category = (int)$_POST['category'];
            }

            // if cms
            if((int)Tools::getValue('iscms') == 1)
            {
                $linkResponsive->id_cms = (int)$_POST['cms'];
            }

            // if product
            if((int)Tools::getValue('isproduct') == 1)
            {
                $linkResponsive->id_product = (int)$_POST['product'];
            }

            // if parent
            if((int)Tools::getValue('isparent') == 1)
            {
                $linkResponsive->id_parent = (int)$_POST['parent'];
            }

            if ($linkResponsive->save())
            {
                $this->_html .= '
                <div class="conf confirm">
                    '.$this->l('The link has been updated.').'
                </div>';
            }
            else
            {
                $this->_html .= '
                <div class="conf error">
                    <img src="../img/admin/disabled.gif" alt="" title="" />
                    '.$this->l('An error has occured during the update of the link.').'
                </div>';
            }

            //after the addition, update the parent child id
            if((int)Tools::getValue('isparent') == 1)
            {
                if(!Db::getInstance()->Execute('
                    UPDATE `'._DB_PREFIX_.'responsivelinks`
                    SET `id_child` = '.$linkResponsive->id.'
                    WHERE `id_responsivelinks` = '.$linkResponsive->id_parent.''))
                    return false;
            }
        }
        /* END LINK EDITION */

        /* LINK ADDITION */
        if (Tools::isSubmit('submitAddLink'))
        {
            $linkResponsive = new ResponsiveLinksClass();
            $linkResponsive->copyFromPost();
            $linkResponsive->id_category = 0;
            $linkResponsive->id_cms = 0;
            $linkResponsive->id_product = 0;
            $linkResponsive->id_parent = 0;
            $linkResponsive->id_child = 0;
            $linkResponsive->id_shop = $this->context->shop->id;
            $linkResponsive->position = ResponsiveLinksClass::getMaxPosition();

            //check if category or cms
            //if category
            if((int)Tools::getValue('iscategory') == 1)
            {
                $linkResponsive->id_category = (int)$_POST['category'];
            }

            // if cms
            if((int)Tools::getValue('iscms') == 1)
            {
                $linkResponsive->id_cms = (int)$_POST['cms'];
            }

            // if cms
            if((int)Tools::getValue('isproduct') == 1)
            {
                $linkResponsive->id_product = (int)$_POST['product'];
            }

            // if parent
            if((int)Tools::getValue('isparent') == 1)
            {
                $linkResponsive->id_parent = (int)$_POST['parent'];
            }

            if ($linkResponsive->save())
            {
                $this->_html .= '
                <div class="conf confirm">
                    '.$this->l('The link has been added.').'
                </div>';
            }
            else
            {
                $this->_html .= '
                <div class="conf error">
                    '.$this->l('An error has occured during the addition of the link.').'
                </div>';
            }

            //after the addition, update the parent child id
            if((int)Tools::getValue('isparent') == 1)
            {
                if(!Db::getInstance()->Execute('
                    UPDATE `'._DB_PREFIX_.'responsivelinks`
                    SET `id_child` = '.$linkResponsive->id.'
                    WHERE `id_responsivelinks` = '.$linkResponsive->id_parent.''))
                    return false;
            }
        }
        /* END LINK ADDITION */

        $this->_displayForm();

        return $this->_html;
    }

    private function _displayForm()
    {
        $responsiveLink = null;
        $category = null;
        $cms = null;
        $product = null;
        $custom = null;

        if(Tools::getIsset('action') && Tools::getIsset('action') == 'editLink')
        {
            $responsiveLink = new ResponsiveLinksClass((int)Tools::getValue('idLink'));

            if($responsiveLink->id_category <> 0)
                $category = new Category((int)$responsiveLink->id_category, $this->context->cookie->id_lang);
            if($responsiveLink->id_cms <> 0)
                $cms = new CMS((int)$responsiveLink->id_cms, $this->context->cookie->id_lang);
            if($responsiveLink->id_product <> 0)
                $product = new Product((int)$responsiveLink->id_product, $this->context->cookie->id_lang);
            if($responsiveLink->id_category == 0 && $responsiveLink->id_cms == 0 && $responsiveLink->id_product == 0)
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
        <link type="text/css" rel="stylesheet" href="'.$this->_path.'css/jquery.treeTable.css" />
        <link type="text/css" rel="stylesheet" href="'.$this->_path.'css/responsivelinks.css" />

        <script type="text/javascript" src="'._PS_JS_DIR_.'jquery/plugins/jquery.tablednd.js"></script>
        <script type="text/javascript" src="'.$this->_path.'javascripts/jquery.treeTable.js"></script>
        <script type="text/javascript" src="'.$this->_path.'javascripts/responsivelinks.js"></script>
        <script type="text/javascript">id_language = Number('.$defaultLanguage.');</script>
        <a id="add_link" href=""><img src="../img/admin/add.gif" border="0"> '.$this->l('Add a link').'</a>
        <div class="clear">&nbsp;</div>';

        if(isset($responsiveLink))
            $this->_html .= '
            <form id="informations_link" ';
        else
            $this->_html .= '
            <form id="informations_link" style="display:none;" ';


        $this->_html .= '
            action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" method="post" enctype="multipart/form-data">
            <fieldset style="margin-bottom:10px;">
                <legend><img src="../img/admin/information.png" class="middle"> '.$this->l('Add a new link to your link bar').'</legend>
                <div>';

        // category ou non
        $this->_html .= '
                    <div class="category_option '.(isset($responsiveLink) ? 'hidden' : '' ).'">
                        <label for="iscategory">'.$this->l('Is a category link?').'</label>
                        <div class="margin-form">
                            <input type="radio" name="iscategory" id="iscategory_on" value="1" '.(isset($category) ? 'checked="checked"' : '' ).'>
                            <label class="t"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Yes').'" title="'.$this->l('Yes').'"></label>
                            <input type="radio" name="iscategory" id="iscategory_off" value="0" '.(isset($category) ? '' : 'checked="checked"' ).'>
                            <label class="t"> <img src="../img/admin/disabled.gif" alt="'.$this->l('No').'" title="'.$this->l('No').'"></label>
                        </div>
                    </div>

                    <div class="category_block '.(isset($category) ? 'active' : '' ).'">
                        <label for="category">'.$this->l('Choose your category page :').'</label>
                        <div class="margin-form">
                            <select name="category" id="category">';
        foreach(Category::getSimpleCategories($this->context->cookie->id_lang) as $categoryTemp){
            $this->_html .= '
                                <option value="'.$categoryTemp['id_category'].'" '.(isset($category) && $category->id == $categoryTemp['id_category'] ? 'selected="selected"' : '').'>'.$categoryTemp['name'].'</option>';
        }

        $this->_html .= '
                            </select>
                        </div>
                    </div>';

        // cms ou non
        $this->_html .= '
                    <div class="cms_option '.(isset($responsiveLink) ? 'hidden' : '' ).'">
                        <label for="iscms">'.$this->l('Is a CMS link?').'</label>
                        <div class="margin-form">
                            <input type="radio" name="iscms" id="iscms_on" value="1" '.(isset($cms) ? 'checked="checked"' : '' ).'>
                            <label class="t"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Yes').'" title="'.$this->l('Yes').'"></label>
                            <input type="radio" name="iscms" id="iscms_off" value="0" '.(isset($cms) ? '' : 'checked="checked"' ).'>
                            <label class="t"> <img src="../img/admin/disabled.gif" alt="'.$this->l('No').'" title="'.$this->l('No').'"></label>
                        </div>
                    </div>

                    <div class="cms_block '.(isset($cms) ? 'active' : '' ).'">
                        <label for="cms">'.$this->l('Choose your CMS page :').'</label>
                        <div class="margin-form">
                            <select name="cms" id="cms">';
        foreach(CMS::listCms($this->context->cookie->id_lang) as $cmsTemp){
            $this->_html .= '
                                <option value="'.$cmsTemp['id_cms'].'" '.(isset($cms) && $cms->id == $cmsTemp['id_cms'] ? 'selected="selected"' : '').'>'.$cmsTemp['meta_title'].'</option>';
        }

        $this->_html .= '
                            </select>
                        </div>
                    </div>';

        // product ou non
        $this->_html .= '
                    <div class="product_option '.(isset($responsiveLink) ? 'hidden' : '' ).'">
                        <label for="isproduct">'.$this->l('Is a product link?').'</label>
                        <div class="margin-form">
                            <input type="radio" name="isproduct" id="isproduct_on" value="1" '.(isset($product) ? 'checked="checked"' : '' ).'>
                            <label class="t"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Yes').'" title="'.$this->l('Yes').'"></label>
                            <input type="radio" name="isproduct" id="isproduct_off" value="0" '.(isset($product) ? '' : 'checked="checked"' ).'>
                            <label class="t"> <img src="../img/admin/disabled.gif" alt="'.$this->l('No').'" title="'.$this->l('No').'"></label>
                        </div>
                    </div>

                    <div class="product_block '.(isset($product) ? 'active' : '' ).'">
                        <label for="product">'.$this->l('Choose your product page :').'</label>
                        <div class="margin-form">
                            <select name="product" id="product">';
        foreach(Product::getSimpleProducts($this->context->cookie->id_lang) as $productTemp){
            $this->_html .= '
                                <option value="'.$productTemp['id_product'].'" '.(isset($product) && $product->id == $productTemp['id_product'] ? 'selected="selected"' : '').'>'.$productTemp['name'].'</option>';
        }

        $this->_html .= '
                            </select>
                        </div>
                    </div>';

        // parent ou non
        $this->_html .= '
                    <div class="parent_option '.(isset($responsiveLink) ? 'hidden' : '' ).'">
                        <label for="isparent">'.$this->l('Has a parent link?').'</label>
                        <div class="margin-form">
                            <input type="radio" name="isparent" id="isparent_on" value="1">
                            <label class="t"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Yes').'" title="'.$this->l('Yes').'"></label>
                            <input type="radio" name="isparent" id="isparent_off" value="0" checked="checked">
                            <label class="t"> <img src="../img/admin/disabled.gif" alt="'.$this->l('No').'" title="'.$this->l('No').'"></label>
                        </div>
                    </div>

                    <div class="parent_block">
                        <label for="parent">'.$this->l('Choose your Parent link :').'</label>
                        <div class="margin-form">
                            <select name="parent" id="parent">';
        foreach(ResponsiveLinksClass::findAll($this->context->cookie->id_lang) as $parentTemp){
            $parentCategory = null;
            $parentCms = null;
            $parentProduct = null;

            if($parentTemp->id_category <> 0){
                $parentCategory = new Category((int)$parentTemp->id_category, $this->context->cookie->id_lang);
                $this->_html .= '<option value="'.$parentTemp->id.'">'.$parentTemp->id.' - '.$parentCategory->name.'</option>';
            }
            elseif($parentTemp->id_cms <> 0){
                $parentCms = new CMS((int)$parentTemp->id_cms, $this->context->cookie->id_lang);
                $this->_html .= '<option value="'.$parentTemp->id.'">'.$parentTemp->id.' - '.$parentCms->meta_title.'</option>';
            }
            elseif($parentTemp->id_product <> 0){
                $parentProduct = new Product((int)$parentTemp->id_product, false, $this->context->cookie->id_lang);
                $this->_html .= '<option value="'.$parentTemp->id.'">'.$parentTemp->id.' - '.$parentProduct->name.'</option>';
            }
            else{
                $this->_html .= '<option value="'.$parentTemp->id.'">'.$parentTemp->id.' - '.$parentTemp->title.'</option>';
            }
        }

        $this->_html .= '
                            </select>
                        </div>
                    </div>';

        // custom ou non
        $this->_html .= '
                    <div class="custom_option '.(isset($responsiveLink) ? 'hidden' : '' ).'">
                        <label for="iscustom">'.$this->l('Is a custom link?').'</label>
                        <div class="margin-form">
                            <input type="radio" name="iscustom" id="iscustom_on" value="1" '.(isset($custom) ? 'checked="checked"' : '' ).'>
                            <label class="t"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Yes').'" title="'.$this->l('Yes').'"></label>
                            <input type="radio" name="iscustom" id="iscustom_off" value="0" '.(isset($custom) ? '' : 'checked="checked"' ).'>
                            <label class="t"> <img src="../img/admin/disabled.gif" alt="'.$this->l('No').'" title="'.$this->l('No').'"></label>
                        </div>
                    </div>';

        //title field
        $this->_html .= '
                    <div class="custom_block '.(isset($custom) ? 'active' : '' ).'">
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
                            <p class="clear">'.$this->l('Title of the link').'</p>
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
                            <p class="clear">'.$this->l('Url of the link (leave blank is no url)').'</p>
                        </div>
                        </div>';

        $this->_html .= '
                        <div class="margin-form">';
        if(Tools::getIsset('action') && Tools::getIsset('action') == 'editLink')
            $this->_html .= '<input type="submit" value="'.$this->l('Save').'" name="submitEditlink" class="button">
                                    <input type="hidden" value="'.$responsiveLink->id.'" name="idLink" class="button">';
        else
            $this->_html .= '<input type="submit" value="'.$this->l('Save').'" name="submitAddLink" class="button">';

        $this->_html .= '
                    </div>
                </div>
            </fieldset>
        </form>';

        $this->_html .= '
        <fieldset>
            <legend><img src="../img/admin/tab-preferences.gif" class="middle"> '.$this->l('Manage your main nav links').'</legend>
            <p>'.$this->l('Edit your main nav links with the edit button and save it.').'</p>
            <hr>
            <table id="links" class="table tableDnD" cellpadding="0" cellspacing="0" style="width: 100%;">
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

        foreach(ResponsiveLinksClass::findAll($this->context->cookie->id_lang, true) as $responsiveLink)
        {
            $category = null;
            $cms = null;
            $product = null;

            if($responsiveLink->id_category <> 0)
                $category = new Category((int)$responsiveLink->id_category, $this->context->cookie->id_lang);
            elseif($responsiveLink->id_cms <> 0)
                $cms = new CMS((int)$responsiveLink->id_cms, $this->context->cookie->id_lang);
            elseif($responsiveLink->id_product <> 0)
                $product = new Product((int)$responsiveLink->id_product, false, $this->context->cookie->id_lang);

            $this->_html .= '
                    <tr id="node-'.$responsiveLink->id.'">
                        <td class="center"><img src="'.$this->_path.'img/folder.png" alt="" /></td>
                        <td class="center">'.$responsiveLink->id.'</td>
                        <td class="center position"></td>
                        <td>';
            if(isset($category))
                $this->_html .= $category->name.'(<b><a target="_blank" href="'.$this->context->link->getCategoryLink($category).'">'.$this->l('link').'</b>)';
            elseif(isset($cms))
                $this->_html .= $cms->meta_title.'(<b><a target="_blank" href="'.$this->context->link->getCMSLink($cms).'">'.$this->l('link').'</b>)';
            elseif(isset($product))
                $this->_html .= $product->name.'(<b><a target="_blank" href="'.$this->context->link->getProductLink($product).'">'.$this->l('link').'</b>)';
            else
                $this->_html .= $responsiveLink->title.'(<b><a target="_blank" href="'.$responsiveLink->url.'">'.$this->l('link').'</b>)';
            $this->_html .= '
                        </td>
                        <td class="center">
                            <a class="editLink" href="" title="'.$this->l('Edit').'">
                                <img src="../img/admin/edit.gif" alt="'.$this->l('Edit').'" alt="'.$this->l('Edit').'">
                            </a>
                            <form style="display: none;" action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" method="POST">
                                <input type="hidden" name="action" value="editLink">
                                <input type="hidden" name="idLink" value="'.$responsiveLink->id.'">
                            </form>
                            <a class="delete_link" href="#" urlajax="'.$this->_path.'ajax.php" id="'.$responsiveLink->id.'" title="'.$this->l('Delete the link ?').'">
                                <img src="../img/admin/delete.gif" alt="'.$this->l('Delete').'" alt="'.$this->l('Delete').'">
                            </a>
                        </td>
                    </tr>';

            $this->getSubLinks($responsiveLink->id, $this->context->cookie->id_lang);
        }

        /*$this->_html .= '
                </tbody>
            </table>
        </fieldset>

        <fieldset style="margin-top:15px;">
            <legend><img src="../img/admin/tab-preferences.gif" class="middle"> '.$this->l('Manage your footer links').'</legend>
            <p>'.$this->l('Edit your footer links with the edit button and save it.').'</p>
            <hr>
        </fieldset>';*/
    }

    private function getSubLinks($id_parent, $id_lang){
        //get all sub links
        foreach(ResponsiveLinksClass::findSub($id_parent, $id_lang) as $responsiveSubLink)
        {
            $category = null;
            $cms = null;
            $product = null;

            if($responsiveSubLink->id_category <> 0)
                $category = new Category((int)$responsiveSubLink->id_category, $id_lang);
            elseif($responsiveSubLink->id_cms <> 0)
                $cms = new CMS((int)$responsiveSubLink->id_cms, $id_lang);
            elseif($responsiveSubLink->id_product <> 0)
                $product = new Product((int)$responsiveSubLink->id_product, false, $id_lang);

            $this->_html .= '
                <tr id="node-'.$responsiveSubLink->id.'" class="child-of-node-'.$id_parent.'">
                    <td class="center"><img src="'.$this->_path.'img/page_white_text.png" alt="" /></td>
                    <td class="center">'.$responsiveSubLink->id.'</td>
                    <td class="center position"></td>
                    <td>';
            if(isset($category))
                $this->_html .= $category->name.'(<b><a target="_blank" href="'.$this->context->link->getCategoryLink($category).'">'.$this->l('link').'</b>)';
            elseif(isset($cms))
                $this->_html .= $cms->meta_title.'(<b><a target="_blank" href="'.$this->context->link->getCMSLink($cms).'">'.$this->l('link').'</b>)';
            elseif(isset($product))
                $this->_html .= $product->name.'(<b><a target="_blank" href="'.$this->context->link->getProductLink($product).'">'.$this->l('link').'</b>)';
            else
                $this->_html .= $responsiveSubLink->title.'(<b><a target="_blank" href="'.$responsiveSubLink->url.'">'.$this->l('link').'</b>)';
            $this->_html .= '
                    </td>
                    <td class="center">
                            <a class="editLink" href="" title="'.$this->l('Edit').'">
                                <img src="../img/admin/edit.gif" alt="'.$this->l('Edit').'" alt="'.$this->l('Edit').'">
                            </a>
                            <form style="display: none;" action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" method="POST">
                                <input type="hidden" name="action" value="editLink">
                                <input type="hidden" name="idLink" value="'.$responsiveSubLink->id.'">
                            </form>
                            <a class="delete_link" href="#" urlajax="'.$this->_path.'ajax.php" id="'.$responsiveSubLink->id.'" title="'.$this->l('Delete the link ?').'">
                                <img src="../img/admin/delete.gif" alt="'.$this->l('Delete').'" alt="'.$this->l('Delete').'">
                            </a>
                        </td>
                </tr>
            ';

            $this->getSubLinks($responsiveSubLink->id, $id_lang);
        }
    }

    public function hookTop($params){
        $i = 0;
        $responsiveLinksArray = array();

        foreach(ResponsiveLinksClass::findAll($this->context->cookie->id_lang, true) as $responsiveLink){
            $category = null;
            $cms = null;
            $product = null;

            //check if it's a category, a cms page or a product
            if($responsiveLink->id_category <> 0){
                $category = new Category($responsiveLink->id_category, $this->context->cookie->id_lang);
                $responsiveLinksArray[$i]['objectLink'] = $category;
            }
            elseif($responsiveLink->id_cms <> 0){
                $cms = new CMS($responsiveLink->id_cms, $this->context->cookie->id_lang);
                $responsiveLinksArray[$i]['objectLink'] = $cms;
            }
            elseif($responsiveLink->id_product <> 0){
                $product = new Product((int)$responsiveLink->id_product, false, $this->context->cookie->id_lang);
                $responsiveLinksArray[$i]['objectLink'] = $product;
            }

            //save the results in an array and search for sublinks
            $responsiveLinksArray[$i]['responsiveLinkObject'] = $responsiveLink;
            $responsiveLinksArray[$i]['subLinks'] = $this->getSubLinksTop($responsiveLink->id, $this->context->cookie->id_lang);

            $i++;
        }

        $this->_hookCommon($params);

        $this->context->smarty->assign('logo_name', Configuration::get('PS_LOGO_NAME'));
        $this->context->smarty->assign('responsiveLinks', $responsiveLinksArray);
        $this->context->smarty->assign('branche_tpl_path', _PS_MODULE_DIR_.$this->name.'/views/templates/hook/responsivesublinks.tpl');
        return $this->display(__FILE__, 'responsivelinkstop.tpl');
    }

    private function getSubLinksTop($id_parent, $id_lang){
        $return = array();
        $responsiveLinkSublist = ResponsiveLinksClass::findSub($id_parent, $id_lang);

        if(count($responsiveLinkSublist) > 0) {
            $i = 0;
            //check all sub link for the current link
            foreach($responsiveLinkSublist as $responsiveLinkSub){
                $category = null;
                $cms = null;
                $product = null;

                //check if it's a category, a cms page or a product
                if($responsiveLinkSub->id_category <> 0){
                    $category = new Category($responsiveLinkSub->id_category, $id_lang);
                    $return[$i]['objectLink'] = $category;
                }
                elseif($responsiveLinkSub->id_cms <> 0){
                    $cms = new CMS($responsiveLinkSub->id_cms, $id_lang);
                    $return[$i]['objectLink'] = $cms;
                }
                elseif($responsiveLinkSub->id_product <> 0){
                    $product = new Product((int)$responsiveLinkSub->id_product, false, $id_lang);
                    $return[$i]['objectLink'] = $product;
                }

                $return[$i]['responsiveLinkObject'] = $responsiveLinkSub;
                $return[$i]['subLinks'] = $this->getSubLinksTop($responsiveLinkSub->id, $id_lang);

                $i++;
            }
        }

        return $return;
    }

    public function hookFooter($params)
    {
        return $this->display(__FILE__, 'responsivelinksfooter.tpl');
    }

    public function hookHeader($params)
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
     * @param mixed $params
     * @return void
     */
    private function _hookCommon($params)
    {
        $this->context->smarty->assign('ENT_QUOTES', ENT_QUOTES);
        $this->context->smarty->assign('search_ssl', (int)Tools::usingSecureMode());

        $this->context->smarty->assign('ajaxsearch', Configuration::get('PS_SEARCH_AJAX'));
        $this->context->smarty->assign('instantsearch', Configuration::get('PS_INSTANT_SEARCH'));

        return true;
    }


    public function installDemoLinks()
    {
        $languages = Language::getLanguages(false);

        //first link
        $indexLinkLang = array(
            'en' => 'Home',
            'br' => 'Bem vindo',
            'de' => 'Willkommen',
            'es' => 'Bienvenido',
            'fr' => 'Accueil',
            'it' => 'Benvenuto'
        );

        $indexLink = new ResponsiveLinksClass();
        $indexLink->position = 1;
        $indexLink->id_category = 0;
        $indexLink->id_cms = 0;
        $indexLink->id_product = 0;
        $indexLink->id_parent = 0;
        $indexLink->id_child = 0;
        $indexLink->id_shop = Configuration::get('PS_SHOP_DEFAULT');
        foreach ($languages as $language){
            $indexLink->title[(int)($language['id_lang'])] = $indexLinkLang[$language['iso_code']];
            $indexLink->url[(int)($language['id_lang'])]   = $this->context->link->getPageLink('index', false, (int)($language['id_lang']));
        }

        $indexLink->save();

        //3 : Ipods category
        $i = 2;
        if(Category::categoryExists(3)){
            //one category link with some products
            $ipodsLink = new ResponsiveLinksClass();
            $ipodsLink->position = 2;
            $ipodsLink->id_category = 3;
            $ipodsLink->id_cms = 0;
            $ipodsLink->id_product = 0;
            $ipodsLink->id_parent = 0;
            $ipodsLink->id_child = 0;
            $ipodsLink->id_shop = (int)Configuration::get('PS_SHOP_DEFAULT');

            $ipodsLink->save();

            //and add some products
            $results = Db::getInstance()->executeS('
                SELECT `id_product`
                FROM `'._DB_PREFIX_.'product`
                WHERE `id_category_default` = 3
                LIMIT 0,3
            ');
            foreach($results as $product){
                $productLink = new ResponsiveLinksClass();
                $productLink->position = $i;
                $productLink->id_category = 0;
                $productLink->id_cms = 0;
                $productLink->id_product = (int)$product['id_product'];
                $productLink->id_parent = $ipodsLink->id;
                $productLink->id_child = 0;
                $productLink->id_shop = (int)Configuration::get('PS_SHOP_DEFAULT');

                $productLink->save();

                $i++;
            }
        }

        if(Category::categoryExists(5)){
            //one category link with some products
            $laptopLink = new ResponsiveLinksClass();
            $laptopLink->position = $i;
            $laptopLink->id_category = 5;
            $laptopLink->id_cms = 0;
            $laptopLink->id_product = 0;
            $laptopLink->id_parent = 0;
            $laptopLink->id_child = 0;
            $laptopLink->id_shop = (int)Configuration::get('PS_SHOP_DEFAULT');

            $laptopLink->save();
            $i++;

            //and add some products
            $results = Db::getInstance()->executeS('
                SELECT `id_product`
                FROM `'._DB_PREFIX_.'product`
                WHERE `id_category_default` = 5
                LIMIT 0,2
            ');
            foreach($results as $product){
                $productLink = new ResponsiveLinksClass();
                $productLink->position = $i;
                $productLink->id_category = 0;
                $productLink->id_cms = 0;
                $productLink->id_product = (int)$product['id_product'];
                $productLink->id_parent = $laptopLink->id;
                $productLink->id_child = 0;
                $productLink->id_shop = (int)Configuration::get('PS_SHOP_DEFAULT');

                $productLink->save();

                $i++;
            }
        }

        //second link
        $contactLinkLang = array(
            'en' => 'Contact',
            'br' => 'Contato',
            'de' => 'Kontakt',
            'es' => 'Contacto',
            'fr' => 'Contact',
            'it' => 'Contatto'
        );

        $contactLink = new ResponsiveLinksClass();
        $contactLink->position = $i;
        $contactLink->id_category = 0;
        $contactLink->id_cms = 0;
        $contactLink->id_product = 0;
        $contactLink->id_parent = 0;
        $contactLink->id_child = 0;
        $contactLink->id_shop = (int)Configuration::get('PS_SHOP_DEFAULT');
        foreach ($languages as $language){
            $contactLink->title[(int)($language['id_lang'])] = $contactLinkLang[$language['iso_code']];
            $contactLink->url[(int)($language['id_lang'])]   = $this->context->link->getPageLink('contact', false, (int)($language['id_lang']));
        }

        $contactLink->save();
    }
}