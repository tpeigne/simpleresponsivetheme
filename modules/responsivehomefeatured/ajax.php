<?php

include_once('../../config/config.inc.php');
include_once('../../init.php');
include_once('responsivehomefeatured.php');

$homeFeatured = new ResponsiveHomeFeatured();

if (Tools::getValue('action') == 'deleteHomeFeatured')
{
	$responsiveHomeFeatured = new ResponsiveHomeFeaturedClass((int)Tools::getValue('idHomeFeatured'));

	if ($responsiveHomeFeatured->delete())
	{
		if(ResponsiveHomeFeaturedClass::deleteHomeFeaturedProduct((int)Tools::getValue('idHomeFeatured'))){
			$response = '
			<div class="conf confirm">
				'.$homeFeatured->l('The category has been deleted.').'
			</div>';
		}
	}
	else
	{
		$response = '
		<div class="conf error">
			'.$homeFeatured->l('An error has occured while deleting the category.').'
		</div>';
	}

	echo $response;
	exit();
}

if (Tools::getValue('action') == 'deleteHomeFeaturedProduct')
{
	if (ResponsiveHomeFeaturedClass::deleteProduct((int)Tools::getValue('idHomeFeaturedProduct')))
	{
		$response = '
		<div class="conf confirm">
			'.$homeFeatured->l('The product has been deleted.').'
		</div>';
	}
	else
	{
		$response = '
		<div class="conf error">
			'.$homeFeatured->l('An error has occured while deleting the product.').'
		</div>';
	}

	echo $response;
	exit();
}

if (Tools::getValue('action') == 'getProductList')
{
	global $cookie;
	$response = array();
	$i = 0;
	$category = new Category((int)Tools::getValue('idCategory'), (int)$cookie->id_lang);

	$products = $category->getProducts((int)$cookie->id_lang, 1, 100);
	if(!empty($products)){
		foreach($products as $product){
			$response[$i]['id_product'] = $product['id_product'];
			$response[$i]['name']       = $product['name'];
			
			$i++;
		}
	}

	echo json_encode($response);
	exit();
}

if (Tools::getValue('action') == 'updatePositionHomeFeatured')
{
	$id_responsive_homefeatured = (int)(Tools::getValue('id_responsive_homefeatured'));
	$way = (int)(Tools::getValue('way'));
	$responsiveHomeFeatured = new ResponsiveHomeFeaturedClass($id_responsive_homefeatured);
	$positions = Tools::getValue('categories');	
	
	if (Validate::isLoadedObject($responsiveHomeFeatured))
		if ($responsiveHomeFeatured->updatePosition($positions))
			die(true);
		else
			die('{"hasError" : true, "errors" : "Can not update category position"}');
	else
		die('{"hasError" : true, "errors" : "This category can not be loaded"}');
		
	exit();
}

?>