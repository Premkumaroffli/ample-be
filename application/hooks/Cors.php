<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cors {
    public function handle() {header("Access-Control-Allow-Origin: *"); // Angular URL
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");
        // header("Access-Control-Allow-Credentials: true");

        // Stop execution if it's a preflight (OPTIONS) request
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit();
        }
    }
}
