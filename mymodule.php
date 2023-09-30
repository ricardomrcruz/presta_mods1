<?php


class Mymodule extends Module
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

        return parent::install() && $this->registerHook('displayLeftColumn');
    }

    public function uninstall()
    {
        return parent::uninstall() && $this->unregisterHook('displayLeftColumn');
    }

    public function postProcess()
    {
            if(Tools::isSubmit('btnSubmitMyModule')){
                
                // dump(Tools::getAllValues());
                Configuration::updateValue('TEST_INPUT', Tools::getValue('TEST_INPUT'));
            }
            return 'no submit';
    }
    
    public function getContent()
    {
        return $this->postProcess().$this->renderForm();
    }

    public function hookdisplayLeftColumn()
    {
        return 'Coucou';
    }


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
                        'type' => 'text',
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
}
