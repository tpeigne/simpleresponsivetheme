<?php

include_once('../../config/config.inc.php');
include_once('../../init.php');
include_once('responsivehomefeatured.php');

$homeFeatured = new ResponsiveHomeFeatured();

if (Tools::getValue('action') == 'updatePositionHomeFeatured') {
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