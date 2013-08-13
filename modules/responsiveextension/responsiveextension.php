<?php

/**
  * ResponsiveExtension module for Prestashop, responsiveextension.php
  *
  * Created by Thomas Peigné (thomas.peigne@gmail.com)
  */

if (!defined('_PS_VERSION_'))
    exit;

class ResponsiveExtension extends Module
{
    public function __construct()
    {
        $this->name = 'responsiveextension';
        $this->tab = 'front_office_features';
        $this->version = '3.2.5';
        $this->author = 'Thomas Peigné';
        $this->need_instance = 0;

        parent::__construct();

        $this->displayName = $this->l('Responsive extension');
        $this->description = $this->l('Enable and configure your responsive theme.');
    }

    public function install()
    {
        if (parent::install() == false OR !$this->registerHook('header'))
            return false;

        //basic configuration
        $responsiveConfiguration = array();
        $responsiveConfiguration['ACCORDION'] = 0;
        $responsiveConfiguration['ALERTS'] = 0;
        $responsiveConfiguration['BUTTONS'] = 0;
        $responsiveConfiguration['CLEARING'] = 0;
        $responsiveConfiguration['FORMS'] = 0;
        $responsiveConfiguration['JOYRIDE'] = 0;
        $responsiveConfiguration['MAGELLAN'] = 0;
        $responsiveConfiguration['MEDIAQUERYTOGGLE'] = 0;
        $responsiveConfiguration['NAVIGATION'] = 1;
        $responsiveConfiguration['ORBIT'] = 1;
        $responsiveConfiguration['REVEAL'] = 1;
        $responsiveConfiguration['TABS'] = 0;
        $responsiveConfiguration['TOOLTIPS'] = 0;
        $responsiveConfiguration['TOPBAR'] = 1;
        $responsiveConfiguration['PLACEHOLDER'] = 1;
        $responsiveConfiguration['MODERNIZR'] = 1;

        $responsiveConfiguration['IE6'] = 1;

        //configuration update
        Configuration::updateValue('RESPONSIVE_EXTENSION', serialize($responsiveConfiguration));

        return true;
    }


    public function uninstall()
    {
        // Uninstall Module
        if (!parent::uninstall())
            return false;

        return true;
    }

    public function getContent()
    {
        $this->_html = '<h2 id="module-title">'.$this->displayName.'</h2>';

        if (Tools::isSubmit('submitSaveConfiguration')) {
            //get data from post method
            $responsiveConfiguration = array();
            $responsiveConfiguration['ACCORDION'] = (int)Tools::getValue('accordion');
            $responsiveConfiguration['ALERTS'] = (int)Tools::getValue('alerts');
            $responsiveConfiguration['BUTTONS'] = (int)Tools::getValue('buttons');
            $responsiveConfiguration['CLEARING'] = (int)Tools::getValue('clearing');
            $responsiveConfiguration['FORMS'] = (int)Tools::getValue('forms');
            $responsiveConfiguration['JOYRIDE'] = (int)Tools::getValue('joyride');
            $responsiveConfiguration['MAGELLAN'] = (int)Tools::getValue('magellan');
            $responsiveConfiguration['MEDIAQUERYTOGGLE'] = (int)Tools::getValue('mediaquerytoggle');
            $responsiveConfiguration['NAVIGATION'] = (int)Tools::getValue('navigation');
            $responsiveConfiguration['ORBIT'] = (int)Tools::getValue('orbit');
            $responsiveConfiguration['REVEAL'] = (int)Tools::getValue('reveal');
            $responsiveConfiguration['TABS'] = (int)Tools::getValue('tabs');
            $responsiveConfiguration['TOOLTIPS'] = (int)Tools::getValue('tooltips');
            $responsiveConfiguration['TOPBAR'] = (int)Tools::getValue('topbar');
            $responsiveConfiguration['PLACEHOLDER'] = (int)Tools::getValue('placeholder');
            $responsiveConfiguration['MODERNIZR'] = 1;

            $responsiveConfiguration['IE6'] = (int)Tools::getValue('ie6');

            if (Configuration::updateValue('RESPONSIVE_EXTENSION', serialize($responsiveConfiguration))) {
                $this->_html .= '
                <div class="conf confirm">
                    '.$this->l('The configuration has been updated !').'
                </div>';
            }
            else
            {
                $this->_html .= '
                <div class="conf error">
                    <img src="../img/admin/disabled.gif" alt="" title="" />
                    '.$this->l('An error has occured during the save of the configuration').'
                </div>';
            }
        }

        $this->_displayForm();

        return $this->_html;
    }

