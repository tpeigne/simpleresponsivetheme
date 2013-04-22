<!-- Header -->
<header id="header" class="row">
    <!-- Block responsive links -->
    <div class="twelve columns align_center" id="header_logo">
        <a href="{$link->getPageLink('index.php')}" title="{$shop_name|escape:'htmlall':'UTF-8'}">
            <img class="logo" src="{$logo_url}" alt="{$shop_name|escape:'htmlall':'UTF-8'}" {if $logo_image_width}width="{$logo_image_width}"{/if} {if $logo_image_height}height="{$logo_image_height}" {/if} />
        </a>
    </div>

    <div id="responsive_links_top" class="twelve columns">
        <div class="contain-to-grid">
            <nav class="top-bar">
            {assign var="first_link" value=""}
            {assign var="other_link_all" value=""}
            {foreach from=$responsiveLinks item=responsiveLink name=navResponsiveLink}
                {if $smarty.foreach.navResponsiveLink.first}
                    {capture name='links' assign='first_link'}
                        {assign var='first_link_link' value=''}
                        {assign var='first_link_name' value=''}

                        {if $responsiveLink['responsiveLinkObject']->id_category != 0}
                            {assign var='first_link_link' value={$link->getCategoryLink($responsiveLink['objectLink'])}}
                            {assign var='first_link_name' value={$responsiveLink['objectLink']->name}}
                        {elseif $responsiveLink['responsiveLinkObject']->id_cms != 0}
                            {assign var='first_link_link' value={$link->getCMSLink($responsiveLink['objectLink'])}}
                            {assign var='first_link_name' value={$responsiveLink['objectLink']->meta_title}}
                        {elseif $responsiveLink['responsiveLinkObject']->id_product != 0}
                            {assign var='first_link_link' value={$link->getProductLink($responsiveLink['objectLink'])}}
                            {assign var='first_link_name' value={$responsiveLink['objectLink']->name}}
                        {else}
                            {assign var='first_link_link' value=$responsiveLink['responsiveLinkObject']->url}
                            {assign var='first_link_name' value=$responsiveLink['responsiveLinkObject']->title}
                        {/if}

                        <ul>
                            <li class="name {if $first_link_link == $come_from}active{/if} {if !empty($responsiveLink['subLinks'])}has-dropdown{/if}">
                                <h1>
                                    <a href="{$first_link_link}">
                                        {$first_link_name}
                                    </a>
                                </h1>
                                <ul class="dropdown">
                                    {foreach from=$responsiveLink['subLinks'] item=child name=subLink}
                                        {if $smarty.foreach.subLink.last}
                                            {include file="$branche_tpl_path" node=$child last='true'}
                                        {else}
                                            {include file="$branche_tpl_path" node=$child last='false'}
                                        {/if}
                                    {/foreach}
                                </ul>
                            </li>
                            <li class="toggle-topbar"><a class="button-toggle" href="#"></a></li>
                        </ul>
                    {/capture}
                {else}
                    {capture name='links' assign='other_link'}
                        {assign var='other_link_link' value=''}
                        {assign var='other_link_name' value=''}

                        {if $responsiveLink['responsiveLinkObject']->id_category != 0}
                            {assign var='other_link_link' value={$link->getCategoryLink($responsiveLink['objectLink'])}}
                            {assign var='other_link_name' value={$responsiveLink['objectLink']->name}}
                        {elseif $responsiveLink['responsiveLinkObject']->id_cms != 0}
                            {assign var='other_link_link' value={$link->getCMSLink($responsiveLink['objectLink'])}}
                            {assign var='other_link_name' value={$responsiveLink['objectLink']->meta_title}}
                        {elseif $responsiveLink['responsiveLinkObject']->id_product != 0}
                            {assign var='other_link_link' value={$link->getProductLink($responsiveLink['objectLink'])}}
                            {assign var='other_link_name' value={$responsiveLink['objectLink']->name}}
                        {else}
                            {assign var='other_link_link' value=$responsiveLink['responsiveLinkObject']->url}
                            {assign var='other_link_name' value=$responsiveLink['responsiveLinkObject']->title}
                        {/if}

                        <li class="{if $other_link_link == $come_from}active{/if}{if !empty($responsiveLink['subLinks'])} has-dropdown{/if}">
                            <a href="{$other_link_link}">{$other_link_name}</a>
                            <ul class="dropdown">
                                {foreach from=$responsiveLink['subLinks'] item=child name=subLink}
                                    {if $smarty.foreach.subLink.last}
                                        {include file="$branche_tpl_path" node=$child last='true'}
                                    {else}
                                        {include file="$branche_tpl_path" node=$child last='false'}
                                    {/if}
                                {/foreach}
                            </ul>
                        </li>
                    {/capture}

                    {assign var="other_link_all" value=$other_link_all|cat:$other_link}
                {/if}
            {/foreach}
            {$first_link}
            <section>
                <!-- Left Nav Section -->
                <ul class="left">
                    {$other_link_all}
                </ul>
                <!-- Right Nav Section -->
                <ul class="right">
                  <li class="last">
                      <div id="search_bar" class="twelve mobile-three columns end">
                        <form method="get" action="{$link->getPageLink('search.php', true)}" id="searchbox">
                            <input type="hidden" name="controller" value="search" />
                            <input type="hidden" name="orderby" value="position" />
                            <input type="hidden" name="orderway" value="desc" />
                            <input placeholder="{l s='Search' mod='responsivelinks'}" class="search_query" type="text" id="search_query_block" name="search_query" value="{if isset($smarty.get.search_query)}{$smarty.get.search_query|htmlentities:$ENT_QUOTES:'utf-8'|stripslashes}{/if}" />
                        </form>
                      </div>
                  </li>
                </ul>
              </section>
            </nav>
        </div>
    </div>
    <!-- /Block responsive links -->
