<?php

include_once('../../config/config.inc.php');
include_once('../../init.php');
include_once('responsivelinks.php');

$responsiveLinks = new ResponsiveLinks();

if (Tools::getValue('action') == 'deleteLink') {
    $link = new ResponsiveLinksClass(Tools::getValue('idLink'));
    $response = '';

    //delete all the sub links for this link
    if(!$link->deleteSubLinks()) {
        return false;
    }

    if ($link->delete()) {
        $response = '
        <div class="conf confirm">
            '.$responsiveLinks->l('Links have been deleted').'
        </div>';
    } else {
        $response = '
        <div class="conf error">
            '.$responsiveLinks->l('An error occurred while deleting links').'
        </div>';
    }

    echo $response;
    exit();
}

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