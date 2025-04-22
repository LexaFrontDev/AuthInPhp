<?php
namespace App\Controller;

use App\Service\Front\FrontService;
use App\Service\Jwt\JwtService;
use App\Models\LogicModel\UsersLogic;
use App\Service\Request\Request;
use App\Router\Route;
use App\Validation\FormValidation;

class Users extends FrontService
{
    protected $jwtService;
    protected $formValidation;
    public function __construct(JwtService $jwtService, FormValidation $formValidation)
    {
        $this->formValidation = $formValidation;
        $this->jwtService = $jwtService;
        parent::__construct(new Request());
    }

    #[Route(url: '/register', action: 'register')]
    public function register()
    {
        if(!empty($_COOKIE['access_token']) || !empty($_SESSION['email'])) return $this->redirect('/'); 
        $this->render('Auth/register');
    }



    #[Route(url: '/login', action: 'login')]
    public function login()
    {
        if(!empty($_COOKIE['access_token']) || !empty($_SESSION['email'])) return $this->redirect('/'); 
        $this->render('Auth/login');
    }


    #[Route(url: '/api/login', action: 'apiLogin', method: 'POST')]
    public function apiLogin()
    {
        $data = $this->request->getBody();
        $logic = new UsersLogic();
        $isLogin = $logic->loginUser(
            $data['email'] ?? '',
            $data['password'] ?? ''
        );
        if (!$isLogin) {
            return $this->sendJsonResponse([
                'success' => false,
                'message' => 'Неверный email или пароль'
            ]);
        }
        $tokens = $this->jwtService->createTokens($data['email']);
        setcookie('access_token', $tokens['access_token'], [
            'expires' => time() + 3600,
            'path' => '/',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Strict'
        ]);
        $_SESSION['email'] = $data['email'];
        return $this->sendJsonResponse([
            'success' => true,
            'message' => 'Вход выполнен успешно',
            'data' => [
                'access_token' => $tokens['access_token'],
                'refresh_token' => $tokens['refresh_token']
            ]
        ]);
    }
    

    #[Route(url: '/api/register', action: 'apiRegister', method: 'POST')]
    public function apiRegister()
    {
        $data = $this->request->getBody(); 
        $logic = new UsersLogic(); 
        $isValidate = $this->formValidation->FormValidation($data, [
            'username' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]); 
        if (!empty($isValidate)) return $this->sendJsonResponse(['success' => false, 'message' => reset($isValidate)]);
        $isSave = $logic->registerUser(
            $data['username'] ?? '',
            $data['email'] ?? '',
            $data['password'] ?? ''
        );
        if (!$isSave) return $this->sendJsonResponse(['success' => false, 'message' => 'Регистрация не удалась']);
        $tokens = $this->jwtService->createTokens($data['email']);
        setcookie('access_token', $tokens['access_token'], [
            'expires' => time() + 3600,
            'path' => '/',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Strict'
        ]);
        $_SESSION['email'] = $data['email'];
        return $this->sendJsonResponse([
            'success' => true, 
            'message' => 'Регистрация успешьно завершена',
            'data' => [
                'user' => $data, 
                'access_token' => $tokens['access_token'], 
                'refresh_token' => $tokens['refresh_token']
            ]
        ]);
    }

    #[Route(url: '/api/logout', action: 'apiRegister')]
    public function logout()
    {
        if(empty($_SESSION['email']) || empty($_COOKIE['access_token'])) {
            unset($_SESSION['email']);
            setcookie('access_token', '', time() - 3600, '/');
            setcookie('refresh_token', '', time() - 3600, '/');
        }
        return $this->sendJsonResponse(['success' => true, 'message' => 'Вы вышли из системы']);
    }
    
}
