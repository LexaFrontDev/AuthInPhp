<?php

namespace App\Controller;

use App\Service\Request\Request;
use App\Service\Front\FrontService;
use App\Router\Route;
use App\Models\LogicModel\UsersLogic;

class Main extends FrontService{


    public function __construct()
    {
        parent::__construct(new Request());
    }



    #[Route(url: '/', action: 'main')]
    public function main(): void
    {
        $data = $this->request->getBody(); 
        if(empty($_SESSION['email']))  $this->redirect('register'); 
        $logic = new UsersLogic(); 
        $data['list'] = $logic->getList();
        $this->render('Main/main', ['list' => $data['list']]);
    }   

}