<div id="featured_products">
    {foreach from=$listeCategory item=category name=categoryHome}
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
                                {if $product.product->specificPrice && isset($product.product->specificPrice.reduction)}
                                    {if $product.product->specificPrice.reduction_type == "percentage" or $product.product->specificPrice.reduction_type == "amount"}
                                        <span class="new">{l s='Reduced price !' mod='responsivehomefeatured'}</span>
                                    {/if}
                                {/if}
                                <img src="{$product.product_image}" height="205" width="205" alt="{$product.product->name|escape:html:'UTF-8'}" />
                            </a>
                            <h5 class="align_center">
                                <a href="{$link->getProductLink($product.product)}" title="{$product.product->name|escape:html:'UTF-8'}">{$product.product->name|truncate:30:'...'|escape:'htmlall':'UTF-8'}</a>
                            </h5>
                            <div class="product_price align_center">
                                {if $product.product->show_price AND !isset($restricted_country_mode) AND !$PS_CATALOG_MODE}
                                    <span class="price">
                                        {if !$priceDisplay}
                                            {convertPrice price=$product.product->price}
                                        {else}
                                            {convertPrice price=$product.product->price_tax_exc}
                                        {/if}
                                    </span>
                                    {if $product.product->specificPrice && isset($product.product->specificPrice.reduction)}
                                        {if $product.product->specificPrice.reduction_type == "percentage" or $product.product->specificPrice.reduction_type == "amount"}
                                        <span class="original_price">
                                            {if $product.product->specificPrice.reduction_type == "percentage"}
                                                {convertPrice  price=(($product.product->price * $product.product->specificPrice.reduction) + $product.product->price)}
                                            {/if}

                                            {if $product.product->specificPrice.reduction_type == "amount"}
                                                {convertPrice  price=$product.product->price + $product.product->specificPrice.reduction}
                                            {/if}
                                        </span>
                                        {/if}
                                    {/if}
                                {/if}
                            </div>
                            <div class="align_justify italic product_description_short">
                                {$product.product->description_short|truncate:180:'...'|strip_tags:escape:'html':'UTF-8'}
                            </div>
                        </li>
                    {/foreach}
                    </ul>
                {else}
                    <p class="twelve columns">{l s='No featured products' mod='responsivehomefeatured'}</p>
                {/if}
            </div>
        </div>
    </section>
    {/foreach}
</div>
