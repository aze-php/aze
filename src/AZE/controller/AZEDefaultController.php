<?php
namespace AZE\controller;

class AZEDefaultController extends CommonController
{

    public function main()
    {
        $this->twig->set('subTemplate', 'home.html');
    }

    public function howToStart()
    {
        $this->twig->set('subTemplate', 'home.html');
    }

    public function controller()
    {
        $this->twig->set('subTemplate', 'controller.html');
    }
}
