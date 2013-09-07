<!-- Block categories module -->
<div id="categories_block_left" class="block">
    <h4 class="title_block">{l s='Categories' mod='blockcategories'}</h4>
    <ul class="tree {if $isDhtml}dhtml{/if}">
    {foreach from=$blockCategTree.children item=child name=blockCategTree}
        {if $smarty.foreach.blockCategTree.last}
            {include file="$branche_tpl_path" node=$child last='true'}
        {else}
            {include file="$branche_tpl_path" node=$child}
        {/if}
    {/foreach}
    </ul>
    {* Javascript moved here to fix bug #PSCFI-151 *}
    <script type="text/javascript">
    // <![CDATA[
        // we hide the tree only if JavaScript is activated
        $('div#categories_block_left ul.dhtml').hide();
    // ]]>
    </script>
</div>
<!-- /Block categories module -->
