{*
* 2007-2012 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 7457 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{if isset($products)}
    <!-- Products list -->
    <ul id="product_list" class="product_list block-grid four-up mobile-two-up">
        {foreach from=$products item=product name=products}
            <li class="ajax_block_product">
                <a class="product_image" href="{$product.link|escape:'htmlall':'UTF-8'}" title="{$product.name|escape:'htmlall':'UTF-8'}">
                    {if isset($product.reduction) && $product.reduction && isset($product.show_price) && $product.show_price && !$PS_CATALOG_MODE}
                        <span class="new">{l s='Reduced price!'}</span>
                    {/if}
                    <img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'large_default')}" alt="{$product.legend|escape:'htmlall':'UTF-8'}" {if isset($homeSize)} width="{$homeSize.width}" height="{$homeSize.height}"{/if} />
                </a>

                <h5 class="align_center">
                    <a href="{$product.link|escape:'htmlall':'UTF-8'}" title="{$product.name|escape:'htmlall':'UTF-8'}">{$product.name|escape:'htmlall':'UTF-8'|truncate:35:'...'}</a>
                </h5>

                <div class="product_price align_center">
                    {if isset($product.show_price) && $product.show_price && !isset($restricted_country_mode)}
                        <span class="price">
                            {if !$priceDisplay}
                                {convertPrice price=$product.price}
                            {else}
                                {convertPrice price=$product.price_tax_exc}
                            {/if}
                        </span>
                        {if isset($product.reduction) && $product.reduction && isset($product.show_price) && $product.show_price && !$PS_CATALOG_MODE}
                            <span class="original_price">
                                {convertPrice price=$product.price_without_reduction}
                            </span>
                        {/if}
                    {/if}
                    {*{if isset($product.available_for_order) && $product.available_for_order && !isset($restricted_country_mode)}
                        <span class="availability">
                            {if ($product.allow_oosp || $product.quantity > 0)}
                                {l s='Available'}
                            {elseif (isset($product.quantity_all_versions) && $product.quantity_all_versions > 0)}
                                {l s='Product available with different options'}
                            {else}
                                {l s='Out of stock'}
                            {/if}
                        </span>
                    {/if}*}
                </div>

                {*{if isset($product.on_sale) && $product.on_sale && isset($product.show_price) && $product.show_price && !$PS_CATALOG_MODE}
                    <span class="on_sale">{l s='On sale!'}</span>
                {elseif isset($product.reduction) && $product.reduction && isset($product.show_price) && $product.show_price && !$PS_CATALOG_MODE}
                    <span class="discount">{l s='Reduced price!'}</span>
                {/if}

                {if (!$PS_CATALOG_MODE AND ((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
                    {if isset($product.online_only) && $product.online_only}
                        <span class="online_only">{l s='Online only!'}</span>
                    {/if}
                {/if}*}

                <div class="align_justify italic product_description_short">
                    {$product.description_short|strip_tags:'UTF-8'|truncate:150:'...'}
                </div>

                <div class="add_block">
                    {if ($product.id_product_attribute == 0 || (isset($add_prod_display) && ($add_prod_display == 1))) && $product.available_for_order && !isset($restricted_country_mode) && $product.minimal_quantity <= 1 && $product.customizable != 2 && !$PS_CATALOG_MODE}
                        {if ($product.allow_oosp || $product.quantity > 0)}
                            {if isset($static_token)}
                                <a class="button radius ajax_add_to_cart_button exclusive" rel="ajax_id_product_{$product.id_product|intval}" href="{$link->getPageLink('cart',false, NULL, "add&amp;id_product={$product.id_product|intval}&amp;token={$static_token}", false)}" title="{l s='Add to cart'}"><span></span>{l s='Add to cart'}</a>
                            {else}
                                <a class="button radius ajax_add_to_cart_button exclusive" rel="ajax_id_product_{$product.id_product|intval}" href="{$link->getPageLink('cart',false, NULL, "add&amp;id_product={$product.id_product|intval}", false)} title="{l s='Add to cart'}"><span></span>{l s='Add to cart'}</a>
                            {/if}
                        {else}
                            <span class="exclusive"><span></span>{l s='Add to cart'}</span><br />
                        {/if}
                    {/if}
                </div>

                {if isset($comparator_max_item) && $comparator_max_item}
                    <p class="compare">
                        <input type="checkbox" class="comparator" id="comparator_item_{$product.id_product}" value="comparator_item_{$product.id_product}" {if isset($compareProducts) && in_array($product.id_product, $compareProducts)}checked="checked"{/if} />
                        <label for="comparator_item_{$product.id_product}">{l s='Select to compare'}</label>
                    </p>
                {/if}
            </li>
        {/foreach}
    </ul>
    <!-- /Products list -->
{/if}