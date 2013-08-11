<?php

/**
  * ResponsiveProductShare module for Prestashop, responsiveproductshare.php
  *
  * Created by Thomas Peigné (thomas.peigne@gmail.com)
  */

if (!defined('_PS_VERSION_'))
    exit;

class ResponsiveProductShare extends Module
{
    public function __construct()
    {
        $this->name = 'responsiveproductshare';
        $this->tab = 'front_office_features';
        $this->version = '1.0';
        $this->author = 'Thomas Peigné';
        $this->need_instance = 0;

        parent::__construct();

        $this->displayName = $this->l('Responsive product share');
        $this->description = $this->l('Adds a block on your product page to share it');
    }

    public function install()
    {
        if (!parent::install() OR !$this->registerHook('extraLeft'))
            return false;
        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall())
            return false;
        return true;
    }

    public function hookExtraLeft()
    {
        return $this->display(__FILE__, 'responsiveproductshare.tpl');
    }

    public function getContent()
    {
        $this->_html = '<h2>'.$this->displayName.'</h2>';
    }

    private function _displayForm()
    {

    }
}
