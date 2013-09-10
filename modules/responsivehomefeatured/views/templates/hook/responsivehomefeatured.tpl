<div id="featured_products">
    {foreach from=$categoryList item=category name=categoryHome}
    <section class="row">
        <div class="three columns category_description align_justify">
            <div class="border_category_description italic">
                <h3><a href="{$link->getCategoryLink($category.category)}" title="{$category.category->name}">{$category.category->name}</a></h3>
                <p>{$category.category->description|strip_tags:escape:'html':'UTF-8'}</p>
            </div>
        </div>
        <div class="nine columns products_content">
            <div class="border_products_content">
                {if isset($category.products) AND $category.products}
                    <ul class="product_list block-grid three-up mobile-two-up">
                    {foreach from=$category.products item=product name=homeFeaturedProducts}
                        <li class="ajax_block_product">
                            <a class="product_image" href="{$link->getProductLink($product.product)}" title="{$product.product->name|escape:html:'UTF-8'}">
                                {if isset($product.reduction) && $product.reduction && isset($product.product->show_price) && $product.product->show_price && !$PS_CATALOG_MODE}
                                    <span class="advert">{l s='Reduced price !' mod='responsivehomefeatured'}</span>
                                {/if}
                                <img src="{$product.image}" height="205" width="205" alt="{$product.product->name|escape:html:'UTF-8'}" />
                            </a>
                            <h5 class="align_center">
                                <a href="{$link->getProductLink($product.product)}" title="{$product.product->name|escape:html:'UTF-8'}">{$product.product->name|truncate:30:'...'|escape:'htmlall':'UTF-8'}</a>
                            </h5>
                            <div class="product_price align_center">
                                {if isset($product.product->show_price) && $product.product->show_price AND !$PS_CATALOG_MODE}
                                    <span class="price">
                                        {if !$priceDisplay}
                                            {convertPrice price=$product.price_tax_inc}
                                        {else}
                                            {convertPrice price=$product.product->price}
                                        {/if}
                                    </span>
                                    {if $product.reduction && isset($product.reduction)}
                                        <span class="original_price">
                                            {convertPrice price=$product.price_without_reduction}
                                        </span>
                                    {/if}
                                {/if}
                            </div>
                            <div class="align_justify italic product_description_short">
                                {$product.product->description_short|truncate:150:'...'|strip_tags:escape:'html':'UTF-8'}
                            </div>
                        </li>
                    {/foreach}
                    </ul>
                {else}
                    <p>{l s='No featured products' mod='responsivehomefeatured'}</p>
                {/if}
            </div>
        </div>
    </section>
    {/foreach}
</div>
