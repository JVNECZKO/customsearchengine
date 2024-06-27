<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class SearchByRefMpn extends Module
{
    public function __construct()
    {
        $this->name = 'searchbyrefmpn';
        $this->tab = 'search_filter';
        $this->version = '1.0.0';
        $this->author = 'Łukasz Janeczko';
        $this->need_instance = 0;

        parent::__construct();

        $this->displayName = $this->l('Search by Reference or MPN');
        $this->description = $this->l('Allows customers to search by reference code or MPN and redirects to the appropriate product combination.');
    }

    public function install()
    {
        return parent::install() && $this->registerHook('displaySearchByRefMpn') && $this->registerHook('displayHeader');
    }

    public function uninstall()
    {
        return parent::uninstall() && $this->unregisterHook('displaySearchByRefMpn') && $this->unregisterHook('displayHeader');
    }

    public function hookDisplayHeader($params)
    {
        $this->context->controller->addJS($this->_path . 'views/js/searchbyrefmpn.js');
    }

    public function hookDisplaySearchByRefMpn($params)
    {
        return $this->display(__FILE__, 'views/templates/hook/searchbyrefmpn.tpl');
    }
}
