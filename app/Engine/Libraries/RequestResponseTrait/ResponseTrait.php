<?php

trait ResponseTrait {
    
    public function getResponse() {
        return $this;
    }
       
    
    // Respond as JSON
    public function send($obj) {
        header("Content-Type: application/json; charset=UTF-8");
        echo toJSON($obj);
    }
    
    
    // Response code
    public function status(int $response_code) {
        http_response_code($response_code);
        return $this;
    }


    // Render veiw
    public function render(string $viewPath, array $arguments = []) {
        $templates = new League\Plates\Engine(APPROOT . "/Views");
        echo $templates->render($viewPath, $arguments);
    }


    // Redirect
    public function redirect(string $url) {
        return header('Location: ' . $url);
    }


    // Redirect back
    public function redirectBack() {

        if (hasFlashData('previous_url'))
            return $this->redirect(URLROOT . "/" . getFlashData('previous_url'));
        else {
            return $this->redirect(URLROOT);
        }
    }
}
