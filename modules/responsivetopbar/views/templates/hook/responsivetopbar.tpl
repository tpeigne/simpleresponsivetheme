<div id="header_user">
    <script type="text/javascript">
        var CUSTOMIZE_TEXTFIELD = {$CUSTOMIZE_TEXTFIELD};
        var img_dir = '{$img_dir}';
        var customizationIdMessage = '{l s='Customization #' mod='responsivetopbar' js=1}';
        var removingLinkText = '{l s='remove this product from my cart' mod='responsivetopbar' js=1}';
        var freeShippingTranslation = '{l s='Free shipping!' mod='responsivetopbar' js=1}';
        var freeProductTranslation = '{l s='Free!' mod='responsivetopbar' js=1}';
        var delete_txt = '{l s='Delete' mod='responsivetopbar' js=1}';
    </script>
    <div class="row">
        <p id="header_user_info" class="five columns hide-for-small">
            {l s='Welcome' mod='responsivetopbar'},
            {if $logged}
                <span>{$cookie->customer_firstname} {$cookie->customer_lastname}</span>
                (<a href="{$link->getPageLink('index.php')}?mylogout" title="{l s='Log me out' mod='responsivetopbar'}">{l s='Log out' mod='responsivetopbar'}</a>)
            {else}
                <a href="{$link->getPageLink('my-account.php', true)}">{l s='Log in' mod='responsivetopbar'}</a>
            {/if}
        </p>
        <div class="seven columns hide-for-small header_user_right">
            <ul id="header_nav" class="clearfix">
                {if $languages|count > 1}
                    <li id="first_languages">
                        {assign var=langage_inactif_all value=""}
                        {foreach from=$languages key=k item=language name="languages"}
                            {assign var=indice_lang value=$language.id_lang}
                            {if $language.iso_code == $lang_iso}
                                {capture name='langage' assign='langage_actif'}
                                    {if isset($lang_rewrite_urls.$indice_lang)}
                                        <a href="{$lang_rewrite_urls.$indice_lang|escape:htmlall}" title="{$language.name}">
                                            <img src="{$img_lang_dir}{$language.id_lang}.jpg" alt="{$language.iso_code}" width="16" height="11" /> {$language.name}
                                        </a>
                                    {else}
                                        <a href="{$link->getLanguageLink($language.id_lang)|escape:htmlall}" title="{$language.name}">
                                            <img src="{$img_lang_dir}{$language.id_lang}.jpg" alt="{$language.iso_code}" width="16" height="11" /> {$language.name}
                                        </a>
                                    {/if}
                                {/capture}
                            {else}
                                {capture name='langage' assign='langage_inactif'}
                                    {if $language.iso_code != $lang_iso}
                                        {if isset($lang_rewrite_urls.$indice_lang)}
                                            <li><a href="{$lang_rewrite_urls.$indice_lang|escape:htmlall}" title="{$language.name}">
                                                <img src="{$img_lang_dir}{$language.id_lang}.jpg" alt="{$language.iso_code}" width="16" height="11" /> {$language.name}
                                            </a></li>
                                        {else}
                                            <li><a href="{$link->getLanguageLink($language.id_lang)|escape:htmlall}" title="{$language.name}">
                                                <img src="{$img_lang_dir}{$language.id_lang}.jpg" alt="{$language.iso_code}" width="16" height="11" /> {$language.name}
                                            </a></li>
                                        {/if}
                                    {/if}
                                {/capture}

                                {assign var="langage_inactif_all" value=$langage_inactif_all|cat:$langage_inactif}
                            {/if}
                        {/foreach}
                        {$langage_actif}
                        {if $languages|count > 0}
                            <ul class="other_languages">{$langage_inactif_all}</ul>
                        {/if}
                    </li>
                {/if}
                <li id="your_account"><a href="{$link->getPageLink('my-account.php', true)}" title="{l s='Your Account' mod='responsivetopbar'}">{l s='Your Account' mod='responsivetopbar'}</a></li>

                {if !$PS_CATALOG_MODE}
                <li id="shopping_cart">
                    <a href="{$link->getPageLink("$order_process.php", true)}">
                        <span class="ajax_cart_quantity{if $cart_qties == 0} hidden{/if}">{$cart_qties}</span>
                        <span class="ajax_cart_product_txt{if $cart_qties != 1} hidden{/if}">{l s='product' mod='responsivetopbar'}</span>
                        <span class="ajax_cart_product_txt_s{if $cart_qties < 2} hidden{/if}">{l s='products' mod='responsivetopbar'}</span>
                        {if $cart_qties >= 0}
                            <span class="ajax_cart_total{if $cart_qties == 0} hidden{/if}">
                                {if $priceDisplay == 1}
                                    {assign var='blockuser_cart_flag' value='Cart::BOTH_WITHOUT_SHIPPING'|constant}
                                    {convertPrice price=$cart->getOrderTotal(false, $blockuser_cart_flag)}
                                {else}
                                    {assign var='blockuser_cart_flag' value='Cart::BOTH_WITHOUT_SHIPPING'|constant}
                                    {convertPrice price=$cart->getOrderTotal(true, $blockuser_cart_flag)}
                                {/if}
                            </span>
                        {/if}
                        <span class="ajax_cart_no_product{if $cart_qties > 0} hidden{/if}">{l s='Cart:' mod='responsivetopbar'} {l s='(empty)' mod='responsivetopbar'}</span>
                        <img src="{$img_dir}icon/arrow-29-16-up.png" class="dropdown up" alt=""/>
                        <img src="{$img_dir}icon/arrow-29-16-down.png" class="dropdown down hidden" alt=""/>
                    </a>
                </li>
                {/if}

                <li id="cart_block">
                    <div class="block_content">
                        <!-- block summary -->
                        <div id="cart_block_summary" class="collapsed">
                            <span class="ajax_cart_quantity" {if $cart_qties <= 0}style="display:none;"{/if}>{$cart_qties}</span>
                            <span class="ajax_cart_product_txt_s" {if $cart_qties <= 1}style="display:none"{/if}>{l s='products' mod='responsivetopbar'}</span>
                            <span class="ajax_cart_product_txt" {if $cart_qties > 1}style="display:none"{/if}>{l s='product' mod='responsivetopbar'}</span>
                            <span class="ajax_cart_total" {if $cart_qties == 0}style="display:none"{/if}>
                                {if $cart_qties > 0}
                                    {if $priceDisplay == 1}
                                        {convertPrice price=$cart->getOrderTotal(false)}
                                    {else}
                                        {convertPrice price=$cart->getOrderTotal(true)}
                                    {/if}
                                {/if}
                            </span>
                            <span class="ajax_cart_no_product" {if $cart_qties != 0}style="display:none"{/if}>{l s='Cart:' mod='responsivetopbar'} {l s='(empty)' mod='responsivetopbar'}</span>
                        </div>
                        <!-- block list of products -->
                        <div id="cart_block_list" class="collapsed">
                        {if $products}
                            <dl class="products">
                            {foreach from=$products item='product' name='myLoop'}
                                {assign var='productId' value=$product.id_product}
                                {assign var='productAttributeId' value=$product.id_product_attribute}
                                <dt id="cart_block_product_{$product.id_product}_{if $product.id_product_attribute}{$product.id_product_attribute}{else}0{/if}_{if $product.id_address_delivery}{$product.id_address_delivery}{else}0{/if}" class="{if $smarty.foreach.myLoop.first}first_item{elseif $smarty.foreach.myLoop.last}last_item{else}item{/if}">
                                    <span class="quantity-formated"><span class="quantity">{$product.cart_quantity}</span>x</span>
                                    <a class="cart_block_product_name" href="{$link->getProductLink($product, $product.link_rewrite, $product.category, null, null, $product.id_shop, $product.id_product_attribute)}" title="{$product.name|escape:html:'UTF-8'}">
                                    {$product.name|truncate:13:'...'|escape:html:'UTF-8'}</a>
                                    <span class="remove_link">{if !isset($customizedDatas.$productId.$productAttributeId) && ($product.total > 0)}<a rel="nofollow" class="ajax_cart_block_remove_link" href="{$link->getPageLink('cart', true, NULL, "delete&amp;id_product={$product.id_product}&amp;ipa={$product.id_product_attribute}&amp;id_address_delivery={$product.id_address_delivery}&amp;token={$static_token}", true)}" title="{l s='remove this product from my cart' mod='responsivetopbar'}">&nbsp;</a>{/if}</span>
                                    <span class="price">
                                        {if $product.total > 0}
                                            {if $priceDisplay == $smarty.const.PS_TAX_EXC}{displayWtPrice p="`$product.total`"}{else}{displayWtPrice p="`$product.total_wt`"}{/if}
                                        {else}
                                            <b>{l s='Free!' mod='responsivetopbar'}</b>
                                        {/if}
                                    </span>
                                </dt>
                                {if isset($product.attributes_small)}
                                <dd id="cart_block_combination_of_{$product.id_product}{if $product.id_product_attribute}_{$product.id_product_attribute}{/if}_{$product.id_address_delivery|intval}" class="{if $smarty.foreach.myLoop.first}first_item{elseif $smarty.foreach.myLoop.last}last_item{else}item{/if}">
                                    <a href="{$link->getProductLink($product, $product.link_rewrite, $product.category, null, null, $product.id_shop, $product.id_product_attribute)}" title="{l s='Product detail' mod='responsivetopbar'}">{$product.attributes_small}</a>
                                {/if}

                                <!-- Customizable datas -->
                                {if isset($customizedDatas.$productId.$productAttributeId[$product.id_address_delivery])}
                                    {if !isset($product.attributes_small)}<dd id="cart_block_combination_of_{$product.id_product}_{if $product.id_product_attribute}{$product.id_product_attribute}{else}0{/if}_{if $product.id_address_delivery}{$product.id_address_delivery}{else}0{/if}" class="{if $smarty.foreach.myLoop.first}first_item{elseif $smarty.foreach.myLoop.last}last_item{else}item{/if}">{/if}
                                    <ul class="cart_block_customizations" id="customization_{$productId}_{$productAttributeId}">
                                        {foreach from=$customizedDatas.$productId.$productAttributeId[$product.id_address_delivery] key='id_customization' item='customization' name='customizations'}
                                            <li name="customization">
                                                <div class="deleteCustomizableProduct" id="deleteCustomizableProduct_{$id_customization|intval}_{$product.id_product|intval}_{$product.id_product_attribute|intval}_{$product.id_address_delivery|intval}"><a class="ajax_cart_block_remove_link" href="{$link->getPageLink('cart', true, NULL, "delete&amp;id_product={$product.id_product|intval}&amp;ipa={$product.id_product_attribute|intval}&amp;id_customization={$id_customization}&amp;token={$static_token}", true)}" rel="nofollow"> </a></div>
                                                <span class="quantity-formated"><span class="quantity">{$customization.quantity}</span>x</span>{if isset($customization.datas.$CUSTOMIZE_TEXTFIELD.0)}
                                                {$customization.datas.$CUSTOMIZE_TEXTFIELD.0.value|escape:html:'UTF-8'|replace:"<br />":" "|truncate:28}
                                                {else}
                                                {l s='Customization #%d:' sprintf=$id_customization|intval mod='responsivetopbar'}
                                                {/if}
                                            </li>
                                        {/foreach}
                                    </ul>
                                    {if !isset($product.attributes_small)}</dd>{/if}
                                {/if}

                                {if isset($product.attributes_small)}</dd>{/if}

                            {/foreach}
                            </dl>
                        {/if}
                            <p {if $products}class="hidden"{/if} id="cart_block_no_products">{l s='No products' mod='responsivetopbar'}</p>
                        {if $discounts|@count > 0}
                            <table id="vouchers">
                                <tbody>
                                        {foreach from=$discounts item=discount}
                                            {if $discount.value_real > 0}
                                            <tr class="bloc_cart_voucher" id="bloc_cart_voucher_{$discount.id_discount}">
                                                <td class="quantity">1x</td>
                                                <td class="name" title="{$discount.description}">{$discount.name|cat:' : '|cat:$discount.description|truncate:18:'...'|escape:'htmlall':'UTF-8'}</td>
                                                <td class="price">-{if $priceDisplay == 1}{convertPrice price=$discount.value_tax_exc}{else}{convertPrice price=$discount.value_real}{/if}</td>
                                                <td class="delete">
                                                    {if strlen($discount.code)}
                                                        <a class="delete_voucher" href="{$link->getPageLink('$order_process', true)}?deleteDiscount={$discount.id_discount}" title="{l s='Delete' mod='responsivetopbar'}" rel="nofollow"><img src="{$img_dir}icon/delete.gif" alt="{l s='Delete' mod='responsivetopbar'}" class="icon" /></a>
                                                    {/if}
                                                </td>
                                            </tr>
                                            {/if}
                                        {/foreach}
                                </tbody>
                            </table>
                            {/if}

                            <p id="cart-prices">
                                <span>{l s='Shipping' mod='responsivetopbar'} : </span>
                                <span id="cart_block_shipping_cost" class="price ajax_cart_shipping_cost">{$shipping_cost}</span>
                                <br/>
                                {if $show_wrapping}
                                    {assign var='cart_flag' value='Cart::ONLY_WRAPPING'|constant}
                                    <span>{l s='Wrapping' mod='responsivetopbar'} : </span>
                                    <span id="cart_block_wrapping_cost" class="price cart_block_wrapping_cost">{if $priceDisplay == 1}{convertPrice price=$cart->getOrderTotal(false, $cart_flag)}{else}{convertPrice price=$cart->getOrderTotal(true, $cart_flag)}{/if}</span>
                                    <br/>
                                {/if}
                                {if $show_tax && isset($tax_cost)}
                                    <span>{l s='Tax' mod='responsivetopbar'} : </span>
                                    <span id="cart_block_tax_cost" class="price ajax_cart_tax_cost">{$tax_cost}</span>
                                    <br/>
                                {/if}
                                <span class="total">{l s='Total' mod='responsivetopbar'} : </span>
                                <span id="cart_block_total" class="price ajax_block_cart_total">{$total}</span>
                            </p>
                            {if $use_taxes && $display_tax_label == 1 && $show_tax}
                                {if $priceDisplay == 0}
                                    <p id="cart-price-precisions">
                                        {l s='Prices are tax included' mod='responsivetopbar'}
                                    </p>
                                {/if}
                                {if $priceDisplay == 1}
                                    <p id="cart-price-precisions">
                                        {l s='Prices are tax excluded' mod='responsivetopbar'}
                                    </p>
                                {/if}
                            {/if}
                            <p id="cart-buttons">
                                <a href="{$link->getPageLink("$order_process", true)}" id="button_order_cart" class="exclusive button radius" title="{l s='Check out' mod='responsivetopbar'}" rel="nofollow">{l s='Check out' mod='responsivetopbar'}</a>
                            </p>
                        </div>
                    </div>
                </li>
            </ul>
        </div>

        <script type="text/javascript">
            $(document).ready(function(){
                $('li#first_languages li:not(.selected_language)').css('opacity', 0.5);
                $('li#first_languages li:not(.selected_language)').hover(function(){ldelim}
                    $(this).css('opacity', 1);
                {rdelim}, function(){ldelim}
                    $(this).css('opacity', 0.5);
                {rdelim});

                $('#first_languages').mouseenter(function(){
                    $('#first_languages .other_languages').show();
                }).mouseleave(function(){
                    $('#first_languages .other_languages').hide();
                });
            });
        </script>

        <a id="sidebarButton" class="nav-open sidebar-button"  href="#sidebar"></a>
        <ul class="nav-bar show-for-small">
            <li class="has-flyout">
                {if $logged}
                    <a href="{$link->getPageLink('my-account.php', true)}" title="{l s='Your Account' mod='responsivetopbar'}"><span>{$cookie->customer_firstname} {$cookie->customer_lastname}</span></a>
                {else}
                    <a href="{$link->getPageLink('my-account.php', true)}">{l s='Welcome' mod='responsivetopbar'}, {l s='Log in' mod='responsivetopbar'}</a>
                {/if}
                <a href="#" class="flyout-toggle"><span> </span></a>
                <ul class="flyout">
                    {if !$PS_CATALOG_MODE}
                    <li>
                        <a href="{$link->getPageLink("$order_process.php", true)}" title="{l s='Your Shopping Cart' mod='responsivetopbar'}">
                            {l s='Cart:' mod='responsivetopbar'}
                            <span class="ajax_cart_quantity{if $cart_qties == 0} hidden{/if}">{$cart_qties}</span>
                            <span class="ajax_cart_product_txt{if $cart_qties != 1} hidden{/if}">{l s='product' mod='responsivetopbar'}</span>
                            <span class="ajax_cart_product_txt_s{if $cart_qties < 2} hidden{/if}">{l s='products' mod='responsivetopbar'}</span>
                            {if $cart_qties >= 0}
                                <span class="ajax_cart_total{if $cart_qties == 0} hidden{/if}">
                                    {if $priceDisplay == 1}
                                        {assign var='blockuser_cart_flag' value='Cart::BOTH_WITHOUT_SHIPPING'|constant}
                                        {convertPrice price=$cart->getOrderTotal(false, $blockuser_cart_flag)}
                                    {else}
                                        {assign var='blockuser_cart_flag' value='Cart::BOTH_WITHOUT_SHIPPING'|constant}
                                        {convertPrice price=$cart->getOrderTotal(true, $blockuser_cart_flag)}
                                    {/if}
                                </span>
                            {/if}
                            <span class="ajax_cart_no_product{if $cart_qties > 0} hidden{/if}">{l s='(empty)' mod='responsivetopbar'}</span>
                        </a>
                    </li>
                    {/if}
                    {assign var=langage_inactif_all value=""}
                    {foreach from=$languages key=k item=language name="languages"}
                        {assign var=indice_lang value=$language.id_lang}
                        {if $language.iso_code == $lang_iso}
                            {capture name='langage' assign='langage_actif'}
                                {if isset($lang_rewrite_urls.$indice_lang)}
                                    <li><a href="{$lang_rewrite_urls.$indice_lang|escape:htmlall}" title="{$language.name}">
                                        <img src="{$img_lang_dir}{$language.id_lang}.jpg" alt="{$language.iso_code}" width="16" height="11" /> {$language.name}
                                    </a></li>
                                {else}
                                    <li><a href="{$link->getLanguageLink($language.id_lang)|escape:htmlall}" title="{$language.name}">
                                        <img src="{$img_lang_dir}{$language.id_lang}.jpg" alt="{$language.iso_code}" width="16" height="11" /> {$language.name}
                                    </a></li>
                                {/if}
                            {/capture}
                        {else}
                            {capture name='langage' assign='langage_inactif'}
                                {if $language.iso_code != $lang_iso}
                                    {if isset($lang_rewrite_urls.$indice_lang)}
                                        <li><a href="{$lang_rewrite_urls.$indice_lang|escape:htmlall}" title="{$language.name}">
                                            <img src="{$img_lang_dir}{$language.id_lang}.jpg" alt="{$language.iso_code}" width="16" height="11" /> {$language.name}
                                        </a></li>
                                    {else}
                                        <li><a href="{$link->getLanguageLink($language.id_lang)|escape:htmlall}" title="{$language.name}">
                                            <img src="{$img_lang_dir}{$language.id_lang}.jpg" alt="{$language.iso_code}" width="16" height="11" /> {$language.name}
                                        </a></li>
                                    {/if}
                                {/if}
                            {/capture}

                            {assign var="langage_inactif_all" value=$langage_inactif_all|cat:$langage_inactif}
                        {/if}
                    {/foreach}
                    {$langage_actif}
                    {$langage_inactif_all}
                    {if $logged}
                        <li>
                            <a href="{$link->getPageLink('my-account.php', true)}" title="{l s='Your Account' mod='responsivetopbar'}">{l s='Your Account' mod='responsivetopbar'}</a>
                        </li>
                        <li>
                            <a href="{$link->getPageLink('index.php')}?mylogout" title="{l s='Log me out' mod='responsivetopbar'}">{l s='Log out' mod='responsivetopbar'}</a>
                        </li>
                    {/if}
                </ul>
            </li>
        </ul>
    </div>
</div>