<?php
namespace App\Main\Template;

use App\Main\Core\Core;
/**
 * Шаблонизатор
 */
class Template extends \Smarty
{
    public function __construct()
    {
        parent::__construct();

        $core = Core::getInstance();
        $config = $core->config['smarty'];

        $this->setTemplateDir($config['template_dir']);
        $this->setCompileDir($config['template_compile_dir']);
        $this->setPluginsDir(__DIR__ . '/smarty_additional');
        $this->assign('core', $core);
    }
}