<?php

/**
 * Class ResponsiveHomeFeaturedClass
 *
 * @author Thomas Peigné <thomas.peigne@gmail.com>
 */
class ResponsiveHomeFeaturedClass extends ObjectModel
{
    public $id_category;
    public $position;
    public $id_shop;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'responsivehomefeatured',
        'primary' => 'id_responsivehomefeatured',
        'fields' => array(
            'id_shop'     => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
            'id_category' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
            'position'    => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
            'date_add'    => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'date_upd'    => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat')
        )
    );

    public function copyFromPost()
    {
        /* Classical fields */
        foreach ($_POST as $key => $value) {
            if (array_key_exists($key, $this) && $key !=  self::$definition['primary'])
                $this->{$key} = $value;
        }

        /* Multilingual fields */
        if (sizeof(ResponsiveHomeFeaturedClass::$definition['fields'])) {
            $languages = Language::getLanguages(false);
            foreach ($languages as $language) {
                foreach (ResponsiveHomeFeaturedClass::$definition['fields'] as $field => $validation) {
                    if (isset($_POST[$field.'_'.(int)($language['id_lang'])])) {
                        $this->{$field}[(int)($language['id_lang'])] = $_POST[$field.'_'.(int)($language['id_lang'])];
                    }
                }
            }
        }
    }


    /**
     * Add a product for a featured category
     *
     * @param int $idProduct product id
     * @return bool
     */
    public function addProduct($idProduct)
    {
        $data = array(
            'id_responsivehomefeatured' => (int)$this->id,
            'id_category' => (int)$this->id_category,
            'id_product' => (int)$idProduct,
            'date_add' => date('Y-m-d H:i:s')
        );

        return Db::getInstance()->insert(
            'responsivehomefeaturedproducts',
            $data
        );
    }

    /**
     * Delete a product of a featured category
     *
     * @param int $idProduct
     * @return bool
     */
    public static function deleteProduct($idProduct)
    {
        return Db::getInstance()->delete(
            'responsivehomefeaturedproducts',
            'id_product = '.(int)$idProduct
        );
    }

    /**
     * Delete all products for a given featured category
     *
     * @param $idHomeFeatured
     * @return bool
     */
    public static function deleteHomeFeaturedProducts($idHomeFeatured)
    {
        return Db::getInstance()->delete(
            'responsivehomefeaturedproducts',
            'id_responsivehomefeatured = '.(int) $idHomeFeatured
        );
    }

    public static function deleteHomeFeaturedProduct($idHomeFeatured, $productId)
    {
        return Db::getInstance()->delete(
            'responsivehomefeaturedproducts',
            'id_responsivehomefeatured = '.(int) $idHomeFeatured.' AND id_product = '.(int) $productId
        );
    }


    /**
     * Get all products of a featured category depending of the current store
     *
     * @return array of Product
     */
    public function getProducts()
    {
        $return = array();

        $query = '
            SELECT
                rhfp.*
            FROM '._DB_PREFIX_.'responsivehomefeatured
                AS rhf
            INNER JOIN '._DB_PREFIX_.'responsivehomefeaturedproducts
                AS rhfp
                ON (rhf.id_responsivehomefeatured = rhfp.id_responsivehomefeatured)
            WHERE
                rhfp.id_category = '.(int) $this->id_category.'
                AND rhf.id_shop = '.(int) Context::getContext()->shop->id.'
        ';

        $result = Db::getInstance()->ExecuteS($query);

        foreach ($result as $responsiveHomeFeatured) {
            $product = new Product($responsiveHomeFeatured['id_product'], false, Context::getContext()->cookie->id_lang);

            if ($product->id) {
                $return[] = $product;
            }
        }

        return $return;
    }

    /**
     * Get featured category id
     *
     * @param int $idCategory a PrestaShop Category id
     * @return int
     */
    public static function getResponsiveHomeFeaturedId($idCategory)
    {
        $query = '
            SELECT
                rhf.id_responsivehomefeatured
            FROM '._DB_PREFIX_.'responsivehomefeatured
                AS rhf
            WHERE
                rhf.id_category = '.(int) $idCategory.'
                AND rhf.id_shop = '.(int) Context::getContext()->shop->id.'
        ';

        $result = Db::getInstance()->getRow($query);

        return $result['id_responsivehomefeatured'];
    }

    /**
     * Check if a PrestaShop category exist for the current store
     *
     * @param int $idCategory PrestaShop Category id
     * @return bool
     */
    public static function existCategory($idCategory)
    {
        $query = '
            SELECT
                r.id_category
            FROM '._DB_PREFIX_.'responsivehomefeatured
                AS r
            WHERE
                r.id_category = '.(int)$idCategory.'
                AND r.id_shop = '.(int)Context::getContext()->shop->id.'
        ';

        $result = Db::getInstance()->getRow($query);

        return isset($result['id_category']);
    }

    /**
     * Get all featured category
     *
     * @return array
     */
    public static function findAll()
    {
        $query = '
            SELECT r.id_responsivehomefeatured AS id
            FROM '._DB_PREFIX_.'responsivehomefeatured AS r
            WHERE r.id_shop = '.(int)Context::getContext()->shop->id.'
            ORDER by r.position ASC
        ';

        $result = Db::getInstance()->ExecuteS($query);

        foreach ($result as $homeFeatured => $value) {
            $result[$homeFeatured] = new ResponsiveHomeFeaturedClass($value['id']);
        }

        return $result;
    }

    /**
     * Get the highest featured category position
     *
     * @return int
     */
    public static function getMaxPosition()
    {
        $query = '
            SELECT
                MAX(r.position) as position
            FROM '._DB_PREFIX_.'responsivehomefeatured
                AS r
            WHERE
                r.id_shop = '.(int)Context::getContext()->shop->id.'
        ';

        $result = Db::getInstance()->getRow($query);

        if (!$result['position']){
            $return = 1;
        } else {
            $return = $result['position'] + 1;
        }

        return $return;
    }

    /**
     * Update positions for each featured category
     *
     * @param array $positions
     * @return bool
     */
    public function updatePosition($positions)
    {
        $i = 1;

        foreach ($positions as $idHomeFeatured) {
            if ($idHomeFeatured <> '') {
                $result = Db::getInstance()->update(
                    'responsivehomefeatured',
                    array('position' => $i),
                    'id_responsivehomefeatured = '.$idHomeFeatured
                );

                if (!$result) {
                    return false;
                }

                $i++;
            }
        }

        return true;
    }
}