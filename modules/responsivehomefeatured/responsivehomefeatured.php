<?php

/**
* ResponsiveHomeFeatured module for Prestashop, responsivehomefeaturedslider.php
*
* Created by Thomas Peigné (thomas.peigne@gmail.com)
*/

if (!defined('_PS_VERSION_'))
exit;

/**
* Class ResponsiveHomeFeatured
*/
class ResponsiveHomeFeatured extends Module
{
    private $_html = '';

    public function __construct()
    {
        $this->name = 'responsivehomefeatured';
        $this->tab = 'front_office_features';
        $this->version = '2.2';
        $this->author = 'Thomas Peigné';
        $this->need_instance = 0;

        parent::__construct();

        $this->displayName = $this->l('Responsive products featured');
        $this->description = $this->l('Displays featured products and categories in your homepage.');

        include_once($this->local_path.'/classes/ResponsiveHomeFeaturedClass.php');
    }

    public function install()
    {
        if (!parent::install() OR !$this->registerHook('home') OR !$this->registerHook('header'))
            return false;

        if (!Db::getInstance()->Execute('
        CREATE TABLE `'._DB_PREFIX_.'responsivehomefeatured` (
        `id_responsivehomefeatured` int(10) unsigned NOT NULL AUTO_INCREMENT,
        `id_shop` int(10) unsigned NOT NULL,
        `id_category` int(10) unsigned NOT NULL,
        `position` int(10) NOT NULL,
        PRIMARY KEY (`id_responsivehomefeatured`))
        ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8'))
            return false;

        if (!Db::getInstance()->Execute('
        CREATE TABLE `'._DB_PREFIX_.'responsivehomefeaturedproducts` (
        `id_responsivehomefeatured` int(10) unsigned NOT NULL,
        `id_category` int(10) unsigned NOT NULL,
        `id_product` int(10) unsigned NOT NULL)
        ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8'))
            return false;

        $this->installDemoLinks();

        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall())
            return false;

        if (!Db::getInstance()->Execute('DROP TABLE `'._DB_PREFIX_.'responsivehomefeatured`'))
            return false;

        if (!Db::getInstance()->Execute('DROP TABLE `'._DB_PREFIX_.'responsivehomefeaturedproducts`'))
            return false;

        return true;
    }

    public function getContent()
    {
        $this->_html = '<h2>'.$this->displayName.'</h2><div style="display:none;" id="ajax_response"></div>';

        if (Tools::isSubmit('submitAddHomeFeatured')) {
            //check if this category already exist
            if (ResponsiveHomeFeaturedClass::existCategory((int)Tools::getValue('id_category'))) {
                $responsiveHomeFeatured = new ResponsiveHomeFeaturedClass(ResponsiveHomeFeaturedClass::getResponsiveHomeFeaturedId((int)Tools::getValue('id_category')));
                $responsiveHomeFeatured->id_category = (int)Tools::getValue('id_category');
            } else {
                $responsiveHomeFeatured = new ResponsiveHomeFeaturedClass();
                $responsiveHomeFeatured->id_category = (int)Tools::getValue('id_category');
                $responsiveHomeFeatured->position = ResponsiveHomeFeaturedClass::getMaxPosition();
            }

            $responsiveHomeFeatured->id_shop = $this->context->shop->id;

            if ($responsiveHomeFeatured->save()) {
                //insert products
                if (Tools::getIsset('product')) {
                    $responsiveHomeFeatured->addProduct((int)Tools::getValue('product'));
                    $this->_html .= '
                    <div class="conf confirm">
                        '.$this->l('The product in the category has been added.').'
                    </div>';
                } else {
                    $this->_html .= '
                    <div class="conf confirm">
                        '.$this->l('The category has been added.').'
                    </div>';
                }
            } else {
                $this->_html .= '
                <div class="conf error">
                    '.$this->l('An error has occured during the addition of the product.').'
                </div>';
            }
        }

        $this->displayForm();

        return $this->_html;
    }

    public function displayForm()
    {
        $category = null;
        $homeFeatured = null;

        if (Tools::getIsset('action') && Tools::getIsset('action') == 'editHomeFeatured') {
            $homeFeatured = new ResponsiveHomeFeaturedClass((int)Tools::getValue('idHomeFeatured'));
            $category = new Category((int)$homeFeatured->id_category, (int)$this->context->cookie->id_lang);
        }

        $this->_html .= '
        <script type="text/javascript">
            var urlAjaxModule = "'._PS_BASE_URL_.$this->_path.'ajax.php";
            var msgProducts = "'.$this->l('No products found in this category').'";
        </script>
        <script type="text/javascript" src="'._PS_JS_DIR_.'jquery/plugins/jquery.tablednd.js"></script>
        <link type="text/css" rel="stylesheet" href="'.$this->_path.'stylesheets/responsivehomefeatured.css" />
        <script type="text/javascript" src="'.$this->_path.'javascripts/responsivehomefeatured.js"></script>
        <a id="add_homefeatured" href=""><img src="../img/admin/add.gif" border="0"> '.$this->l('Add a category').'</a>
        ';

        $this->_html .= '
        <form id="informations_link" style="'.(isset($homeFeatured) ? 'margin-top: 15px;' : 'display:none;margin-top: 15px;').'" action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" method="post" enctype="multipart/form-data">
            <fieldset style="margin-bottom:10px;">
                <legend><img src="../img/admin/information.png" class="middle"> '.$this->l('Add a new category to your home page').'</legend>
                <div>';

                    //category
                    $this->_html .= '
                    <div class="category_block">
                        <label for="id_category">'.$this->l('Choose your category :').'</label>
                        <div class="margin-form">
                            <select name="id_category" id="id_category">';
                            foreach(Category::getSimpleCategories($this->context->cookie->id_lang) as $categoryTemp){
                                $this->_html .= '
                                <option value="'.$categoryTemp['id_category'].'" '.(isset($category) && $category->id == $categoryTemp['id_category'] ? 'selected="selected"' : '').'>'.$categoryTemp['name'].'</option>';
                            }

                            $this->_html .= '
                            </select>
                        </div>
                    </div>';

                    //products
                    $this->_html .= '
                    <div class="category_block">
                        <label for="product">'.$this->l('Choose your product page :').'</label>
                        <div class="margin-form">
                            <select name="product" id="product">';
                                $this->_html .= '
                                <option value=""></option>';
                            $this->_html .= '
                            </select><p id="product_ajax" style="padding-top:5px;"></p>
                        </div>
                    </div>';

                    //position
                    $this->_html .= '
                    <div class="position_block" style="display:none">
                        <label for="position">'.$this->l('Position :').'</label>
                        <div class="margin-form">
                            <input type="text" size="1" name="position" class="required" id="position" value="'.(isset($homeFeatured->position) ? $homeFeatured->position : '').'"/>
                        </div>
                    </div>';

                    $this->_html .= '
                    <div class="margin-form">';
                        if(isset($homeFeatured))
                            $this->_html .= '<input type="submit" value="'.$this->l('Save').'" name="submitEditHomeFeatured" class="button">
                                <input type="hidden" value="'.$homeFeatured->id.'" name="idHomeFeatured" class="button">';
                        else
                            $this->_html .= '<input type="submit" value="'.$this->l('Save').'" name="submitAddHomeFeatured" id="submitAddHomeFeatured" class="button">';

                    $this->_html .= '
                    </div>
                </div>
            </fieldset>
        </form>';

        $this->_html .= '
        <fieldset style="margin-top: 15px;">
            <legend><img src="../img/admin/tab-preferences.gif" class="middle"> '.$this->l('Manage your categories').'</legend>
            <p>'.$this->l('Edit your categories.').'</p>
            <hr>
            <table id="categories" class="table tableDnD" cellpadding="0" cellspacing="0" style="width: 100%;">
                <thead>
                    <tr>
                        <th class="center">'.$this->l('Position').'</th>
                        <th>'.$this->l('Name').'</th>
                        <th class="center">'.$this->l('Products').'</th>
                        <th class="center">'.$this->l('Actions').'</th>
                    </tr>
                </thead>
                <tbody>';

                foreach(ResponsiveHomeFeaturedClass::findAll() as $responsiveHomeFeatured)
                {
                    $category = null;
                    $category = new Category((int)$responsiveHomeFeatured->id_category, $this->context->cookie->id_lang);
                    $productsResponsiveHomeFeaturedAll = $responsiveHomeFeatured->getProducts();

                    $this->_html .= '
                    <tr id="'.$responsiveHomeFeatured->id.'">
                        <td class="center position"></td>
                        <td>
                            '.$category->name.'
                            (<b><a target="_blank" href="'.(isset($category) ? $this->context->link->getCategoryLink($category) : '').'">'.$this->l('Category link').'</a></b>)
                        </td>
                        <td class="center">
                            <span><b>'.count($productsResponsiveHomeFeaturedAll).' '.(count($productsResponsiveHomeFeaturedAll) > 1 ? $this->l('products') : $this->l('product')).'</b></span>
                        </td>
                        <td class="center">';
                        $this->_html .= '
                            <a class="delete_homefeatured" href="#" id="'.$responsiveHomeFeatured->id.'" title="'.$this->l('Delete the category ?').'">
                                <img src="../img/admin/delete.gif" alt="'.$this->l('Delete').'" alt="'.$this->l('Delete').'">
                            </a>
                        </td>
                    </tr>';

                    foreach($productsResponsiveHomeFeaturedAll as $productsResponsiveHomeFeatured)
                    {
                        $this->_html .= '
                        <tr class="'.$responsiveHomeFeatured->id.'_product hidden subcategory nodrag nodrop">
                            <td class="center"></td>
                            <td>
                                '.$productsResponsiveHomeFeatured->name.'
                                (<b><a target="_blank" href="'.$this->context->link->getProductLink($productsResponsiveHomeFeatured).'">'.$this->l('Product link').'</a></b>)
                            </td>
                            <td class="center">

                            </td>
                            <td class="center">
                                <a class="delete_homefeatured_product" href="#" urlajax="'.$this->_path.'ajax.php" id="'.$productsResponsiveHomeFeatured->id.'" title="'.$this->l('Delete the product ?').'">
                                    <img src="../img/admin/delete.gif" alt="'.$this->l('Delete').'" alt="'.$this->l('Delete').'">
                                </a>
                            </td>
                        </tr>';
                    }
                }

                $this->_html .= '
                </tbody>
            </table>
        </fieldset>';
    }

    public function hookHome($params)
    {
        $categoryList = array();
        $i = 0;
        $j = 0;

        /** @var $homeFeatured ResponsiveHomeFeaturedClass */
        foreach(ResponsiveHomeFeaturedClass::findAll() as $homeFeatured)
        {
            $categoryList[$i]['category'] = new Category($homeFeatured->id_category, $this->context->cookie->id_lang);

            //get product list
            foreach($homeFeatured->getProducts() as $product)
            {
                $cover = $product->getCover($product->id);
                $categoryList[$i]['products'][$j]['product'] = $product;
                $categoryList[$i]['products'][$j]['price_tax_inc'] = Product::getPriceStatic($product->id, true);

                $categoryList[$i]['products'][$j]['reduction'] = Product::getPriceStatic(
                    $product->id,
                    (bool)Tax::excludeTaxeOption(),
                    null,
                    6,
                    null,
                    true,
                    true,
                    1,
                    true,
                    null,
                    null,
                    null
                );

                if (Group::getPriceDisplayMethod((int)Group::getCurrent()->id) == PS_TAX_EXC) {
                    $categoryList[$i]['products'][$j]['price_without_reduction'] = $product->getPrice(false, null, 6, null, false, false);
                } else {
                    $categoryList[$i]['products'][$j]['price_without_reduction'] = $product->getPrice(true, null, 6, null, false, false);
                }

                $categoryList[$i]['products'][$j]['image'] = $this->context->link->getImageLink($product->link_rewrite, $product->id.'-'.$cover['id_image'], 'large_default');

                $j++;
            }

            $i++;
        }

        $this->context->smarty->assign(array('categoryList' => $categoryList));

        return $this->display(__FILE__, 'responsivehomefeatured.tpl');
    }

    public function hookHeader($params)
    {
        $this->context->controller->addCSS(($this->_path).'responsivehomefeatured.css', 'all');
    }

    /**
     * Function used to initialize demo products (if they exists)
     *
     * @return bool
     */
    public function installDemoLinks()
    {
        //first category
        if (Category::categoryExists(3)) {
            $firstHomeFeatured = new ResponsiveHomeFeaturedClass();
            $firstHomeFeatured->id_category = 3;
            $firstHomeFeatured->position = 1;
            $firstHomeFeatured->id_shop = (int)Configuration::get('PS_SHOP_DEFAULT');

            $firstHomeFeatured->save();

            //and add some products
            $results = Db::getInstance()->executeS('
                SELECT `id_product`
                FROM `'._DB_PREFIX_.'product`
                WHERE `id_category_default` = 3
                LIMIT 0,3
            ');

            foreach($results as $product)
            {
                if (!$firstHomeFeatured->addProduct((int)$product['id_product']))
                    return false;
            }
        }

        //second category
        if (Category::categoryExists(5)) {
            $secondHomeFeatured = new ResponsiveHomeFeaturedClass();
            $secondHomeFeatured->id_category = 5;
            $secondHomeFeatured->position = 1;
            $secondHomeFeatured->id_shop = (int)Configuration::get('PS_SHOP_DEFAULT');

            $secondHomeFeatured->save();

            //and add some products
            $results = Db::getInstance()->executeS('
                SELECT `id_product`
                FROM `'._DB_PREFIX_.'product`
                WHERE `id_category_default` = 5
                LIMIT 0,2
            ');

            foreach($results as $product)
            {
                if (!$secondHomeFeatured->addProduct((int)$product['id_product']))
                    return false;
            }
        }

        return true;
    }
}
