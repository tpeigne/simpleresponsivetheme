<?php

if (!defined('_PS_VERSION_'))
    exit;

function upgrade_module_2_3($object)
{
    if (!Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'responsivelinks` ADD `id_cms_category` INT UNSIGNED NOT NULL'))
        return false;

    if (!Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'responsivelinks` DROP `id_shop`'))
        return false;

    return true;
}