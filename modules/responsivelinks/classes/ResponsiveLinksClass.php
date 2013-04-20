<?php

/**
 * Class ResponsiveLinksClass
 */
class ResponsiveLinksClass extends ObjectModel
{
    public $position;
    public $title;
    public $url;
    public $id_category;
    public $id_cms;
    public $id_product;
    public $id_parent;
    public $id_child;
    public $id_shop;

    public static $definition = array(
        'table' => 'responsivelinks',
        'primary' => 'id_responsivelinks',
        'fields' => array(
            'id_shop' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'title' => array('size' => 255),
            'url' => array('size' => 255))
    );

    /**
      * Check then return multilingual fields for database interaction
      *
      * @return array Multilingual fields
      */
    public function getTranslationsFieldsChild()
    {
        parent::validateFieldsLang();

        $fieldsArray = array('title', 'url');
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
        $fields['id_responsivelinks'] = (int)($this->id);
        $fields['position'] = (int)($this->position);
        $fields['id_category'] = (int)($this->id_category);
        $fields['id_cms'] = (int)($this->id_cms);
        $fields['id_product'] = (int)($this->id_product);
        $fields['id_parent'] = (int)($this->id_parent);
        $fields['id_child'] = (int)($this->id_child);
        $fields['id_shop'] = (int)($this->id_shop);

        return $fields;
    }

    public function copyFromPost()
    {
        /* Classical fields */
        foreach ($_POST AS $key => $value)
            if (key_exists($key, $this) AND $key != 'id_'.$this->table)
                $this->{$key} = $value;

        /* Multilingual fields */
        if (sizeof(ResponsiveLinksClass::$definition['fields']))
        {
            $languages = Language::getLanguages(false);
            foreach ($languages AS $language)
                foreach (ResponsiveLinksClass::$definition['fields'] AS $field => $validation)
                    if (isset($_POST[$field.'_'.(int)($language['id_lang'])]))
                        $this->{$field}[(int)($language['id_lang'])] = $_POST[$field.'_'.(int)($language['id_lang'])];
        }
    }

    public static function findAll($id_lang, $has_parent = false)
    {
        $result = Db::getInstance()->ExecuteS('
        SELECT r.*
        FROM '._DB_PREFIX_.'responsivelinks r
        WHERE id_shop = \''.Context::getContext()->shop->id.'\'
        '.($has_parent == true ? ' AND id_parent = 0' : '').'
        ORDER by position ASC');

        foreach($result as $link => $value)
        {
            $result[$link] = new ResponsiveLinksClass($value['id_responsivelinks'], $id_lang);
        }

        return $result;
    }

    public static function findSub($id, $id_lang)
    {
        $result = Db::getInstance()->ExecuteS('
        SELECT r.*
        FROM '._DB_PREFIX_.'responsivelinks r
        WHERE id_parent = '.(int)$id.' AND id_shop = \''.Context::getContext()->shop->id.'\'
        ORDER by position ASC');

        foreach($result as $link => $value)
        {
            $result[$link] = new ResponsiveLinksClass($value['id_responsivelinks'], $id_lang);
        }

        return $result;
    }

    public function deleteSubLinks()
    {
        global $cookie;

        //get all sub links for deletion
        foreach($this->findSub($this->id, $cookie->id_lang) as $linkSub){
            $linkSub->deleteSubLinks();

            if(!$linkSub->delete())
                return false;
        }

        return true;
    }

    public static function getMaxPosition(){
        $return = 0;
        $result = Db::getInstance()->getRow('
        SELECT MAX(r.position) as position
        FROM '._DB_PREFIX_.'responsivelinks r
        WHERE id_shop = \''.Context::getContext()->shop->id.'\'');

        if(!$result['position']){
            $return = 1;
        }else{
            $return = $result['position'] + 1;
        }

        return $return;
    }

    public function updatePosition($positions){
        $i = 1;

        foreach($positions as $idLink){
            if($idLink <> ''){
                if(!Db::getInstance()->Execute('
                    UPDATE `'._DB_PREFIX_.'responsivelinks`
                    SET `position` = '.$i.'
                    WHERE `id_responsivelinks` = '.$idLink.''))
                    return false;
                $i++;
            }

        }

        return true;
    }
}