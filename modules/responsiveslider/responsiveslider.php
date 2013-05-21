<?php

/**
  * ResponsiveSlider module for Prestashop, responsiveslider.php
  *
  * Created by Thomas Peigné (thomas.peigne@gmail.com)
  */

if (!defined('_PS_VERSION_'))
    exit;

class ResponsiveSlider extends Module
{
    public function __construct()
    {
        $this->name = 'responsiveslider';
        $this->tab = 'front_office_features';
        $this->version = '2.2';
        $this->author = 'Thomas Peigné';
        $this->need_instance = 0;

        parent::__construct();

        $this->displayName = $this->l('Responsive Slider');
        $this->description = $this->l('Add an responsive slider on your home page');

        include_once($this->local_path.'/classes/ResponsiveSliderClass.php');
    }

    public function install()
    {
        // Install Module
        if (!parent::install() OR !$this->registerHook('home') OR !$this->registerHook('header'))
            return false;

        // Install Module Table
        if (!Db::getInstance()->Execute('
        CREATE TABLE `'._DB_PREFIX_.'responsiveslider` (
        `id_responsiveslider` int(10) unsigned NOT NULL AUTO_INCREMENT,
        `id_shop` int(10) unsigned NOT NULL,
        `position` int(10) NOT NULL,
        `isonline` tinyint(1) NOT NULL,
        PRIMARY KEY (`id_responsiveslider`))
        ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8'))
            return false;

        // Install Module Table
        if (!Db::getInstance()->Execute('
        CREATE TABLE `'._DB_PREFIX_.'responsiveslider_lang` (
        `id_responsiveslider` int(10) unsigned NOT NULL AUTO_INCREMENT,
        `id_lang` int(10) unsigned NOT NULL,
        `title` varchar(255) NOT NULL,
        `description` text NOT NULL,
        `url` varchar(255) NOT NULL,
        `urlimage` varchar(255) NOT NULL,
        PRIMARY KEY (`id_responsiveslider`, `id_lang`))
        ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8'))
            return false;

        //responsive slider configuration array
        $responsiveSliderConfiguration = array();

        //basic configuration
        $responsiveSliderConfiguration['RESPONSIVESLIDER_ANIMATION'] = 'horizontal-slide';
        $responsiveSliderConfiguration['RESPONSIVESLIDER_SLIDESHOWSPEED'] = 5000;
        $responsiveSliderConfiguration['RESPONSIVESLIDER_ANIMATIONSPEED'] = 1000;
        $responsiveSliderConfiguration['RESPONSIVESLIDER_CONTROLNAV'] = 1;

        //configuration update
        Configuration::updateValue('RESPONSIVESLIDER_CONFIGURATION', serialize($responsiveSliderConfiguration));

        $this->installDemoLinks();

        return true;
    }

    public function uninstall()
    {
        // Uninstall Module
        if (!parent::uninstall())
            return false;

        if (!Db::getInstance()->Execute('DROP TABLE `'._DB_PREFIX_.'responsiveslider`'))
            return false;

        if (!Db::getInstance()->Execute('DROP TABLE `'._DB_PREFIX_.'responsiveslider_lang`'))
            return false;

        Configuration::deleteByName('RESPONSIVESLIDER_CONFIGURATION');

        return true;
    }

    public function disable($forceAll = false)
    {
        parent::disable($forceAll);
    }

    public function enable($forceAll = false)
    {
        parent::enable($forceAll);
    }

    public function getContent()
    {
        $this->_html = '';

        /* add css file */
        $this->_addCSS();
        /* add js file */
        $this->_addJS();

        /* SLIDER EDITION */
        if (Tools::isSubmit('submitEditSlide')) {
            //check if we are deleting a image
            if (Tools::getIsset('actionSlide') && Tools::getIsset('actionSlide') == 'deleteImage') {
                $slider = new ResponsiveSliderClass(Tools::getValue('idSlide'));
                $slider->urlimage = '';

                if ($slider->save()) {
                    $response = '
                    <div class="conf confirm">
                        '.$this->l('The image slide has been updated.').'
                    </div>';

                    $this->_html .= $response;
                } else {
                    $response = '
                    <div class="conf error">
                        <img src="../img/admin/disabled.gif" alt="" title="" />
                        '.$this->l('An error has occured during the update of the slide.').'
                    </div>';

                    $this->_html .= $response;
                }

            } else {
                $slider = new ResponsiveSliderClass(Tools::getValue('idSlide'));
                $slider->copyFromPost();
                $slider->uploadImages($_FILES, $this->_path);

                if ($slider->save()) {
                    $this->_html .= '
                    <div class="conf confirm">
                        '.$this->l('The slide').' '.$slider->title[$this->context->cookie->id_lang].' '.$this->l('has been updated.').'
                    </div>';
                } else {
                    $this->_html .= '
                    <div class="conf error">
                        <img src="../img/admin/disabled.gif" alt="" title="" />
                        '.$this->l('An error has occured during the update of the slide.').'
                    </div>';
                }
            }
        }
        /* END SLIDER EDITION */

        /* SLIDER ADDITION */
        if (Tools::isSubmit('submitAddSlide')) {
            //get data from post method
            $slider = new ResponsiveSliderClass();
            $slider->copyFromPost();
            $slider->uploadImages($_FILES, $this->_path);
            $slider->position = ResponsiveSliderClass::getMaxPosition();
            $slider->id_shop = $this->context->shop->id;

            if ($slider->save()) {
                $this->_html .= '
                <div class="conf confirm">
                    '.$this->l('The slide').' '.$slider->title[$this->context->cookie->id_lang].' '.$this->l('has been added to your slider.').'
                </div>';
            } else {
                $this->_html .= '
                <div class="conf error">
                    <img src="../img/admin/disabled.gif" alt="" title="" />
                    '.$this->l('An error has occured during the addition of the slide.').'
                </div>';
            }
        }
        /* END SLIDER ADDITION */

        /* CONFIGURATION EDITION */
        if (Tools::isSubmit('submitConfiguration')) {
            //responsive slider configuration array
            $responsiveSliderConfiguration = array();
            //basic configuration
            $responsiveSliderConfiguration['RESPONSIVESLIDER_ANIMATION'] = Tools::getValue('animation');
            $responsiveSliderConfiguration['RESPONSIVESLIDER_SLIDESHOWSPEED'] = (int)Tools::getValue('slideshowSpeed');
            $responsiveSliderConfiguration['RESPONSIVESLIDER_ANIMATIONSPEED'] = (int)Tools::getValue('animationSpeed');
            $responsiveSliderConfiguration['RESPONSIVESLIDER_CONTROLNAV'] = (int)Tools::getValue('controlNav');

            //configuration update
            Configuration::updateValue('RESPONSIVESLIDER_CONFIGURATION', serialize($responsiveSliderConfiguration));

            $this->_html .= '
            <div class="conf confirm">
                '.$this->l('The slider configuration has been updated.').'
            </div>';
        }
        /* END CONFIGURATION EDITION */

        $this->_html .= '<h2 id="module-title">'.$this->displayName.'</h2>
                <div style="display:none;" id="ajax-response"></div>';

        $this->_displayForm();

        return $this->_html;
    }

    private function _displayForm()
    {
        //get responsvive slider configuration
        $responsiveSliderConfiguration = unserialize(Configuration::get('RESPONSIVESLIDER_CONFIGURATION'));

        $sliderEdition = null;
        //check if we are editting a slide
        if(Tools::getIsset('action') && Tools::getIsset('action') == 'editSlide')
            $sliderEdition = new ResponsiveSliderClass(Tools::getValue('idSlide'));

        /* Languages preliminaries */
        $defaultLanguage = (int)(Configuration::get('PS_LANG_DEFAULT'));
        $languages = Language::getLanguages(false);
        $divLangName = 'title¤description¤url¤urlimage';

        $this->_html .= '
        <script type="text/javascript">
            var urlAjaxModule = "'._PS_BASE_URL_.$this->_path.'ajax.php";
        </script>
        <script type="text/javascript" src="'._PS_JS_DIR_.'jquery/plugins/jquery.tablednd.js"></script>
        <script type="text/javascript">id_language = Number('.$defaultLanguage.');</script>
        <a id="add-image" href=""><img src="../img/admin/add.gif" border="0"> '.$this->l('Add a slide').'</a>
        <div class="clear">&nbsp;</div>';

        $this->_html .= '
        <form id="informations-image" '.(isset($sliderEdition) ? '' : 'style="display:none;"').' action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" method="POST" enctype="multipart/form-data">
            <fieldset style="margin-bottom:10px;">
                <legend><img src="../img/admin/information.png" class="middle"> '.$this->l('Add a new slide to your slider').'</legend>';

                //champ nom
                $this->_html .= '
                <label>'.$this->l('Title :').'</label>
                <div class="margin-form">';

                foreach ($languages as $language)
                {
                    $this->_html .= '
                    <div id="title_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';float: left;">
                        <input class="required" type="text" name="title_'.$language['id_lang'].'" id="title_'.$language['id_lang'].'" size="35" value="'.(isset($sliderEdition->title[$language['id_lang']]) ? $sliderEdition->title[$language['id_lang']] : '').'" />
                    </div>';
                }
                $this->_html .= $this->displayFlags($languages, $defaultLanguage, $divLangName, 'title', true);
                $this->_html .= '
                    <p class="clear">'.$this->l('Title of the slide').'</p>
                </div>';

                //champ description
                $this->_html .= '
                <label>'.$this->l('Description :').'</label>
                <div class="margin-form">';

                foreach ($languages as $language)
                {
                    $this->_html .= '
                    <div id="description_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';float: left;">
                        <textarea class="required" name="description_'.$language['id_lang'].'" id="description_'.$language['id_lang'].'" cols="45">'.(isset($sliderEdition->description[$language['id_lang']]) ? $sliderEdition->description[$language['id_lang']] : '').'</textarea>
                    </div>';
                }
                $this->_html .= $this->displayFlags($languages, $defaultLanguage, $divLangName, 'description', true);
                $this->_html .= '
                    <p class="clear">'.$this->l('Description of the slide').'</p>
                </div>';

                //champ url
                $this->_html .= '
                <label>'.$this->l('Url :').'</label>
                <div class="margin-form">';

                foreach ($languages as $language)
                {
                    $this->_html .= '
                    <div id="url_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';float: left;">
                        <input class="required" type="text" name="url_'.$language['id_lang'].'" id="url_'.$language['id_lang'].'" size="35" value="'.(isset($sliderEdition->url[$language['id_lang']]) ? $sliderEdition->url[$language['id_lang']] : '').'" />
                    </div>';
                }
                $this->_html .= $this->displayFlags($languages, $defaultLanguage, $divLangName, 'url', true);
                $this->_html .= '
                    <p class="clear">'.$this->l('Url of the slide (leave blank is no url)').'</p>
                </div>';

                //champ image
                $this->_html .= '
                <label for="urlimage">'.$this->l('Image :').'</label>
                <div class="margin-form">';

                $apercuSlide = '';

                foreach ($languages as $language)
                {
                    //check if we are editting a slide
                    if (isset($sliderEdition)) {
                        if($sliderEdition->urlimage[$language['id_lang']] <> '') {
                            $apercuSlide = '
                            <div id="image" style="margin-top: 10px;">
                                <a class="apercu-fancy" rel="fancybox-thumb" href="'.$this->_path.'images/'.$sliderEdition->urlimage[$language['id_lang']].'" title="'.$sliderEdition->title[$language['id_lang']].'">
                                    <img src="'.$this->_path.'/images/'.$sliderEdition->urlimage[$language['id_lang']].'" style="max-width:100%;"/>
                                </a>
                                <p align="center">'.$this->l('Filesize').' '.(filesize(dirname(__FILE__).'/images/'.$sliderEdition->urlimage[$language['id_lang']].'') / 1000).'kb</p>
                            </div>';
                        } else {
                            $apercuSlide = '';
                        }
                    }

                    $this->_html .= '
                    <div id="urlimage_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';float: left;">
                        <input class="required" type="file" name="urlimage_'.$language['id_lang'].'" id="urlimage_'.$language['id_lang'].'" size="35" value="'.(isset($sliderEdition->url[$language['id_lang']]) ? $sliderEdition->url[$language['id_lang']] : '').'" />
                        '.$apercuSlide.'
                    </div>';
                }
                $this->_html .= $this->displayFlags($languages, $defaultLanguage, $divLangName, 'urlimage', true);
                $this->_html .= '
                    <p class="clear">'.$this->l('Extensions allowed : .png, .jpg, .jpeg').'</p>
                </div>';

                //other fields
                $this->_html .= '
                <label for="isonline">'.$this->l('Online :').'</label>';
                if (isset($sliderEdition)) {
                    if ($sliderEdition->isonline == 1) {
                        $optionTrue = 'checked="checked"';
                        $optionFalse = '';
                    } else {
                        $optionTrue = '';
                        $optionFalse = 'checked="checked"';
                    }
                } else {
                    $optionTrue = '';
                    $optionFalse = 'checked="checked"';
                }

                $this->_html .= '
                <div class="margin-form">
                    <input type="radio" name="isonline" id="isonline_on" value="1" '.$optionTrue.'>
                    <label class="t"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Online').'" title="'.$this->l('Online').'"></label>
                    <input type="radio" name="isonline" id="isonline_off" value="0" '.$optionFalse.'>
                    <label class="t"> <img src="../img/admin/disabled.gif" alt="'.$this->l('Offline').'" title="'.$this->l('Offline').'"></label>
                </div>
                <div class="margin-form">';
                    if(isset($sliderEdition))
                        $this->_html .= '<input type="submit" value="'.$this->l('Save').'" name="submitEditSlide" class="button">
                            <input type="hidden" value="'.$sliderEdition->id.'" name="idSlide" class="button">';
                    else
                        $this->_html .= '<input type="submit" value="'.$this->l('Save').'" name="submitAddSlide" class="button">';

                $this->_html .= '
                </div>
            </fieldset>
        </form>';

        //form for slide edition
        if(isset($sliderEdition)) {
            $this->_html .= '
            <form id="deleteImageForm" style="display: none;" action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" method="POST">
                <input type="hidden" name="actionSlide" value="deleteImage">
                <input type="hidden" name="action" value="editSlide">
                <input type="hidden" name="idSlide" value="'.$sliderEdition->id.'">
                <input type="hidden" value="'.$this->l('Save').'" name="submitEditSlide">
            </form>';
        }

        $this->_html .= '
        <fieldset>
            <legend><img src="../img/admin/tab-preferences.gif" class="middle"> '.$this->l('Manage your slides').'</legend>
            <p>'.$this->l('Edit your slides with the edit button and save it.').'</p>
            <hr>
            <table id="slides" class="table tableDnD" cellpadding="0" cellspacing="0" style="width: 100%;">
                <thead>
                    <tr>
                        <th class="center">'.$this->l('Position').'</th>
                        <th>'.$this->l('Title').'</th>
                        <th>'.$this->l('Description').'</th>
                        <th class="center">'.$this->l('Preview').'</th>
                        <th class="center">'.$this->l('Online').'</th>
                        <th class="center">'.$this->l('Actions').'</th>
                    </tr>
                </thead>
                <tbody>';

                foreach(ResponsiveSliderClass::findAll() as $slider)
                {
                    $urlImage = urlencode($this->_path.'images/'.$slider->urlimage[$this->context->cookie->id_lang]);

                    $this->_html .= '
                    <tr id="'.$slider->id.'">
                        <td class="center position"></td>
                        <td>'.$slider->title[$this->context->cookie->id_lang].'</td>
                        <td>'.$slider->description[$this->context->cookie->id_lang].'</td>
                        <td class="center">
                            <a class="apercu-fancy" rel="fancybox-thumb" href="'.$this->_path.'images/'.$slider->urlimage[$this->context->cookie->id_lang].'" title="'.$slider->title[$this->context->cookie->id_lang].'">
                                <img src="'.$this->_path.'classes/timthumb.php?src='.$urlImage.'&h=50&w=50" alt="'.$slider->title[$this->context->cookie->id_lang].'" />
                            </a>
                        </td>
                        <td class="center">';
                        if ($slider->isonline == 1) {
                            $this->_html .= '
                            <a class="online-slide" href="" urlajax="'.$this->_path.'ajax.php" actionOnline="putOffline" id="'.$slider->id.'" title="'.$this->l('Put offline ?').'">
                                <img src="../img/admin/enabled.gif" alt="'.$this->l('Online').'"">
                            </a>
                            ';
                        } else {
                            $this->_html .= '
                            <a class="online-slide" href="" urlajax="'.$this->_path.'ajax.php" actionOnline="putOnline" id="'.$slider->id.'" title="'.$this->l('Put online ?').'">
                                <img src="../img/admin/disabled.gif" alt="'.$this->l('Offline').'" title="'.$this->l('Offline').'">
                            </a>
                            ';
                        }

                        $this->_html .= '
                        </td>
                        <td class="center">
                            <a class="editSlide" href="" title="'.$this->l('Edit').'">
                                <img src="../img/admin/edit.gif" alt="'.$this->l('Edit').'" alt="'.$this->l('Edit').'">
                            </a>
                            <form style="display: none;" action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" method="POST">
                                <input type="hidden" name="action" value="editSlide">
                                <input type="hidden" name="idSlide" value="'.$slider->id.'">
                            </form>
                            <a class="delete-image" href="#" urlajax="'.$this->_path.'ajax.php" id="'.$slider->id.'" title="'.$this->l('Delete the slide ?').'">
                                <img src="../img/admin/delete.gif" alt="'.$this->l('Delete').'" alt="'.$this->l('Delete').'">
                            </a>
                        </td>
                    </tr>';
                }

                $this->_html .= '
                </tbody>
            </table>
        </fieldset>

        <form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" method="POST">
            <fieldset class="space">
                <legend><img src="../img/admin/prefs.gif" class="middle"> '.$this->l('Manage your slider').'</legend>
                <h3 style="margin-left:210px;"><a href="" title="'.$this->l('Basic configuration').'">'.$this->l('Basic configuration').' >></a></h3>
                <div>
                    <label for="animation">'.$this->l('Animation :').'</label>
                    <div class="margin-form">
                        <select name="animation">
                            <option value="fade" '.($responsiveSliderConfiguration['RESPONSIVESLIDER_ANIMATION'] == 'fade' ? 'selected="selected"' : '').'>'.$this->l('Fade').'</option>
                            <option value="horizontal-slide" '.($responsiveSliderConfiguration['RESPONSIVESLIDER_ANIMATION'] == 'horizontal-slide' ? 'selected="selected"' : '').'>'.$this->l('Horizontal slide').'</option>
                            <option value="vertical-slide" '.($responsiveSliderConfiguration['RESPONSIVESLIDER_ANIMATION'] == 'vertical-slide' ? 'selected="selected"' : '').'>'.$this->l('Vertical slide').'</option>
                            <option value="horizontal-push" '.($responsiveSliderConfiguration['RESPONSIVESLIDER_ANIMATION'] == 'horizontal-push' ? 'selected="selected"' : '').'>'.$this->l('Horizontal push').'</option>
                        </select>
                        <p>'.$this->l('Select your animation type, "fade", "horizontal-slide", "vertical-slide" or "horizontal-push"').'</p>
                    </div>
                    <label for="slideshowSpeed">'.$this->l('Slideshow speed :').'</label>
                    <div class="margin-form">
                        <input type="text" name="slideshowSpeed" id="slideshowSpeed" value="'.(int)$responsiveSliderConfiguration['RESPONSIVESLIDER_SLIDESHOWSPEED'].'"/>
                        <p>'.$this->l('Set the speed of the slideshow cycling, in milliseconds').'</p>
                    </div>
                    <label for="animationSpeed">'.$this->l('Animation speed :').'</label>
                    <div class="margin-form">
                        <input type="text" name="animationSpeed" id="animationSpeed" value="'.(int)$responsiveSliderConfiguration['RESPONSIVESLIDER_ANIMATIONSPEED'].'"/>
                        <p>'.$this->l('Set the speed of animations, in milliseconds').'</p>
                    </div>
                    <label for="controlNav">'.$this->l('Control nav :').'</label>
                    <div class="margin-form">
                        <input type="radio" name="controlNav" id="controlnav_on" value="1" '.($responsiveSliderConfiguration['RESPONSIVESLIDER_CONTROLNAV'] == 1 ? 'checked="checked"' : '').'>
                        <label class="t"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Online').'" title="'.$this->l('Online').'"></label>
                        <input type="radio" name="controlNav" id="controlnav_off" value="0" '.($responsiveSliderConfiguration['RESPONSIVESLIDER_CONTROLNAV'] == 0 ? 'checked="checked"' : '').'>
                        <label class="t"> <img src="../img/admin/disabled.gif" alt="'.$this->l('Offline').'" title="'.$this->l('Offline').'"></label>
                        <p>'.$this->l('Create navigation for paging control of each slide.').'</p>
                    </div>
                </div>
                <div class="margin-form">
                    <input type="submit" value="'.$this->l('Save').'" name="submitConfiguration" class="button">
                </div>
            </fieldset>
        </form>
        ';
    }

    private function _addJS()
    {
        $this->_html .= '<script type="text/javascript" src="'.__PS_BASE_URI__.'js/jquery/plugins/fancybox/jquery.fancybox.js"></script>';
        $this->_html .= '<script type="text/javascript" src="'.$this->_path.'javascripts/responsiveslider.js"></script>';
    }

    private function _addCSS()
    {
        $this->_html .= '<link type="text/css" rel="stylesheet" href="'.__PS_BASE_URI__.'js/jquery/plugins/fancybox/jquery.fancybox.css" />';
    }

    public function hookHome($params)
    {
        $this->context->smarty->assign(array(
            'configuration' => unserialize(Configuration::get('RESPONSIVESLIDER_CONFIGURATION')),
            'sliders' => ResponsiveSliderClass::findAllByOnline()
        ));

        return $this->display(__FILE__, 'responsiveslider.tpl');
    }

    public function hookHeader()
    {
        $this->context->controller->addCSS(($this->_path).'responsiveslider.css', 'all');
    }


    /**
     * Install demo products
     *
     * @return bool
     */
    public function installDemoLinks()
    {
        $languages = Language::getLanguages(true);

        //first slide
        $firstSlide = new ResponsiveSliderClass();
        $firstSlide->position = 1;
        $firstSlide->isonline = 1;
        $firstSlide->id_shop  = Configuration::get('PS_SHOP_DEFAULT');
        foreach ($languages as $language)
        {
            $firstSlide->title[(int)($language['id_lang'])]       = 'iPod Nano';
            $firstSlide->description[(int)($language['id_lang'])] = 'iPod Nano';
            $firstSlide->url[(int)($language['id_lang'])]         = '';
            if (!copy($this->local_path.'/images/nano.png', $this->local_path.'/images/nano-'.(int)$language['id_lang'].'.png')) {
                //Error while coping the 2nd demo slide
                return false;
            }
            $firstSlide->urlimage[(int)($language['id_lang'])]    = 'nano-'.(int)$language['id_lang'].'.png';
        }

        if (!$firstSlide->save())
            return false;

        //second slide
        $secondSlide = new ResponsiveSliderClass();
        $secondSlide->position = 2;
        $secondSlide->isonline = 1;
        $secondSlide->id_shop  = Configuration::get('PS_SHOP_DEFAULT');
        foreach ($languages as $language)
        {
            $secondSlide->title[(int)($language['id_lang'])]       = 'iPod Touch';
            $secondSlide->description[(int)($language['id_lang'])] = 'iPod Touch';
            $secondSlide->url[(int)($language['id_lang'])]         = '';
            if (!copy($this->local_path.'/images/touch.png', $this->local_path.'/images/touch-'.(int)$language['id_lang'].'.png')) {
                //Error while coping the 2nd demo slide
                return false;
            }
            $secondSlide->urlimage[(int)($language['id_lang'])]    = 'touch-'.(int)$language['id_lang'].'.png';
        }

        if ($secondSlide->save())
            return false;
    }
}