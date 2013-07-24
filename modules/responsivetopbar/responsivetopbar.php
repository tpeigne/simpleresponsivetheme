<?php

/**
  * ResponsiveTopBar module for Prestashop, responsivetopbar.php
  *
  * Created by Thomas Peigné (thomas.peigne@gmail.com)
  */

if (!defined('_PS_VERSION_'))
    exit;

class ResponsiveTopBar extends Module
{
    public function __construct()
    {
        $this->name = 'responsivetopbar';
        $this->tab = 'front_office_features';
        $this->version = '2.0';
        $this->author = 'Thomas Peigné';
        $this->need_instance = 0;

        parent::__construct();

        $this->displayName = $this->l('Responsive top bar');
        $this->description = $this->l('Adds a responsive top bar at the top of your website.');
    }

    public function install()
    {
        // Install Module
        if (!parent::install() OR !$this->registerHook('top') OR !$this->registerHook('header'))
            return false;

        //configuration update
        if (Configuration::updateValue('RESPONSIVE_BLOCK_CART_AJAX', 1) == false)
            return false;

        return true;
    }

    public function getContent()
    {
        $output = '<h2>'.$this->displayName.'</h2>';
        if (Tools::isSubmit('submitResponsiveBlockCart'))
        {
            $ajax = Tools::getValue('cart_ajax');
            if ($ajax != 0 && $ajax != 1)
                $output .= '<div class="alert error">'.$this->l('Ajax : Invalid choice.').'</div>';
            else
                Configuration::updateValue('RESPONSIVE_BLOCK_CART_AJAX', (int)($ajax));
            $output .= '<div class="conf confirm">'.$this->l('Settings updated').'</div>';
        }
        return $output.$this->displayForm();
    }

    public function displayForm()
    {
        return '
        <form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" method="post">
            <fieldset>
                <legend>'.$this->l('Settings').'</legend>

                <label>'.$this->l('Ajax cart').'</label>
                <div class="margin-form">
                    <input type="radio" name="cart_ajax" id="ajax_on" value="1" '.(Tools::getValue('cart_ajax', Configuration::get('RESPONSIVE_BLOCK_CART_AJAX')) ? 'checked="checked" ' : '').'/>
                    <label class="t" for="ajax_on"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" /></label>
                    <input type="radio" name="cart_ajax" id="ajax_off" value="0" '.(!Tools::getValue('cart_ajax', Configuration::get('RESPONSIVE_BLOCK_CART_AJAX')) ? 'checked="checked" ' : '').'/>
                    <label class="t" for="ajax_off"> <img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" /></label>
                    <p class="clear">'.$this->l('Activate AJAX mode for cart (compatible with the default theme)').'</p>
                </div>

                <center><input type="submit" name="submitResponsiveBlockCart" value="'.$this->l('Save').'" class="button" /></center>
            </fieldset>
        </form>';
    }

    public function hookTop($params)
    {
        if (!$this->active)
            return;

        $languages = Language::getLanguages();
        if (!count($languages))
            return;

        if ((int)Configuration::get('PS_REWRITING_SETTINGS'))
        {
            $default_rewrite = array();
            $phpSelf = isset($_SERVER['PHP_SELF']) ? substr($_SERVER['PHP_SELF'], strlen(__PS_BASE_URI__)) : '';
            if ($phpSelf == 'product.php' AND $id_product = (int)Tools::getValue('id_product'))
            {
                $rewrite_infos = Product::getUrlRewriteInformations((int)$id_product);
                foreach ($rewrite_infos AS $infos)
                    $default_rewrite[$infos['id_lang']] = $this->context->link->getProductLink((int)$id_product, $infos['link_rewrite'], $infos['category_rewrite'], $infos['ean13'], (int)$infos['id_lang']);
            }

            if ($phpSelf == 'category.php' AND $id_category = (int)Tools::getValue('id_category'))
            {
                $rewrite_infos = Category::getUrlRewriteInformations((int)$id_category);
                foreach ($rewrite_infos AS $infos)
                    $default_rewrite[$infos['id_lang']] = $this->context->link->getCategoryLink((int)$id_category, $infos['link_rewrite'], $infos['id_lang']);
            }

            if ($phpSelf == 'cms.php' AND ($id_cms = (int)Tools::getValue('id_cms') OR $id_cms_category = (int)Tools::getValue('id_cms_category')))
            {
                $rewrite_infos = (isset($id_cms) AND !isset($id_cms_category)) ? CMS::getUrlRewriteInformations($id_cms) : CMSCategory::getUrlRewriteInformations($id_cms_category);
                foreach ($rewrite_infos AS $infos)
                {
                    $arr_link = (isset($id_cms) AND !isset($id_cms_category)) ?
                        $this->context->link->getCMSLink($id_cms, $infos['link_rewrite'], NULL, $infos['id_lang']) :
                        $this->context->link->getCMSCategoryLink($id_cms_category, $infos['link_rewrite'], $infos['id_lang']);
                    $default_rewrite[$infos['id_lang']] = $arr_link;
                }
            }
            if (count($default_rewrite))
                $this->context->smarty->assign('lang_rewrite_urls', $default_rewrite);
        }

        $this->assignContentVars($params);

        $this->context->smarty->assign(array(
            'cart' => $this->context->cart,
            'cart_qties' => $this->context->cart->nbProducts(),
            'logged' => $this->context->customer->isLogged(),
            'customerName' => ($this->context->cookie->logged ? $this->context->cookie->customer_firstname.' '.$this->context->cookie->customer_lastname : false),
            'firstName' => ($this->context->cookie->logged ? $this->context->cookie->customer_firstname : false),
            'lastName' => ($this->context->cookie->logged ? $this->context->cookie->customer_lastname : false),
            'order_process' => Configuration::get('PS_ORDER_PROCESS_TYPE') ? 'order-opc' : 'order',
            'languages' => $languages
        ));
        return $this->display(__FILE__, 'responsivetopbar.tpl');
    }

