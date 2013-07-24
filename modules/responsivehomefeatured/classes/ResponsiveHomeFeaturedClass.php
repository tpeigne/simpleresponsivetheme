<?php

/**
 * Class ResponsiveHomeFeaturedClass
 */
class ResponsiveHomeFeaturedClass extends ObjectModel
{
    public $id_category;
    public $position;
    public $id_shop;

    public static $definition = array(
        'table' => 'responsivehomefeatured',
        'primary' => 'id_responsivehomefeatured',
        'fields' => array()
    );

    public function getFields()
    {
        parent::validateFields();
        $fields['id_responsivehomefeatured'] = (int)($this->id);
        $fields['id_shop'] = (int)($this->id_shop);
        $fields['position'] = (int)($this->position);
        $fields['id_category'] = (int)($this->id_category);

        return $fields;
    }

    public function copyFromPost()
    {
        /* Classical fields */
        foreach ($_POST AS $key => $value)
        {
            if (key_exists($key, $this) AND $key != 'id_'.$this->table)
                $this->{$key} = $value;
        }

        /* Multilingual fields */
        if (sizeof(ResponsiveHomeFeaturedClass::$definition['fields'])) {
            $languages = Language::getLanguages(false);
            foreach ($languages AS $language)
                foreach (ResponsiveHomeFeaturedClass::$definition['fields'] AS $field => $validation)
                    if (isset($_POST[$field.'_'.(int)($language['id_lang'])]))
                        $this->{$field}[(int)($language['id_lang'])] = $_POST[$field.'_'.(int)($language['id_lang'])];
        }
    }


    /**
     * Add a product for a homefeatured category
     *
     * @param int $idProduct product id
     * @return bool
     */
    public function addProduct($idProduct)
    {
        $result = Db::getInstance()->autoExecute(
            _DB_PREFIX_.'responsivehomefeaturedproducts',
            array('id_responsivehomefeatured' => (int)$this->id, 'id_category' => (int)$this->id_category, 'id_product' => (int)$idProduct),
            'INSERT'
        );

        return $result;
    }

    /**
     * Delete a product of a homefeatured category
     *
     * @param int $idProduct
     * @return bool
     */
    public static function deleteProduct($idProduct)
    {
        return Db::getInstance()->Execute('
        DELETE FROM '._DB_PREFIX_.'responsivehomefeaturedproducts
        WHERE id_product = '.(int)$idProduct.'');
    }

    /**
     * Delete a homefeatured category
     *
     * @param $idHomeFeatured
     * @return bool
     */
    public static function deleteHomeFeaturedProduct($idHomeFeatured)
    {
        return Db::getInstance()->Execute('
        DELETE FROM '._DB_PREFIX_.'responsivehomefeaturedproducts
        WHERE id_responsivehomefeatured = '.(int)$idHomeFeatured.'');
    }


    /**
     * Get products of a homefeatured category
     *
     * @return array of Product
     */
    public function getProducts(){
        $return = array();

        $result = Db::getInstance()->ExecuteS('
        SELECT r.*
        FROM '._DB_PREFIX_.'responsivehomefeaturedproducts r
        WHERE id_category = '.(int)$this->id_category.'');

        foreach($result as $responsiveHomeFeatured)
        {
            $product = new Product($responsiveHomeFeatured['id_product'], false, Context::getContext()->cookie->id_lang);

            if ($product->id) {
                $return[] = $product;
            }
        }

        return $return;
    }

    /**
     * Get homefeatured category id
     *
     * @param int $idCategory a PrestaShop Category id
     * @return int
     */
    public static function getResponsiveHomeFeaturedId($idCategory){
        $result = Db::getInstance()->getRow('
        SELECT r.id_responsivehomefeatured
        FROM '._DB_PREFIX_.'responsivehomefeatured r
        WHERE id_category = '.(int)$idCategory.' AND id_shop = \''.Context::getContext()->shop->id.'\'');

        return $result['id_responsivehomefeatured'];
    }

    /**
     * TODO : review function description
     * Check if a PrestaShop Category exist
     *
     * @param int $idCategory PrestaShop Category id
     * @return bool
     */
    public static function existCategory($idCategory){
        $result = Db::getInstance()->getRow('
        SELECT r.id_category
        FROM '._DB_PREFIX_.'responsivehomefeatured r
        WHERE id_category = '.(int)$idCategory.' AND id_shop = \''.Context::getContext()->shop->id.'\'');

        return isset($result['id_category']);
    }

    /**
     * Get all homefeatured category
     *
     * @return array
     */
    public static function findAll(){
        $result = Db::getInstance()->ExecuteS('
        SELECT r.*
        FROM '._DB_PREFIX_.'responsivehomefeatured r
        WHERE id_shop = \''.Context::getContext()->shop->id.'\'
        ORDER by position ASC');

        foreach($result as $homeFeatured => $value)
        {
            $result[$homeFeatured] = new ResponsiveHomeFeaturedClass($value['id_responsivehomefeatured']);
        }

        return $result;
    }

    /**
     * Get the highest homefeatured category position
     *
     * @return int
     */
    public static function getMaxPosition(){
        $return = 0;
        $result = Db::getInstance()->getRow('
        SELECT MAX(r.position) as position
        FROM '._DB_PREFIX_.'responsivehomefeatured r
        WHERE id_shop = \''.Context::getContext()->shop->id.'\'');

        if(!$result['position']){
            $return = 1;
        }else{
            $return = $result['position'] + 1;
        }

        return $return;
    }

    /**
     * Update positions for each homefeatured category
     *
     * @param array $positions
     * @return bool
     */
    public function updatePosition($positions){
        $i = 1;

        foreach($positions as $idHomeFeatured){
            if($idHomeFeatured <> ''){
                if(!Db::getInstance()->Execute('
                    UPDATE `'._DB_PREFIX_.'responsivehomefeatured`
                    SET `position` = '.$i.'
                    WHERE `id_responsivehomefeatured` = '.$idHomeFeatured.''))
                    return false;
                $i++;
            }
        }

        return true;
    }
}