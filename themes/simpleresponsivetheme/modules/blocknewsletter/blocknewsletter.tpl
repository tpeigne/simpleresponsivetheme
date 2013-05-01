{*
* 2007-2013 PrestaShop
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
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<section class="four columns mobile-two" id="newsletter_block">
    <h4>{l s='Newsletter' mod='blocknewsletter'}</h4>
    <div class="block_content">
        {if isset($msg)}
            <p class="{if $nw_error}warning_inline{else}success_inline{/if}">{$msg}</p>
        {/if}
        <form action="{$link->getPageLink('index')}" method="post">
            <div class="row collapse">
                <div class="ten mobile-three columns">
                    <input placeholder="{l s='your e-mail' mod='blocknewsletter'}" type="email" name="email" size="18" value="{if isset($value) && $value}{$value}{/if}"/>
                    {*<select name="action">
                        <option value="0"{if isset($action) && $action == 0} selected="selected"{/if}>{l s='Subscribe' mod='blocknewsletter'}</option>
                        <option value="1"{if isset($action) && $action == 1} selected="selected"{/if}>{l s='Unsubscribe' mod='blocknewsletter'}</option>
                    </select>*}
                </div>
                <div class="two mobile-one columns">
                    <input type="submit" value="Ok" class="button expand postfix submitNewsletter" name="submitNewsletter" />
                    <input type="hidden" name="action" value="0" />
                </div>
            </div>
        </form>
    </div>
    <div class="align_right">
        <a style="margin-right: 15px;" href="" title=""><img src="{$img_dir}html5.png" alt=""/></a>
        <a href="" title=""><img src="{$img_dir}css3.png" alt=""/></a>
    </div>
</section>
