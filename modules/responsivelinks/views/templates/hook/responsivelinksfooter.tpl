<section class="two columns mobile-two">
    <h4>{l s='Browse' mod='responsivelinks'}</h4>
    <ul class="clearfix">
        <li {if $page_name == 'index'}class="active"{/if}><a href="{$link->getPageLink('index.php')}" title="{l s='Home' mod='responsivelinks'}">{l s='Home' mod='responsivelinks'}</a></li>
        <li><a href="#" title="{l s='Our Products' mod='responsivelinks'}">{l s='Our Products' mod='responsivelinks'}</a></li>
        <li><a href="{$link->getPageLink('contact-form.php')}" title="{l s='Contact' mod='responsivelinks'}">{l s='Contact' mod='responsivelinks'}</a></li>
        <li><a href="#" title="{l s='Links' mod='responsivelinks'}">{l s='Links' mod='responsivelinks'}</a></li>
        <li><a href="{$link->getPageLink('my-account.php', true)}" title="{l s='Login' mod='responsivelinks'}">{l s='Login' mod='responsivelinks'}</a></li>
        <li><a href="{$link->getPageLink('my-account.php', true)}" title="{l s='Register' mod='responsivelinks'}">{l s='Register' mod='responsivelinks'}</a></li>
    </ul>
</section>

<section class="three columns mobile-two">
    <h4>{l s='Site Info' mod='responsivelinks'}</h4>
    <ul class="clearfix">
        <li><a href="{$link->getCMSLink(4)}" title="{l s='About Us' mod='responsivelinks'}">{l s='About Us' mod='responsivelinks'}</a></li>
        <li><a href="{$link->getCMSLink(1)}" title="{l s='Delivery' mod='responsivelinks'}">{l s='Delivery' mod='responsivelinks'}</a></li>
        <li><a href="{$link->getCMSLink(3)}" title="{l s='Legal Notice' mod='responsivelinks'}">{l s='Legal Notice' mod='responsivelinks'}</a></li>
        <li><a href="{$link->getCMSLink(2)}" title="{l s='Terms and conditions of use' mod='responsivelinks'}">{l s='Terms and conditions of use' mod='responsivelinks'}</a></li>
        <li><a href="{$link->getCMSLink(5)}" title="{l s='Secure payment' mod='responsivelinks'}">{l s='Secure payment' mod='responsivelinks'}</a></li>
        <li><a href="{$link->getCMSLink(6)}" title="{l s='Privacy policy' mod='responsivelinks'}">{l s='Privacy policy' mod='responsivelinks'}</a></li>
        <li><a href="{$link->getPageLink('stores.php')}" title="{l s='Stores' mod='responsivelinks'}">{l s='Stores' mod='responsivelinks'}</a></li>
        <li><a href="{$link->getPageLink('sitemap.php')}" title="{l s='Sitemap' mod='responsivelinks'}">{l s='Sitemap' mod='responsivelinks'}</a></li>
    </ul>
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
