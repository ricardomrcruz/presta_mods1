<?php

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;



class Mymodule extends Module implements WidgetInterface
{

    public function __construct()
    {
        $this->name = 'mymodule';
        $this->author = 'strasbourgwebsolutions';
        $this->version = '1.0.0';
        $this->need_instance = 0;

        $this->ps_versions_compliancy = [
            'min' => '1.7.1.0',
            'max' => _PS_VERSION_,
        ];

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->trans('This is my custom module', [], 'Modules.MyModule.Admin');
        $this->description = $this->trans('Display text on the left column', [], 'Modules.MyModule.Admin');

        $this->templateFile = 'module:mymodule/views/templates/hook/mymodule.tpl';
    }


    public function install()
    {
        $this->_clearCache('*');

        return parent::install() && $this->registerHook('displayLeftColumn') && $this->registerHook('displayHeader');
    }

    public function uninstall()
    {
        return parent::uninstall() && $this->unregisterHook('displayLeftColumn') && $this->unregisterHook('displayHeader');
    }

    public function postProcess()
    {
            if(Tools::isSubmit('btnSubmitMyModule')){
                
                // dump(Tools::getAllValues());
                Configuration::updateValue('TEST_INPUT', Tools::getValue('TEST_INPUT'), true);
            }
            return 'no submit';
    }

    // true lets you insert html into the dom bypassing security
    
    public function getContent()
    {

        $this->registerHook('displayCustom');

        return $this->postProcess().$this->renderForm();
    }

    public function hookdisplayHeader(){
        
        if($this->context->controller->php_self == 'category')
        {
        
            $this->context->controller->addCSS($this->_path . 'views/css/front.css', 'all');
        
        }
    }

    public function hookdisplayCustom(){

        return "display test";
    }

    // you can find the name of the controller (index, category, contact etc.) by inpecting the id of 
    // the body element of the page you working on


                                    //   HOOK
// --------------------------------------------------------------------
    // public function hookdisplayLeftColumn() 
    // {
    //     $this->context->smarty->assign('message_mod', Configuration::get('TEST_INPUT'));
    //     return $this->fetch($this->templateFile);
    // }
// ------------------------------------------------------------------




    // this hook is commented due to implementing the widgetinterface which opens the possibility to get all the hooks from the backoffice


    public function renderForm()
    {
        $fields_form = [
            'form' => [
                'legend' => [
                    'title' => $this->trans('Contact details', [], 'Modules.Checkpayment.Admin'),
                    'icon' => 'icon-envelope',
                ],
                'input' => [
                    [
                        'type' => 'textarea',
                        'autoload_rte' => true,
                        'label' => $this->trans('test input', [], 'Modules.Checkpayment.Admin'),
                        'name' => 'TEST_INPUT',
                        'required' => true,
                    ],
                    // [
                    //     'type' => 'textarea',
                    //     'label' => $this->trans('Address', [], 'Modules.Checkpayment.Admin'),
                    //     'desc' => $this->trans('Address where the check should be sent to.', [], 'Modules.Checkpayment.Admin'),
                    //     'name' => 'CHEQUE_ADDRESS',
                    //     'required' => true,
                    // ],
                ],
                'submit' => [
                    'title' => $this->trans('Save', [], 'Admin.Actions'),
                ],
            ],
        ];

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->id = (int) Tools::getValue('id_carrier');
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'btnSubmitMyModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = [
            'fields_value' => $this->getConfigFieldsValues(),
        ];

        return $helper->generateForm([$fields_form]);
    }

    public function getConfigFieldsValues()
    {
        
        return [
            'TEST_INPUT' => Tools::getValue('TEST_INPUT', Configuration::get('TEST_INPUT')),
            
        ];
    }

    

/**
     * {@inheritdoc}
     */
    public function renderWidget($hookName, array $configuration)
    {
        $this->smarty->assign($this->getWidgetVariables($hookName, $configuration));

        return $this->fetch(
            $this->templateFile
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetVariables($hookName, array $configuration)
    {
        
        return [
            'message_mod' => Configuration::get('TEST_INPUT')
        ];
    }
}