    public function assignContentVars(&$params)
    {
        global $errors;

        // Set currency
        if ((int)$params['cart']->id_currency && (int)$params['cart']->id_currency != $this->context->currency->id)
            $currency = new Currency((int)$params['cart']->id_currency);
        else
            $currency = $this->context->currency;

        $taxCalculationMethod = Group::getPriceDisplayMethod((int)Group::getCurrent()->id);

        $useTax = !($taxCalculationMethod == PS_TAX_EXC);

        $products = $params['cart']->getProducts(true);
        $nbTotalProducts = 0;
        //add cover image and total products
        foreach ($products as &$product){
            $nbTotalProducts += (int)$product['cart_quantity'];
            $idCoverImage = Product::getCover($product['id_product']);
            $product['cover_image'] = $this->context->link->getImageLink($product['link_rewrite'], $product['id_product'].'-'.$idCoverImage['id_image'], 'small_default');
        }

        $cart_rules = $params['cart']->getCartRules();

        $shipping_cost = Tools::displayPrice($params['cart']->getOrderTotal($useTax, Cart::ONLY_SHIPPING), $currency);
        $shipping_cost_float = Tools::convertPrice($params['cart']->getOrderTotal($useTax, Cart::ONLY_SHIPPING), $currency);
        $wrappingCost = (float)($params['cart']->getOrderTotal($useTax, Cart::ONLY_WRAPPING));
        $totalToPay = $params['cart']->getOrderTotal($useTax);

        if ($useTax && Configuration::get('PS_TAX_DISPLAY') == 1)
        {
            $totalToPayWithoutTaxes = $params['cart']->getOrderTotal(false);
            $this->context->smarty->assign('tax_cost', Tools::displayPrice($totalToPay - $totalToPayWithoutTaxes, $currency));
        }

        // The cart content is altered for display
        foreach ($cart_rules as &$cart_rule)
        {
            if ($cart_rule['free_shipping'])
            {
                $shipping_cost = Tools::displayPrice(0, $currency);
                $shipping_cost_float = 0;
                $cart_rule['value_real'] -= Tools::convertPrice($params['cart']->getOrderTotal(true, Cart::ONLY_SHIPPING), $currency);
                $cart_rule['value_tax_exc'] = Tools::convertPrice($params['cart']->getOrderTotal(false, Cart::ONLY_SHIPPING), $currency);
            }
            if ($cart_rule['gift_product'])
            {
                foreach ($products as &$product)
                    if ($product['id_product'] == $cart_rule['gift_product'] && $product['id_product_attribute'] == $cart_rule['gift_product_attribute'])
                    {
                        $product['total_wt'] = Tools::ps_round($product['total_wt'] - $product['price_wt'], (int)$currency->decimals * _PS_PRICE_DISPLAY_PRECISION_);
                        $product['total'] = Tools::ps_round($product['total'] - $product['price'], (int)$currency->decimals * _PS_PRICE_DISPLAY_PRECISION_);
                        $cart_rule['value_real'] = Tools::ps_round($cart_rule['value_real'] - $product['price_wt'], (int)$currency->decimals * _PS_PRICE_DISPLAY_PRECISION_);
                        $cart_rule['value_tax_exc'] = Tools::ps_round($cart_rule['value_tax_exc'] - $product['price'], (int)$currency->decimals * _PS_PRICE_DISPLAY_PRECISION_);
                    }
            }
        }

        $this->context->smarty->assign(array(
            'products' => $products,
            'customizedDatas' => Product::getAllCustomizedDatas((int)($params['cart']->id)),
            'CUSTOMIZE_FILE' => _CUSTOMIZE_FILE_,
            'CUSTOMIZE_TEXTFIELD' => _CUSTOMIZE_TEXTFIELD_,
            'discounts' => $cart_rules,
            'nb_total_products' => (int)($nbTotalProducts),
            'shipping_cost' => $shipping_cost,
            'shipping_cost_float' => $shipping_cost_float,
            'show_wrapping' => $wrappingCost > 0 ? true : false,
            'show_tax' => (int)(Configuration::get('PS_TAX_DISPLAY') == 1 && (int)Configuration::get('PS_TAX')),
            'wrapping_cost' => Tools::displayPrice($wrappingCost, $currency),
            'product_total' => Tools::displayPrice($params['cart']->getOrderTotal($useTax, Cart::BOTH_WITHOUT_SHIPPING), $currency),
            'total' => Tools::displayPrice($totalToPay, $currency),
            'order_process' => Configuration::get('PS_ORDER_PROCESS_TYPE') ? 'order-opc' : 'order',
            'ajax_allowed' => (int)(Configuration::get('PS_BLOCK_CART_AJAX')) == 1 ? true : false,
            'static_token' => Tools::getToken(false)
        ));
        if (count($errors))
            $this->context->smarty->assign('errors', $errors);
    }

    public function hookHeader($params)
    {
        $this->context->controller->addCSS(($this->_path).'responsivetopbar.css');

        if ((int)(Configuration::get('RESPONSIVE_BLOCK_CART_AJAX'))) {
            $this->context->controller->addJs(($this->_path).'ajax-cart.js');
        }
    }
}


