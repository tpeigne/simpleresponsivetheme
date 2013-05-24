<?php

class ResponsiveSliderClass extends ObjectModel
{
    public $position;
    public $title;
    public $description;
    public $url;
    public $urlimage;
    public $isonline;
    public $id_shop;

    public static $definition = array(
        'table' => 'responsiveslider',
        'primary' => 'id_responsiveslider',
        'multilang' => true,
        'fields' => array(
            'title'       => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 255),
            'description' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 255),
            'url'         => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 128))
    );

    /**
      * Check then return multilingual fields for database interaction
      *
      * @return array Multilingual fields
      */
    public function getTranslationsFieldsChild()
    {
        parent::validateFieldsLang();

        $fieldsArray = array('title', 'description', 'url', 'urlimage');
        $fields = array();
        $languages = Language::getLanguages(false);
        $defaultLanguage = (int)(Configuration::get('PS_LANG_DEFAULT'));
        foreach ($languages as $language)
        {
            $fields[$language['id_lang']]['id_lang'] = (int)($language['id_lang']);
            $fields[$language['id_lang']][$this->identifier] = (int)($this->id);
            foreach ($fieldsArray as $field)
            {
                if (!Validate::isTableOrIdentifier($field))
                    die(Tools::displayError());
                if (isset($this->{$field}[$language['id_lang']]) AND !empty($this->{$field}[$language['id_lang']]))
                    $fields[$language['id_lang']][$field] = pSQL($this->{$field}[$language['id_lang']], true);
                elseif (in_array($field, $this->fieldsRequiredLang))
                    $fields[$language['id_lang']][$field] = pSQL($this->{$field}[$defaultLanguage], true);
                else
                    $fields[$language['id_lang']][$field] = '';
            }
        }
        return $fields;
    }

    public function getFields()
    {
        parent::validateFields();
        $fields['id_responsiveslider'] = (int)($this->id);
        $fields['id_shop'] = (int)($this->id_shop);
        $fields['position'] = (int)($this->position);
        $fields['isonline'] = (int)($this->isonline);

        return $fields;
    }

    public function copyFromPost()
    {
        /* Classical fields */
        foreach ($_POST AS $key => $value)
            if (key_exists($key, $this) AND $key != 'id_'.$this->table)
                $this->{$key} = $value;

        /* Multilingual fields */
        if (sizeof(ResponsiveSliderClass::$definition['fields']))
        {
            $languages = Language::getLanguages(false);
            foreach ($languages AS $language)
                foreach (ResponsiveSliderClass::$definition['fields'] AS $field => $validation)
                    if (isset($_POST[$field.'_'.(int)($language['id_lang'])]))
                        $this->{$field}[(int)($language['id_lang'])] = $_POST[$field.'_'.(int)($language['id_lang'])];
        }
    }

    /**
     * Get all slides
     *
     * @return array of ResponsiveSliderClass
     */
    public static function findAll()
    {
        $result = Db::getInstance()->ExecuteS('
        SELECT r.*
        FROM '._DB_PREFIX_.'responsiveslider r
        WHERE id_shop = \''.Context::getContext()->shop->id.'\'
        ORDER by position ASC');

        foreach($result as $slide => $value)
        {
            $result[$slide] = new ResponsiveSliderClass($value['id_responsiveslider']);
        }

        return $result;
    }

    /**
     * Get all slides online
     *
     * @param int $isOnline
     * @return array of ResponsiveSliderClass
     */
    public static function findAllByOnline($isOnline = 1)
    {
        $result = Db::getInstance()->ExecuteS('
        SELECT r.*
        FROM '._DB_PREFIX_.'responsiveslider r
        WHERE r.isonline = '.(int)$isOnline.' AND id_shop = \''.Context::getContext()->shop->id.'\'
        ORDER by position ASC');

        foreach($result as $slide => $value)
        {
            $result[$slide] = new ResponsiveSliderClass($value['id_responsiveslider'], Context::getContext()->cookie->id_lang);
        }

        return $result;
    }

    /**
     * Delete one slide
     *
     * @param $idSlide
     * @param $dirCaller
     * @return bool
     */
    public static function deleteSlide($idSlide, $dirCaller)
    {
        $slider = new ResponsiveSliderClass($idSlide);

        //delete all images of a slide
        foreach ($slider->urlimage AS $image){
            //check if the field is not empty
            if ($image <> '') {
                unlink($dirCaller.'/images/'.$image);
            }
        }

        if(!Db::getInstance()->delete(_DB_PREFIX_.'responsiveslider', '`id_responsiveslider` = '.(int)$idSlide.''))
            return false;

        if(!Db::getInstance()->delete(_DB_PREFIX_.'responsiveslider_lang', '`id_responsiveslider` = '.(int)$idSlide.''))
            return false;

        return true;
    }

    /**
     * Upload one image on the server
     *
     * @param $file
     * @param $modulePath
     * @param $idLangue
     * @return bool|string
     */
    private function uploadOneImage($file, $modulePath, $idLangue)
    {
        //check image error
        if ($file["error"] > 0) {
            return false;
        } else {
            //check image type
            if ($file['type'] == 'image/png'
                || $file['type'] == 'image/jpg'
                || $file['type'] == 'image/gif'
                || $file['type'] == 'image/jpeg'
                || $file['type'] == 'image/pjpeg') {
                if (file_exists($modulePath.'images/'.$file["name"])) {
                    return false;
                } else {
                    $rep = dirname(__DIR__).'/images/';
                    $image = md5(date('YmdHis')).'-'.$idLangue.'.jpg';

                    move_uploaded_file($file["tmp_name"], $rep.$image);

                    return $image;
                }
            } else {
                return false;
            }
        }
    }

    /**
     * Upload images on the server
     *
     * @param FILE $file images of a slide for each language
     * @param $modulePath
     */
    public function uploadImages($file, $modulePath)
    {
        /* Multilingual fields */
        $field = 'urlimage';
        $languages = Language::getLanguages(false);
        foreach ($languages AS $language){
            if (isset($file[$field.'_'.(int)($language['id_lang'])])){
                $urlImage = $this->uploadOneImage($file[$field.'_'.(int)($language['id_lang'])], $modulePath, (int)($language['id_lang']));

                if ($urlImage <> false) {
                    $this->{$field}[(int)($language['id_lang'])] = $urlImage;
                }
            }
        }
    }

    /**
     * Update position for each slide
     *
     * @param array $positions
     * @return bool
     */
    public function updatePosition($positions)
    {
        $i = 1;

        foreach($positions as $idSlide)
        {
            if ($idSlide <> '') {
                if (!Db::getInstance()->Execute('
                    UPDATE `'._DB_PREFIX_.'responsiveslider`
                    SET `position` = '.$i.'
                    WHERE `id_responsiveslider` = '.$idSlide.''))
                    return false;
                $i++;
            }

        }

        return true;
    }

    /**
     * Get the max position from the slider
     *
     * @return int
     */
    public static function getMaxPosition()
    {
        $return = 0;
        $result = Db::getInstance()->getRow('
        SELECT MAX(r.position) as position
        FROM '._DB_PREFIX_.'responsiveslider r
        WHERE id_shop = \''.Context::getContext()->shop->id.'\'');

        if (!$result['position']) {
            $return = 1;
        } else {
            $return = $result['position'] + 1;
        }

        return $return;
    }
}