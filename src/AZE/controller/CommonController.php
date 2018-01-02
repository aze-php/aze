<?php
namespace AZE\controller;

use AZE\core\Controller;
use AZE\twig\TwigConfig;
use AZE\twig\TwigExtended;

class CommonController extends Controller
{
    public $twig = null;

    public function __construct($action)
    {
        $twigConfig = new TwigConfig();
        $twigConfig->setTemplateDir(__DIR__ . '/twig/template');
        $twigConfig->setCacheDir(__DIR__ . '/twig/cache');
        $twigConfig->setAutoReload(true);

        $this->twig = new TwigExtended($twigConfig);

        $this->twig->addCss('https://cdnjs.cloudflare.com/ajax/libs/materialize/0.98.0/css/materialize.min.css');
        $this->twig->addCss('https://cdnjs.cloudflare.com/ajax/libs/prism/1.8.1/themes/prism.min.css');

        $this->twig->addJs('https://code.jquery.com/jquery-2.1.1.min.js');
        $this->twig->addJs('https://cdnjs.cloudflare.com/ajax/libs/materialize/0.98.0/js/materialize.min.js');
        $this->twig->addJs('https://cdnjs.cloudflare.com/ajax/libs/prism/1.8.1/prism.min.js');
        $this->twig->addJs('https://cdnjs.cloudflare.com/ajax/libs/prism/1.8.1/components/prism-php.min.js');
        $this->twig->addJs('https://cdnjs.cloudflare.com/ajax/libs/prism/1.8.1/components/prism-bash.min.js');

        $this->addCallAfter(function($controller){
            $controller->twig->render('common.html');
        });

        parent::__construct($action);
    }
}