    private function _displayForm()
    {
        $responsiveConfiguration = unserialize(Configuration::get('RESPONSIVE_EXTENSION'));

        $this->_html .= '
            <form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" method="post" enctype="multipart/form-data">
                <fieldset>
                    <legend><img src="../img/admin/information.png" class="middle"> '.$this->l('Responsive extension configuration').'</legend>
                    <div class="margin-form" style="color: black;">
                        <p>'.$this->l('This theme has been build with the Foundation CSS framework from ZURB').' (<b><a href="http://foundation.zurb.com/old-docs/f3/" target="_blank">foundation.zurb.com/old-docs/f3/</a>)</b></p>
                        <p>'.$this->l('You can check the online documentation for more information about this amazing framework.').'</p>
                    </div>
                    <label for="ie6">'.$this->l('IE6 warning message').' :</label>
                    <div class="margin-form">
                        <input type="radio" name="ie6" id="ie6_on" value="1" '.($responsiveConfiguration['IE6'] == 1 ? 'checked="checked"' : '').'>
                        <label class="t"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Yes').'" title="'.$this->l('Yes').'"></label>
                        <input type="radio" name="ie6" id="ie6_off" value="0" '.($responsiveConfiguration['IE6'] == 0 ? 'checked="checked"' : '').'>
                        <label class="t"> <img src="../img/admin/disabled.gif" alt="'.$this->l('No').'" title="'.$this->l('No').'"></label>
                        <p>'.$this->l('Displays a warning message politely informing the user to upgrade the browser to a newer version').' : <a target="_blank" href="http://code.google.com/p/ie6-upgrade-warning/">code.google.com/p/ie6-upgrade-warning</a></p>
                    </div>
                    <label for="accordion">'.$this->l('Accordion').' :</label>
                    <div class="margin-form">
                        <input type="radio" name="accordion" id="accordion_on" value="1" '.($responsiveConfiguration['ACCORDION'] == 1 ? 'checked="checked"' : '').'>
                        <label class="t"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Yes').'" title="'.$this->l('Yes').'"></label>
                        <input type="radio" name="accordion" id="accordion_off" value="0" '.($responsiveConfiguration['ACCORDION'] == 0 ? 'checked="checked"' : '').'>
                        <label class="t"> <img src="../img/admin/disabled.gif" alt="'.$this->l('No').'" title="'.$this->l('No').'"></label>
                        <p>'.$this->l('Add open/close functionality to accordions').' : <a target="_blank" href="http://foundation.zurb.com/old-docs/f3/elements.php">foundation.zurb.com/old-docs/f3/elements.php</a></p>
                    </div>
                    <label for="alerts">'.$this->l('Alerts').' :</label>
                    <div class="margin-form">
                        <input type="radio" name="alerts" id="alerts_on" value="1" '.($responsiveConfiguration['ALERTS'] == 1 ? 'checked="checked"' : '').'>
                        <label class="t"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Yes').'" title="'.$this->l('Yes').'"></label>
                        <input type="radio" name="alerts" id="alerts_off" value="0" '.($responsiveConfiguration['ALERTS'] == 0 ? 'checked="checked"' : '').'>
                        <label class="t"> <img src="../img/admin/disabled.gif" alt="'.$this->l('No').'" title="'.$this->l('No').'"></label>
                        <p>'.$this->l('Adds the ability to close alerts').' : <a target="_blank" href="http://foundation.zurb.com/old-docs/f3/elements.php">foundation.zurb.com/old-docs/f3/elements.php</a></p>
                    </div>
                    <label for="buttons">'.$this->l('Buttons').' :</label>
                    <div class="margin-form">
                        <input type="radio" name="buttons" id="buttons_on" value="1" '.($responsiveConfiguration['BUTTONS'] == 1 ? 'checked="checked"' : '').'>
                        <label class="t"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Yes').'" title="'.$this->l('Yes').'"></label>
                        <input type="radio" name="buttons" id="buttons_off" value="0" '.($responsiveConfiguration['BUTTONS'] == 0 ? 'checked="checked"' : '').'>
                        <label class="t"> <img src="../img/admin/disabled.gif" alt="'.$this->l('No').'" title="'.$this->l('No').'"></label>
                        <p>'.$this->l('Adds dropdown functionality for dropdown buttons and split buttons').' : <a target="_blank" href="http://foundation.zurb.com/old-docs/f3/elements.php">foundation.zurb.com/old-docs/f3/elements.php</a></p>
                    </div>
                    <label for="clearing">'.$this->l('Clearing').' :</label>
                    <div class="margin-form">
                        <input type="radio" name="clearing" id="clearing_on" value="1" '.($responsiveConfiguration['CLEARING'] == 1 ? 'checked="checked"' : '').'>
                        <label class="t"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Yes').'" title="'.$this->l('Yes').'"></label>
                        <input type="radio" name="clearing" id="clearing_off" value="0" '.($responsiveConfiguration['CLEARING'] == 0 ? 'checked="checked"' : '').'>
                        <label class="t"> <img src="../img/admin/disabled.gif" alt="'.$this->l('No').'" title="'.$this->l('No').'"></label>
                        <p>'.$this->l('A new image gallery plugin').' : <a target="_blank" href="http://foundation.zurb.com/old-docs/f3/clearing.php">foundation.zurb.com/old-docs/f3/clearing.php</a></p>
                    </div>
                    <label for="forms">'.$this->l('Forms').' :</label>
                    <div class="margin-form">
                        <input type="radio" name="forms" id="forms_on" value="1" '.($responsiveConfiguration['FORMS'] == 1 ? 'checked="checked"' : '').'>
                        <label class="t"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Yes').'" title="'.$this->l('Yes').'"></label>
                        <input type="radio" name="forms" id="forms_off" value="0" '.($responsiveConfiguration['FORMS'] == 0 ? 'checked="checked"' : '').'>
                        <label class="t"> <img src="../img/admin/disabled.gif" alt="'.$this->l('No').'" title="'.$this->l('No').'"></label>
                        <p>'.$this->l('Adds ability to create custom form elements').' : <a target="_blank" href="http://foundation.zurb.com/old-docs/f3/forms.php">foundation.zurb.com/old-docs/f3/forms.php</a></p>
                    </div>
                    <label for="joyride">'.$this->l('Joyride').' :</label>
                    <div class="margin-form">
                        <input type="radio" name="joyride" id="joyride_on" value="1" '.($responsiveConfiguration['JOYRIDE'] == 1 ? 'checked="checked"' : '').'>
                        <label class="t"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Yes').'" title="'.$this->l('Yes').'"></label>
                        <input type="radio" name="joyride" id="joyride_off" value="0" '.($responsiveConfiguration['JOYRIDE'] == 0 ? 'checked="checked"' : '').'>
                        <label class="t"> <img src="../img/admin/disabled.gif" alt="'.$this->l('No').'" title="'.$this->l('No').'"></label>
                        <p>'.$this->l('An awesome feature tour plugin').' : <a target="_blank" href="http://foundation.zurb.com/old-docs/f3/joyride.php">foundation.zurb.com/old-docs/f3/joyride.php</a></p>
                    </div>
                    <label for="magellan">'.$this->l('Magellan').' :</label>
                    <div class="margin-form">
                        <input type="radio" name="magellan" id="magellan_on" value="1" '.($responsiveConfiguration['MAGELLAN'] == 1 ? 'checked="checked"' : '').'>
                        <label class="t"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Yes').'" title="'.$this->l('Yes').'"></label>
                        <input type="radio" name="magellan" id="magellan_off" value="0" '.($responsiveConfiguration['MAGELLAN'] == 0 ? 'checked="checked"' : '').'>
                        <label class="t"> <img src="../img/admin/disabled.gif" alt="'.$this->l('No').'" title="'.$this->l('No').'"></label>
                        <p>'.$this->l('A sweet sticky nav plugin').' : <a target="_blank" href="http://foundation.zurb.com/old-docs/f3/magellan.php">foundation.zurb.com/old-docs/f3/magellan.php</a></p>
                    </div>
                    <label for="mediaquerytoggle">'.$this->l('Media query toggle').' :</label>
                    <div class="margin-form">
                        <input type="radio" name="mediaquerytoggle" id="mediaquerytoggle_on" value="1" '.($responsiveConfiguration['MEDIAQUERYTOGGLE'] == 1 ? 'checked="checked"' : '').'>
                        <label class="t"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Yes').'" title="'.$this->l('Yes').'"></label>
                        <input type="radio" name="mediaquerytoggle" id="mediaquerytoggle_off" value="0" '.($responsiveConfiguration['MEDIAQUERYTOGGLE'] == 0 ? 'checked="checked"' : '').'>
                        <label class="t"> <img src="../img/admin/disabled.gif" alt="'.$this->l('No').'" title="'.$this->l('No').'"></label>
                        <p>'.$this->l('Adds helpful media query viewer tool').'</p>
                    </div>
                    <label for="navigation">'.$this->l('Navigation').' :</label>
                    <div class="margin-form">
                        <input type="radio" name="navigation" id="navigation_on" value="1" '.($responsiveConfiguration['NAVIGATION'] == 1 ? 'checked="checked"' : '').'>
                        <label class="t"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Yes').'" title="'.$this->l('Yes').'"></label>
                        <input type="radio" name="navigation" id="navigation_off" value="0" '.($responsiveConfiguration['NAVIGATION'] == 0 ? 'checked="checked"' : '').'>
                        <label class="t"> <img src="../img/admin/disabled.gif" alt="'.$this->l('No').'" title="'.$this->l('No').'"></label>
                        <p>'.$this->l('Adds functionality to navigation elements').' : <a target="_blank" href="http://foundation.zurb.com/old-docs/f3/navigation.php">foundation.zurb.com/old-docs/f3/navigation.php</a></p>
                    </div>
                    <label for="orbit">'.$this->l('Orbit').' :</label>
                    <div class="margin-form">
                        <input type="radio" name="orbit" id="orbit_on" value="1" '.($responsiveConfiguration['ORBIT'] == 1 ? 'checked="checked"' : '').'>
                        <label class="t"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Yes').'" title="'.$this->l('Yes').'"></label>
                        <input type="radio" name="orbit" id="orbit_off" value="0" '.($responsiveConfiguration['ORBIT'] == 0 ? 'checked="checked"' : '').'>
                        <label class="t"> <img src="../img/admin/disabled.gif" alt="'.$this->l('No').'" title="'.$this->l('No').'"></label>
                        <p>'.$this->l('A custom image/content slider plugin').' : <a target="_blank" href="http://foundation.zurb.com/old-docs/f3/orbit.php">foundation.zurb.com/old-docs/f3/orbit.php</a></p>
                    </div>
                    <label for="reveal">'.$this->l('Reveal').' :</label>
                    <div class="margin-form">
                        <input type="radio" name="reveal" id="reveal_on" value="1" '.($responsiveConfiguration['REVEAL'] == 1 ? 'checked="checked"' : '').'>
                        <label class="t"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Yes').'" title="'.$this->l('Yes').'"></label>
                        <input type="radio" name="reveal" id="reveal_off" value="0" '.($responsiveConfiguration['REVEAL'] == 0 ? 'checked="checked"' : '').'>
                        <label class="t"> <img src="../img/admin/disabled.gif" alt="'.$this->l('No').'" title="'.$this->l('No').'"></label>
                        <p>'.$this->l('Our simple modal plugin').' : <a target="_blank" href="http://foundation.zurb.com/old-docs/f3/reveal.php">foundation.zurb.com/old-docs/f3/reveal.php</a></p>
                    </div>
                    <label for="tabs">'.$this->l('Tabs').' :</label>
                    <div class="margin-form">
                        <input type="radio" name="tabs" id="tabs_on" value="1" '.($responsiveConfiguration['TABS'] == 1 ? 'checked="checked"' : '').'>
                        <label class="t"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Yes').'" title="'.$this->l('Yes').'"></label>
                        <input type="radio" name="tabs" id="tabs_off" value="0" '.($responsiveConfiguration['TABS'] == 0 ? 'checked="checked"' : '').'>
                        <label class="t"> <img src="../img/admin/disabled.gif" alt="'.$this->l('No').'" title="'.$this->l('No').'"></label>
                        <p>'.$this->l('Adds toggle capability to tabs').' : <a target="_blank" href="http://foundation.zurb.com/old-docs/f3/tabs.php">foundation.zurb.com/old-docs/f3/tabs.php</a></p>
                    </div>
                    <label for="tooltips">'.$this->l('Tool tips').' :</label>
                    <div class="margin-form">
                        <input type="radio" name="tooltips" id="tooltips_on" value="1" '.($responsiveConfiguration['TOOLTIPS'] == 1 ? 'checked="checked"' : '').'>
                        <label class="t"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Yes').'" title="'.$this->l('Yes').'"></label>
                        <input type="radio" name="tooltips" id="tooltips_off" value="0" '.($responsiveConfiguration['TOOLTIPS'] == 0 ? 'checked="checked"' : '').'>
                        <label class="t"> <img src="../img/admin/disabled.gif" alt="'.$this->l('No').'" title="'.$this->l('No').'"></label>
                        <p>'.$this->l('Adds tooltips functionality').' : <a target="_blank" href="http://foundation.zurb.com/old-docs/f3/elements.php">foundation.zurb.com/old-docs/f3/elements.php</a></p>
                    </div>
                    <label for="topbar">'.$this->l('Top bar').' :</label>
                    <div class="margin-form">
                        <input type="radio" name="topbar" id="topbar_on" value="1" '.($responsiveConfiguration['TOPBAR'] == 1 ? 'checked="checked"' : '').'>
                        <label class="t"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Yes').'" title="'.$this->l('Yes').'"></label>
                        <input type="radio" name="topbar" id="topbar_off" value="0" '.($responsiveConfiguration['TOPBAR'] == 0 ? 'checked="checked"' : '').'>
                        <label class="t"> <img src="../img/admin/disabled.gif" alt="'.$this->l('No').'" title="'.$this->l('No').'"></label>
                        <p>'.$this->l('Adds functionality for the top bar').' : <a target="_blank" href="http://foundation.zurb.com/old-docs/f3/navigation.php">foundation.zurb.com/old-docs/f3/navigation.php</a></p>
                    </div>
                    <label for="placeholder">'.$this->l('Placeholder').' :</label>
                    <div class="margin-form">
                        <input type="radio" name="placeholder" id="placeholder_on" value="1" '.($responsiveConfiguration['PLACEHOLDER'] == 1 ? 'checked="checked"' : '').'>
                        <label class="t"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Yes').'" title="'.$this->l('Yes').'"></label>
                        <input type="radio" name="placeholder" id="placeholder_off" value="0" '.($responsiveConfiguration['PLACEHOLDER'] == 0 ? 'checked="checked"' : '').'>
                        <label class="t"> <img src="../img/admin/disabled.gif" alt="'.$this->l('No').'" title="'.$this->l('No').'"></label>
                        <p>'.$this->l('Adds placeholder functions to forms').'</p>
                    </div>
                    <div class="margin-form">
                        <input type="submit" value="'.$this->l('Save').'" name="submitSaveConfiguration" class="button">
                    </div>
                </fieldset>
            </form>
        ';
    }

