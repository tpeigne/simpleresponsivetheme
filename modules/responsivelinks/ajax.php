<?php

include_once('../../config/config.inc.php');
include_once('../../init.php');
include_once('responsivelinks.php');

$responsiveLinks = new ResponsiveLinks();

if (Tools::getValue('action') == 'updatePositionLinks') {
    $idLink = explode('node-', Tools::getValue('id_link'));
    $responsiveLinks = new ResponsiveLinksClass($idLink[1]);
    $positions = Tools::getValue('links');

    if (Validate::isLoadedObject($responsiveLinks)) {
        if ($responsiveLinks->updatePosition($positions)) {
            if(true) {
                die(true);
            } else {
                die('{"hasError" : true, "errors" : "Can not update link position"}');
            }
        }
    } else {
        die('{"hasError" : true, "errors" : "This link can not be loaded"}');
    }

    exit();
}

exit();