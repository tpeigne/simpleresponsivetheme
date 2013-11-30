<?php

if (!defined('_PS_VERSION_'))
    exit;

/**
 * ResponsiveHomeFeatured module for Prestashop
 *
 * @author Thomas Peigné <thomas.peigne@gmail.com>
 */
class ResponsiveHomeFeatured extends Module
{
    private $_html = '';

    public function __construct()
    {
        $this->name = 'responsivehomefeatured';
        $this->tab = 'front_office_features';
        $this->version = '2.4';
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
        `date_add` datetime NOT NULL,
        `date_upd` datetime NOT NULL,
        PRIMARY KEY (`id_responsivehomefeatured`))
        ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8'))
            return false;

        if (!Db::getInstance()->Execute('
        CREATE TABLE `'._DB_PREFIX_.'responsivehomefeaturedproducts` (
        `id_responsivehomefeatured` int(10) unsigned NOT NULL,
        `id_category` int(10) unsigned NOT NULL,
        `id_product` int(10) unsigned NOT NULL,
        `date_add` datetime NOT NULL)
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
        $this->_html = '<h2>'.$this->displayName.'</h2>';
        $this->session();
        $this->displaySessionMessage();

        if (Tools::getIsset('action') && Tools::getValue('action') == 'delete' && Tools::getValue('target') == 'category') {
            $responsiveHomeFeatured = new ResponsiveHomeFeaturedClass((int) Tools::getValue('id'));

            if ($responsiveHomeFeatured->delete()) {
                if (ResponsiveHomeFeaturedClass::deleteHomeFeaturedProducts((int) Tools::getValue('id'))) {
                    $_SESSION[$this->name]['message'] = $this->l('The category has been deleted');
                    $_SESSION[$this->name]['type'] = 'confirm';

                    Tools::redirectAdmin($this->getPageUrl());
                } else {
                    $_SESSION[$this->name]['message'] = $this->l('An error has occurred while deleting the category');
                    $_SESSION[$this->name]['type'] = 'error';

                    Tools::redirectAdmin($this->getPageUrl());
                }
            }
        }

        if (Tools::getIsset('action') && Tools::getValue('action') == 'delete' && Tools::getValue('target') == 'product') {
            if (ResponsiveHomeFeaturedClass::deleteHomeFeaturedProduct((int) Tools::getValue('id_homefeatured'), (int) Tools::getValue('id_product'))) {
                $_SESSION[$this->name]['message'] = $this->l('The product has been deleted');
                $_SESSION[$this->name]['type'] = 'confirm';

                Tools::redirectAdmin($this->getPageUrl());
            } else {
                $_SESSION[$this->name]['message'] = $this->l('An error has occurred while deleting the product');
                $_SESSION[$this->name]['type'] = 'error';

                Tools::redirectAdmin($this->getPageUrl());
            }
        }

        if (Tools::isSubmit('addCategory')) {
            //check if this category already exist
            if (ResponsiveHomeFeaturedClass::existCategory((int) Tools::getValue('id_category'))) {
                $responsiveHomeFeatured = new ResponsiveHomeFeaturedClass(ResponsiveHomeFeaturedClass::getResponsiveHomeFeaturedId((int) Tools::getValue('id_category')));
                $responsiveHomeFeatured->id_category = (int) Tools::getValue('id_category');
            } else {
                $responsiveHomeFeatured = new ResponsiveHomeFeaturedClass();
                $responsiveHomeFeatured->id_category = (int) Tools::getValue('id_category');
                $responsiveHomeFeatured->position = ResponsiveHomeFeaturedClass::getMaxPosition();
            }

            $responsiveHomeFeatured->id_shop = $this->context->shop->id;

            if ($responsiveHomeFeatured->save()) {
                $_SESSION[$this->name]['message'] = $this->l('The category has been added');
                $_SESSION[$this->name]['type'] = 'confirm';

                Tools::redirectAdmin($this->getPageUrl());
            } else {
                $_SESSION[$this->name]['message'] = $this->l('An error has occurred during the category addition');
                $_SESSION[$this->name]['type'] = 'error';

                Tools::redirectAdmin($this->getPageUrl());
            }
        }

        if (Tools::isSubmit('addProduct')) {
            $responsiveHomeFeatured = new ResponsiveHomeFeaturedClass((int) Tools::getValue('id_category'));

            if (Tools::getIsset('id_product')) {
                $responsiveHomeFeatured->addProduct((int) Tools::getValue('id_product'));

                $_SESSION[$this->name]['message'] = $this->l('The product has been added to the category');
                $_SESSION[$this->name]['type'] = 'confirm';

                Tools::redirectAdmin($this->getPageUrl());
            } else {
                $_SESSION[$this->name]['message'] = $this->l('An error has occurred while adding the product to the category');
                $_SESSION[$this->name]['type'] = 'error';

                Tools::redirectAdmin($this->getPageUrl());
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
        <link type="text/css" rel="stylesheet" href="'.$this->_path.'../responsiveextension/stylesheets/admin-common.css" />
        <link type="text/css" rel="stylesheet" href="'.$this->_path.'stylesheets/responsivehomefeatured.css" />

        <script type="text/javascript" src="'.$this->_path.'../responsiveextension/javascripts/admin-common.js"></script>
        <script type="text/javascript" src="'.$this->_path.'javascripts/responsivehomefeatured.js"></script>

        <button class="button dropdown" section="category-add"><span>'.$this->l('Add a category').'</span></button>
        <button class="button dropdown" section="product-add"><span>'.$this->l('Add a product').'</span></button>';

        $this->_html .= '
        <form id="category-add" class="dropdown-content" action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" method="POST">
            <fieldset>
                <legend><img src="../img/admin/information.png" class="middle"> '.$this->l('Add a new category').'</legend>
                <div>';

        //category
        $this->_html .= '
                    <div class="category_block">
                        <label for="id_category">'.$this->l('Choose your category :').'</label>
                        <div class="margin-form">
                            <select name="id_category" id="id_category" size="5">';
        foreach(Category::getSimpleCategories($this->context->cookie->id_lang) as $categoryTemp){
            $this->_html .= '
                                <option value="'.$categoryTemp['id_category'].'" '.(isset($category) && $category->id == $categoryTemp['id_category'] ? 'selected="selected"' : '').'>'.$categoryTemp['name'].'</option>';
        }

        $this->_html .= '
                            </select>
                        </div>
                    </div>';

        $this->_html .= '
                    <div class="margin-form">
                        <input type="submit" value="'.$this->l('Save').'" name="addCategory" class="button">
                    </div>
                </div>
            </fieldset>
        </form>';

        $this->_html .= '
        <form id="product-add" class="dropdown-content" action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" method="POST">
            <fieldset>
                <legend><img src="../img/admin/information.png" class="middle"> '.$this->l('Add a new product').'</legend>
                <div>';

        // get all responsivehomefeatured categories
        $homeFeaturedCategories = ResponsiveHomeFeaturedClass::findAll();
        if (empty($homeFeaturedCategories)) {
            $this->_html .= '
            <div class="warn">
                '.$this->l('No responsive category found.').'
            </div>
            ';
        } else {
            //category
            $this->_html .= '
                        <div class="category_block">
                            <label for="id_category">'.$this->l('Choose your category :').'</label>
                            <div class="margin-form">
                                <select name="id_category" id="id_category">';
            foreach($homeFeaturedCategories as $responsiveCategory){
                $psCategory = new Category($responsiveCategory->id_category, Context::getContext()->cookie->id_lang);
                $this->_html .= '
                                    <option value="'.$responsiveCategory->id.'">'.$psCategory->name.'</option>';
            }

            $this->_html .= '
                                </select>
                            </div>
                        </div>';

            // product
            $this->_html .= '
                        <div class="category_block">
                            <label for="id_category">'.$this->l('Choose your product :').'</label>
                            <div class="margin-form">
                                <input type="text" id="product_auto" name="product_auto" size="50"/>
                                <input type="hidden" id="id_product" name="id_product" />
                                <p class="clear">'.$this->l('Type a word to search products').'</p>
                            </div>
                        </div>';

            $this->_html .= '
                        <div class="margin-form">
                            <input type="submit" value="'.$this->l('Save').'" name="addProduct" class="button">
                        </div>';
        }
        $this->_html .= '
                </div>
            </fieldset>
        </form>';

        $this->_html .= '
        <fieldset>
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
            $category = new Category(
                (int) $responsiveHomeFeatured->id_category,
                $this->context->cookie->id_lang
            );

            // Retrieve all products for this category
            $productsResponsiveHomeFeaturedAll = $responsiveHomeFeatured->getProducts();

            $this->_html .= '
                    <tr id="'.$responsiveHomeFeatured->id.'">
                        <td class="center position"></td>
                        <td>
                            '.$category->name.'
                        </td>
                        <td class="center">
                            <span class="product-count"><b>'.count($productsResponsiveHomeFeaturedAll).' '.(count($productsResponsiveHomeFeaturedAll) > 1 ? $this->l('products') : $this->l('product')).'</b></span>
                        </td>
                        <td class="center">';
            $this->_html .= '
                            <a class="delete" href="'.$this->getPageUrl(array('id='.$responsiveHomeFeatured->id, 'action=delete', 'target=category')).'" id="'.$responsiveHomeFeatured->id.'" title="'.$this->l('Delete the category ?').'">
                                <img src="../img/admin/delete.gif" alt="'.$this->l('Delete').'" alt="'.$this->l('Delete').'">
                            </a>
                        </td>
                    </tr>';

            foreach($productsResponsiveHomeFeaturedAll as $productsResponsiveHomeFeatured)
            {
                $this->_html .= '
                        <tr class="product-'.$responsiveHomeFeatured->id.' subcategory nodrag nodrop">
                            <td class="center"></td>
                            <td>
                                '.$productsResponsiveHomeFeatured->name.'
                            </td>
                            <td class="center">

                            </td>
                            <td class="center">
                                <a class="delete" href="'.$this->getPageUrl(array('id_homefeatured='.$responsiveHomeFeatured->id, 'id_product='.$productsResponsiveHomeFeatured->id, 'action=delete', 'target=product')).'" id="'.$productsResponsiveHomeFeatured->id.'" title="'.$this->l('Delete the product ?').'">
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

    public function hookHome()
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

    public function hookHeader()
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
        $shops = Shop::getShops();

        //first category
        if (Category::categoryExists(3)) {
            foreach ($shops as $shop) {
                $firstHomeFeatured = new ResponsiveHomeFeaturedClass();
                $firstHomeFeatured->id_category = 3;
                $firstHomeFeatured->position    = 1;
                $firstHomeFeatured->id_shop     = (int) $shop['id_shop'];

                $firstHomeFeatured->save();

                //and add some products
                $results = Db::getInstance()->executeS('
                    SELECT `id_product`
                    FROM `'._DB_PREFIX_.'product`
                    WHERE `id_category_default` = 3
                    LIMIT 0,3
                ');

                foreach($results as $product) {
                    if (!$firstHomeFeatured->addProduct((int) $product['id_product'])) {
                        return false;
                    }
                }
            }
        }

        //second category
        if (Category::categoryExists(5)) {
            foreach ($shops as $shop) {
                $secondHomeFeatured = new ResponsiveHomeFeaturedClass();
                $secondHomeFeatured->id_category = 5;
                $secondHomeFeatured->position    = 1;
                $secondHomeFeatured->id_shop     = (int) $shop['id_shop'];

                $secondHomeFeatured->save();

                //and add some products
                $results = Db::getInstance()->executeS('
                    SELECT `id_product`
                    FROM `'._DB_PREFIX_.'product`
                    WHERE `id_category_default` = 5
                    LIMIT 0,2
                ');

                foreach($results as $product) {
                    if (!$secondHomeFeatured->addProduct((int) $product['id_product'])) {
                        return false;
                    }
                }
            }
        }

        return true;
    }

    /**
     * Return current session if not exist
     *
     * @return void
     */
    protected function session() {
        if(!session_id()) {
            session_start();
        }

    }

    /**
     * Add session messages into module html
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