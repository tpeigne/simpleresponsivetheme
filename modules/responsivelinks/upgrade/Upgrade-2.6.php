<?php

if (!defined('_PS_VERSION_'))
    exit;

function upgrade_module_2_6($object)
{
    if (!Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'responsivelinks` ADD `date_add` datetime NOT NULL'))
        return false;

    if (!Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'responsivelinks` ADD `date_upd` datetime NOT NULL'))
        return false;

    return true;
}