<?php

if (!defined('_PS_VERSION_'))
    exit;

function upgrade_module_2_5($object)
{
    if (!Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'responsivelinks` DROP `id_child`'))
        return false;

    if (!Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'responsivelinks` ADD `page_category` VARCHAR( 50 ) NOT NULL'))
        return false;

    if (!Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'responsivelinks` ADD `page_category_column` INT UNSIGNED NOT NULL'))
        return false;

    //update previous installed links
    if (!Db::getInstance()->update('responsivelinks', array('page_category' => 'header')))
        return false;

    return true;
}