</header>

{if $instantsearch}
    <script type="text/javascript">
    // <![CDATA[
        {literal}
        function tryToCloseInstantSearch() {
            if ($('#old_center_column').length > 0)
            {
                $('#center_column').remove();
                $('#old_center_column').attr('id', 'center_column');
                $('#center_column').show();
                return false;
            }
        }

        instantSearchQueries = new Array();
        function stopInstantSearchQueries(){
            for(i=0;i<instantSearchQueries.length;i++) {
                instantSearchQueries[i].abort();
            }
            instantSearchQueries = new Array();
        }

        $("#search_query_block").keyup(function(){
            if ($(this).val().length > 0) {
                stopInstantSearchQueries();
                instantSearchQuery = $.ajax({
                    url: '{if $search_ssl == 1}{$link->getPageLink('search', true)}{else}{$link->getPageLink('search')}{/if}',
                    data: {
                        instantSearch: 1,
                        id_lang: {$cookie->id_lang},
                        q: $(this).val()
                    },
                    dataType: 'html',
                    type: 'POST',
                    success: function(data){
                        if ($("#search_query_block").val().length > 0) {
                            tryToCloseInstantSearch();
                            $('#center_column').attr('id', 'old_center_column');
                            $('#old_center_column').after('<section id="center_column" class="twelve columns">'+data+'</section>');
                            $('#old_center_column').hide();
                            $("#instant_search_results a.close").click(function() {
                                $("#search_query_block").val('');
                                return tryToCloseInstantSearch();
                            });
                            return false;
                        }
                        else
                            tryToCloseInstantSearch();
                        }
                });
                instantSearchQueries.push(instantSearchQuery);
            }
            else
                tryToCloseInstantSearch();
        });
    // ]]>
    {/literal}
    </script>
{/if}

{if $ajaxsearch}
    <script type="text/javascript">
    // <![CDATA[
    {literal}
        $('document').ready( function() {
            $("#search_query_block")
                .autocomplete(
                        '{if $search_ssl == 1}{$link->getPageLink('search', true)}{else}{$link->getPageLink('search')}{/if}', {
                        minChars: 3,
                        max: 10,
                        width: 500,
                        selectFirst: false,
                        scroll: false,
                        dataType: "json",
                        formatItem: function(data, i, max, value, term) {
                            return value;
                        },
                        parse: function(data) {
                            var mytab = new Array();
                            for (var i = 0; i < data.length; i++)
                                mytab[mytab.length] = { data: data[i], value: data[i].cname + ' > ' + data[i].pname };
                            return mytab;
                        },
                        extraParams: {
                            ajaxSearch: 1,
                            id_lang: {$cookie->id_lang}
                        }
                    }
                )
                .result(function(event, data, formatted) {
                    $('#search_query_block').val(data.pname);
                    document.location.href = data.product_link;
                })
        });
    {/literal}
    // ]]>
    </script>
{/if}