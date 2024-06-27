<?php
include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');

$query = Tools::getValue('query');
$action = Tools::getValue('action');

try {
    if ($action == 'suggest') {
        // Funkcja podpowiedzi produktów
        $sql = 'SELECT pa.id_product, pa.id_product_attribute, pa.reference, pa.supplier_reference, pa.mpn FROM '._DB_PREFIX_.'product_attribute pa
                WHERE pa.reference LIKE "%'.pSQL($query).'%" OR pa.supplier_reference LIKE "%'.pSQL($query).'%" OR pa.mpn LIKE "%'.pSQL($query).'%" LIMIT 10';
        
        // Debug: Logowanie zapytania SQL
        error_log("SQL Query for suggestions: " . $sql);
        
        $results = Db::getInstance()->executeS($sql);
        
        // Debug: Logowanie wyników zapytania
        error_log("SQL Results for suggestions: " . print_r($results, true));
        
        $suggestions = [];
        $link = new Link();
        foreach ($results as $result) {
            $product = new Product($result['id_product'], true, Context::getContext()->language->id);
            $image = Image::getCover($result['id_product']); // Pobranie domyślnej miniaturki
            if ($image) {
                $imagePath = Tools::getHttpHost(true) . __PS_BASE_URI__ . 'img/p/' . Image::getImgFolderStatic($image['id_image']) . $image['id_image'] . '-home_default.jpg';
            } else {
                $imagePath = Tools::getHttpHost(true) . __PS_BASE_URI__ . 'img/p/' . _THEME_PROD_DIR_ . 'default-home_default.jpg'; // Ścieżka do domyślnego obrazka, jeśli brak miniaturki
            }
            error_log("Generated image path: " . $imagePath); // Logowanie wygenerowanej ścieżki obrazu
            $suggestions[] = [
                'id_product' => $result['id_product'],
                'id_product_attribute' => $result['id_product_attribute'],
                'reference' => $result['reference'],
                'supplier_reference' => $result['supplier_reference'],
                'mpn' => $result['mpn'],
                'name' => $product->name,
                'link' => $link->getProductLink($product, null, null, null, null, null, $result['id_product_attribute']),
                'image' => $imagePath
            ];
        }

        if (empty($suggestions)) {
            // Jeśli brak wyników na podstawie MPN i kodów referencyjnych, wykonaj standardowe wyszukiwanie
            $sql = 'SELECT p.id_product, pl.name FROM '._DB_PREFIX_.'product p
                    LEFT JOIN '._DB_PREFIX_.'product_lang pl ON (p.id_product = pl.id_product AND pl.id_lang = '.(int)$context->language->id.')
                    WHERE pl.name LIKE "%'.pSQL($query).'%" LIMIT 10';
            
            error_log("SQL Query for standard search: " . $sql);
            $results = Db::getInstance()->executeS($sql);
            
            error_log("SQL Results for standard search: " . print_r($results, true));
            
            foreach ($results as $result) {
                $product = new Product($result['id_product'], true, Context::getContext()->language->id);
                $image = Image::getCover($result['id_product']);
                if ($image) {
                    $imagePath = Tools::getHttpHost(true) . __PS_BASE_URI__ . 'img/p/' . Image::getImgFolderStatic($image['id_image']) . $image['id_image'] . '-home_default.jpg';
                } else {
                    $imagePath = Tools::getHttpHost(true) . __PS_BASE_URI__ . 'img/p/' . _THEME_PROD_DIR_ . 'default-home_default.jpg';
                }
                error_log("Generated image path: " . $imagePath);
                $suggestions[] = [
                    'id_product' => $result['id_product'],
                    'name' => $result['name'],
                    'link' => $link->getProductLink($product),
                    'image' => $imagePath
                ];
            }
        }
        
        error_log("Suggestions: " . print_r($suggestions, true));
        
        die(json_encode(['success' => true, 'suggestions' => $suggestions]));
    }

    // Funkcja przekierowania do właściwego produktu
    $sql = 'SELECT pa.id_product, pa.id_product_attribute FROM '._DB_PREFIX_.'product_attribute pa
            WHERE pa.reference = "'.pSQL($query).'" OR pa.supplier_reference = "'.pSQL($query).'" OR pa.mpn = "'.pSQL($query).'"';
    
    error_log("SQL Query for product: " . $sql);
    
    $result = Db::getInstance()->getRow($sql);
    
    error_log("SQL Result for product: " . print_r($result, true));
    
    if ($result) {
        $product = new Product($result['id_product'], true, Context::getContext()->language->id);
        $link = new Link();
        $url = $link->getProductLink($product, null, null, null, null, null, $result['id_product_attribute']);
        error_log("Redirecting to: " . $url);
        die(json_encode(['success' => true, 'url' => $url]));
    } else {
        // Jeśli nie znaleziono produktu na podstawie MPN i kodu referencyjnego, wykonaj standardowe wyszukiwanie
        $sql = 'SELECT p.id_product FROM '._DB_PREFIX_.'product p
                LEFT JOIN '._DB_PREFIX_.'product_lang pl ON (p.id_product = pl.id_product AND pl.id_lang = '.(int)$context->language->id.')
                WHERE pl.name LIKE "%'.pSQL($query).'%"';
        
        error_log("SQL Query for standard product search: " . $sql);
        
        $results = Db::getInstance()->executeS($sql);
        
        if ($results) {
            $product = new Product($results[0]['id_product'], true, Context::getContext()->language->id);
            $url = $link->getProductLink($product);
            error_log("Redirecting to: " . $url);
            die(json_encode(['success' => true, 'url' => $url]));
        } else {
            die(json_encode(['success' => false]));
        }
    }
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    die(json_encode(['success' => false, 'error' => $e->getMessage()]));
}
?>
