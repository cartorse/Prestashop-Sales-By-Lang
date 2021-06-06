<?php

if (!defined('_PS_VERSION_'))
    exit;

class Salesbylang extends Module {

    //construct
    public function __construct() {
        $this->name = 'salesbylang';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Burxi';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->ps_versions_compliancy = array('min'=>'1.6','max'=>_PS_VERSION_);

        parent::__construct();

        $this->displayName = $this->l('Sales by Language');
        $this->description = $this->l('This module allows yo to get the sales of your store between two chosen dates for each language configured on your store.');
    }//constructor

    //install
    public function install(){
        if(!parent::install()){
            return false;
        }//if
        if( !$this->registerHook('displayBackOfficeHeader')){
            return false;
        }//else if
        else{
            return true;
        }//else
    }//install
    
    //uninstall
    public function uninstall(){
        return parent::uninstall();
    }//uninstall


    public function hookdisplayBackOfficeHeader(){
        $this->context->controller->addCSS($this->_path."views/css/salesbylang.css");
    }//hookdisplayBackOfficeHeader

    public function getContent(){
        $languages_aux = array();
        $languages = Language::getLanguages(false);

        foreach($languages as $lang){
            array_push($languages_aux,[$lang["id_lang"],$lang["name"]]);
        }//foreach

        $this->smarty->assign("languages",$languages_aux);
        $this->smarty->assign("updateURL",FALSE);
        $this->smarty->assign("query",FALSE);

        if(Tools::isSubmit("submitConfig")){
            Configuration::updateValue("fromDate",Tools::getValue("fromDate"));
            Configuration::updateValue("toDate",Tools::getValue("toDate"));
            Configuration::updateValue("language",Tools::getValue("language"));
            $this->smarty->assign("updateURL",TRUE);
            $excel = Salesbylang::queryInfo(Tools::getValue("fromDate"),Tools::getValue("toDate"),Tools::getValue("language"));
            Salesbylang::prepareCSV($excel);
        }//if

        $this->smarty->assign("fromDate",Configuration::get("fromDate"));
        $this->smarty->assign("toDate",Configuration::get("toDate"));
        $this->smarty->assign("lang",Configuration::get("language"));

        return $this->display(__FILE__,'configure.tpl');
    }//getContent

    public function queryInfo($fr,$to,$lang){
        $finalCSV = array();
        $sql = "
        SELECT DISTINCT
            cust.firstname AS firstname,
            cust.lastname AS surname,
            cust.email AS email,
            GROUP_CONCAT( DISTINCT country.name  SEPARATOR ', ') AS lang
        FROM 
            `ps_orders` ord
        INNER JOIN  
            `ps_customer` cust
        ON 
            ord.id_customer = cust.id_customer
        INNER JOIN 
            `ps_lang` lang
        ON 
            ord.id_lang = lang.id_lang
        INNER JOIN 
            `ps_address` addr
        ON 
            ord.id_address_delivery = addr.id_address
        INNER JOIN 
            `ps_country_lang` country
        ON 
            addr.id_country = country.id_country
        WHERE 
            ord.date_add BETWEEN '".$fr."' AND '".$to."' AND
            ord.id_lang = ".$lang." AND
            country.id_lang = 1
        GROUP BY 
            cust.id_customer
        ";
        $sql_res = Db::getInstance()->executeS($sql);
        $columnaCSV = ['NAME', 'SURNAME', 'EMAIL', 'LANGUAJE'];
        array_push($finalCSV, $columnaCSV);
        foreach ($sql_res as $entry){
            array_push($finalCSV, [$entry["firstname"],$entry["surname"],$entry["email"],$entry["lang"]]);
        }//foreach
        $this->smarty->assign("users",$sql_res);
        $this->smarty->assign("query",TRUE);
        $this->smarty->assign("shop",__PS_BASE_URI__);
        return $finalCSV;
    }//queryInfo

    public function prepareCSV($excel){
        $fp = fopen(__DIR__.'/excel.csv', 'w');
        foreach ($excel as $campos) {
            fputcsv($fp, $campos);
        }//foreach
        fclose($fp);
    }//prepareCSV

}//Salesbylang