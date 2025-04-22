<?php

namespace App\Service\Front;
use App\Service\Request\Request;

class FrontService
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    protected function render(string $template, array $data = []): void
    {
        extract($data);
        $templatePath = __DIR__ . '/../../Template/' . $template . '.php';
        if (file_exists($templatePath)) {
            include $templatePath;
        } else {
            echo "Template not found: " . $templatePath;
        }
    }

    public function sendJsonResponse(array $data): void
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    public function redirect(string $url, int $statusCode = 302): void
    {
        header("Location: $url", true, $statusCode);
        exit;
    }
    

}
