<?php

class SearchController extends SearchControllerCore
{
    public function initContent()
    {
        parent::initContent();

        $query = Tools::getValue('s');
        error_log("Search query received in override: " . $query);

        $results = $this->searchByRefOrMpn($query);

        if ($results) {
            $this->context->smarty->assign('search_products', $results);
        } else {
            $this->context->smarty->assign('search_products', $this->searchProducts($query));
        }

        $this->setTemplate(_PS_THEME_DIR_.'search.tpl');
    }

    private function searchByRefOrMpn($query)
    {
        error_log("Search by ref or mpn called with query: " . $query);
        $sql = 'SELECT pa.id_product, pa.id_product_attribute, pa.reference, pa.supplier_reference, pa.mpn FROM '._DB_PREFIX_.'product_attribute pa
                WHERE pa.reference LIKE "%'.pSQL($query).'%" OR pa.supplier_reference LIKE "%'.pSQL($query).'%" OR pa.mpn LIKE "%'.pSQL($query).'%" LIMIT 10';

        $results = Db::getInstance()->executeS($sql);

        error_log("SQL Results for search by ref or mpn: " . print_r($results, true));

        if ($results) {
            $products = [];
            foreach ($results as $result) {
                $products[] = new Product($result['id_product'], true, Context::getContext()->language->id);
            }
            return $products;
        }

        return false;
    }

    private function searchProducts($query)
    {
        $sql = 'SELECT p.id_product FROM '._DB_PREFIX_.'product p
                LEFT JOIN '._DB_PREFIX_.'product_lang pl ON (p.id_product = pl.id_product AND pl.id_lang = '.(int)Context::getContext()->language->id.')
                WHERE pl.name LIKE "%'.pSQL($query).'%" LIMIT 10';

        $results = Db::getInstance()->executeS($sql);

        error_log("SQL Results for search products: " . print_r($results, true));

        if ($results) {
            $products = [];
            foreach ($results as $result) {
                $products[] = new Product($result['id_product'], true, Context::getContext()->language->id);
            }
            return $products;
        }

        return [];
    }
}
