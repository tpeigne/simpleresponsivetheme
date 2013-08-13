<?php

if (!defined('_PS_VERSION_'))
    exit;

function upgrade_module_2_4($object)
{
    if (!Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'responsivelinks` DROP `page_category`'))
        return false;

    if (!Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'responsivelinks` DROP `page_category_column`'))
        return false;

    return true;
}