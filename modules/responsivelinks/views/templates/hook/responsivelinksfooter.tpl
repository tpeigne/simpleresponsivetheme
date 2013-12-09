{assign var="browse_links" value=""}
{assign var="siteinfo_links" value=""}
{foreach from=$footerLinks item=responsiveLink name=navResponsiveLink}
    {if $responsiveLink['responsiveLinkObject']->page_category_column == 1}
        {assign var='link_link' value=''}
        {assign var='link_name' value=''}

        {if $responsiveLink['responsiveLinkObject']->id_category != 0}
            {assign var='link_link' value={$link->getCategoryLink($responsiveLink['objectLink'])}}
            {assign var='link_name' value={$responsiveLink['objectLink']->name}}
        {elseif $responsiveLink['responsiveLinkObject']->id_cms != 0}
            {assign var='link_link' value={$link->getCMSLink($responsiveLink['objectLink'])}}
            {assign var='link_name' value={$responsiveLink['objectLink']->meta_title}}
        {elseif $responsiveLink['responsiveLinkObject']->id_cms_category != 0}
            {assign var='link_link' value={$link->getCMSCategoryLink($responsiveLink['objectLink'])}}
            {assign var='link_name' value={$responsiveLink['objectLink']->name}}
        {elseif $responsiveLink['responsiveLinkObject']->id_product != 0}
            {assign var='link_link' value={$link->getProductLink($responsiveLink['objectLink'])}}
            {assign var='link_name' value={$responsiveLink['objectLink']->name}}
        {else}
            {assign var='link_link' value=$responsiveLink['responsiveLinkObject']->url}
            {assign var='link_name' value=$responsiveLink['responsiveLinkObject']->title}
        {/if}

        {capture name='links' assign='onLink'}
            <li><a href="{$link_link}" title="{$link_name}">{$link_name}</a></li>
        {/capture}

        {assign var="browse_links" value=$browse_links|cat:$onLink}
    {else}
        {assign var='link_link' value=''}
        {assign var='link_name' value=''}

        {if $responsiveLink['responsiveLinkObject']->id_category != 0}
            {assign var='link_link' value={$link->getCategoryLink($responsiveLink['objectLink'])}}
            {assign var='link_name' value={$responsiveLink['objectLink']->name}}
        {elseif $responsiveLink['responsiveLinkObject']->id_cms != 0}
            {assign var='link_link' value={$link->getCMSLink($responsiveLink['objectLink'])}}
            {assign var='link_name' value={$responsiveLink['objectLink']->meta_title}}
        {elseif $responsiveLink['responsiveLinkObject']->id_cms_category != 0}
            {assign var='link_link' value={$link->getCMSCategoryLink($responsiveLink['objectLink'])}}
            {assign var='link_name' value={$responsiveLink['objectLink']->name}}
        {elseif $responsiveLink['responsiveLinkObject']->id_product != 0}
            {assign var='link_link' value={$link->getProductLink($responsiveLink['objectLink'])}}
            {assign var='link_name' value={$responsiveLink['objectLink']->name}}
        {else}
            {assign var='link_link' value=$responsiveLink['responsiveLinkObject']->url}
            {assign var='link_name' value=$responsiveLink['responsiveLinkObject']->title}
        {/if}

        {capture name='links' assign='onLink'}
            <li><a href="{$link_link}" title="{$link_name}">{$link_name}</a></li>
        {/capture}

        {assign var="siteinfo_links" value=$siteinfo_links|cat:$onLink}
    {/if}
{/foreach}

<section class="three columns mobile-two">
    <h4>{l s='Browse' mod='responsivelinks'}</h4>
    {if $browse_links == ''}
        <div class="alert-box">
            {l s='No links found' mod='responsivelinks'}
            <a href="" class="close">&times;</a>
        </div>
    {else}
        <ul class="clearfix">
            {$browse_links}
        </ul>
    {/if}
</section>

<section class="three columns mobile-two">
    <h4>{l s='Site Info' mod='responsivelinks'}</h4>
    {if $siteinfo_links == ''}
        <div class="alert-box">
            {l s='No links found' mod='responsivelinks'}
            <a href="" class="close">&times;</a>
        </div>
    {else}
        <ul class="clearfix">
            {$siteinfo_links}
        </ul>
    {/if}
</section>

<section class="three columns mobile-two" id="follow_us_footer">
    <h4>{l s='Follow Us...' mod='responsivelinks'}</h4>
    <ul class="clearfix">
        {if isset($responsiveLinksConfiguration) && $responsiveLinksConfiguration.FOLLOWFACEBOOK.option == 1}
            <li>
                <a href="{$responsiveLinksConfiguration.FOLLOWFACEBOOK.value}" title="{l s='Facebook' mod='responsivelinks'}"><img src="{$img_dir}social-facebook.png" alt="{l s='Facebook' mod='responsivelinks'}" width="38" height="38"/></a>
            </li>
        {/if}
        {if isset($responsiveLinksConfiguration) && $responsiveLinksConfiguration.FOLLOWYOUTUBE.option == 1}
            <li>
                <a href="{$responsiveLinksConfiguration.FOLLOWYOUTUBE.value}" title="{l s='YouTube' mod='responsivelinks'}"><img src="{$img_dir}social-youtube.png" alt="{l s='YouTube' mod='responsivelinks'}" width="38" height="38"/></a>
            </li>
        {/if}
        {if isset($responsiveLinksConfiguration) && $responsiveLinksConfiguration.FOLLOWTWITTER.option == 1}
            <li class="last">
                <a href="{$responsiveLinksConfiguration.FOLLOWTWITTER.value}" title="{l s='Twitter' mod='responsivelinks'}"><img src="{$img_dir}social-twitter.png" alt="{l s='Twitter' mod='responsivelinks'}" width="38" height="38"/></a>
            </li>
        {/if}
    </ul>
</section>
