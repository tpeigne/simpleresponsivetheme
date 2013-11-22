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

{* Assign a value to 'current_step' to display current style *}
{capture name="url_back"}
{if isset($back) && $back}back={$back}{/if}
{/capture}

{if !isset($multi_shipping)}
    {assign var='multi_shipping' value='0'}
{/if}

{if !$opc}
<!-- Steps -->
<ul class="step block-grid five-up" id="order_step">
    <li class="{if $current_step=='summary'}step_current{else}{if $current_step=='payment' || $current_step=='shipping' || $current_step=='address' || $current_step=='login'}step_done{else}step_todo{/if}{/if}">
        {if $current_step=='payment' || $current_step=='shipping' || $current_step=='address' || $current_step=='login'}
		<a href="{$link->getPageLink('order', true)}">
            <span class="bullet">1</span> <span class="content">{l s='Summary'}</span>
        </a>
        {else}
            <span><span class="bullet">1</span> <span class="content">{l s='Summary'}</span></span>
        {/if}
    </li>
    <li class="{if $current_step=='login'}step_current{else}{if $current_step=='payment' || $current_step=='shipping' || $current_step=='address'}step_done{else}step_todo{/if}{/if}">
        {if $current_step=='payment' || $current_step=='shipping' || $current_step=='address'}
		<a href="{$link->getPageLink('order', true, NULL, "{$smarty.capture.url_back}&step=1&multi-shipping={$multi_shipping}")|escape:'html'}">
            <span class="bullet">2</span> <span class="content">{l s='Login'}</span>
        </a>
        {else}
            <span><span class="bullet">2</span> <span class="content">{l s='Login'}</span></span>
        {/if}
    </li>
    <li class="{if $current_step=='address'}step_current{else}{if $current_step=='payment' || $current_step=='shipping'}step_done{else}step_todo{/if}{/if}">
        {if $current_step=='payment' || $current_step=='shipping'}
		<a href="{$link->getPageLink('order', true, NULL, "{$smarty.capture.url_back}&step=1&multi-shipping={$multi_shipping}")|escape:'html'}">
            <span class="bullet">3</span> <span class="content">{l s='Address'}</span>
        </a>
        {else}
            <span><span class="bullet">3</span> <span class="content">{l s='Address'}</span></span>
        {/if}
    </li>
    <li class="{if $current_step=='shipping'}step_current{else}{if $current_step=='payment'}step_done{else}step_todo{/if}{/if}">
        {if $current_step=='payment'}
		<a href="{$link->getPageLink('order', true, NULL, "{$smarty.capture.url_back}&step=2&multi-shipping={$multi_shipping}")|escape:'html'}">
            <span class="bullet">4</span> <span class="content">{l s='Shipping'}</span>
        </a>
        {else}
            <span><span class="bullet">4</span> <span class="content">{l s='Shipping'}</span></span>
        {/if}
    </li>
    <li id="step_end" class="{if $current_step=='payment'}step_current_end{else}step_todo{/if}">
        <span><span class="bullet">5</span> <span class="content">{l s='Payment'}</span></span>
    </li>
</ul>
<!-- /Steps -->
{/if}
