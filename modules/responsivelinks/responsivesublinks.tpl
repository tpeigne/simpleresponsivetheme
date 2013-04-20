{assign var='other_link_link' value=''}
{assign var='other_link_name' value=''}

{if $node['responsiveLinkObject']->id_category <> 0}
    {assign var='other_link_link' value={$link->getCategoryLink($node['objectLink'])}}
    {assign var='other_link_name' value={$node['objectLink']->name}}
{elseif $node['responsiveLinkObject']->id_cms <> 0}
    {assign var='other_link_link' value={$link->getCMSLink($node['objectLink'])}}
    {assign var='other_link_name' value={$node['objectLink']->meta_title}}
{elseif $node['responsiveLinkObject']->id_product <> 0}
    {assign var='other_link_link' value={$link->getProductLink($node['objectLink'])}}
    {assign var='other_link_name' value={$node['objectLink']->name}}
{else}
    {assign var='other_link_link' value=$node['responsiveLinkObject']->url}
    {assign var='other_link_name' value=$node['responsiveLinkObject']->title}
{/if}

<li {if $node['subLinks']|@count > 0}class="has-dropdown"{/if}>
    <a href="{$other_link_link}">{$other_link_name}</a>
    {if $node['subLinks']|@count > 0}
        <ul class="dropdown">
            {foreach from=$node['subLinks'] item=child name=subLink}
                {if $smarty.foreach.subLink.last}
                    {include file="$branche_tpl_path" node=$child last='true'}
                {else}
                    {include file="$branche_tpl_path" node=$child last='false'}
                {/if}
            {/foreach}
        </ul>
    {/if}
</li>
{if isset($last) && $last == 'false'}
    <li class="divider"></li>
{/if}