    public function hookHeader()
    {
        $responsiveConfiguration = unserialize(Configuration::get('RESPONSIVE_EXTENSION'));

        if($responsiveConfiguration['ACCORDION'] == 1)
            $this->context->controller->addJs(($this->_path).'javascripts/jquery.foundation.accordion.js');
        if($responsiveConfiguration['ALERTS'] == 1)
            $this->context->controller->addJs(($this->_path).'javascripts/jquery.foundation.alerts.js');
        if($responsiveConfiguration['BUTTONS'] == 1)
            $this->context->controller->addJs(($this->_path).'javascripts/jquery.foundation.buttons.js');
        if($responsiveConfiguration['CLEARING'] == 1)
            $this->context->controller->addJs(($this->_path).'javascripts/jquery.foundation.clearing.js');
        if($responsiveConfiguration['FORMS'] == 1)
            $this->context->controller->addJs(($this->_path).'javascripts/jquery.foundation.forms.js');
        if($responsiveConfiguration['JOYRIDE'] == 1)
            $this->context->controller->addJs(($this->_path).'javascripts/jquery.foundation.joyride.js');
        if($responsiveConfiguration['MAGELLAN'] == 1)
            $this->context->controller->addJs(($this->_path).'javascripts/jquery.foundation.magellan.js');
        if($responsiveConfiguration['MEDIAQUERYTOGGLE'] == 1)
            $this->context->controller->addJs(($this->_path).'javascripts/jquery.foundation.mediaQueryToggle.js');
        if($responsiveConfiguration['NAVIGATION'] == 1)
            $this->context->controller->addJs(($this->_path).'javascripts/jquery.foundation.navigation.js');
        if($responsiveConfiguration['ORBIT'] == 1)
            $this->context->controller->addJs(($this->_path).'javascripts/jquery.foundation.orbit.js');
        if($responsiveConfiguration['REVEAL'] == 1)
            $this->context->controller->addJs(($this->_path).'javascripts/jquery.foundation.reveal.js');
        if($responsiveConfiguration['TABS'] == 1)
            $this->context->controller->addJs(($this->_path).'javascripts/jquery.foundation.tabs.js');
        if($responsiveConfiguration['TOOLTIPS'] == 1)
            $this->context->controller->addJs(($this->_path).'javascripts/jquery.foundation.tooltips.js');
        if($responsiveConfiguration['TOPBAR'] == 1)
            $this->context->controller->addJs(($this->_path).'javascripts/jquery.foundation.topbar.js');
        if($responsiveConfiguration['PLACEHOLDER'] == 1)
            $this->context->controller->addJs(($this->_path).'javascripts/jquery.placeholder.js');
        if($responsiveConfiguration['MODERNIZR'] == 1)
            $this->context->controller->addJs(($this->_path).'javascripts/modernizr.foundation.js');

        $this->context->controller->addJs(($this->_path).'javascripts/app.js');

        if($responsiveConfiguration['IE6'] == 1){
            return '
                <!--[if lte IE 6]>
                    <script type="text/javascript" src="'.($this->_path).'javascripts/ie6/warning.js"></script>
                    <script>window.onload=function(){e("'.($this->_path).'javascripts/ie6/")}</script>
                <![endif]-->
            ';
        }
    }
}
?>