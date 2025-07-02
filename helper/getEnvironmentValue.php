<?php
namespace Helper;

class EnvironmentValue {

    public function __construct() {
        $this->loadEnv();
    }
    private function loadEnv() {
        $envPath = dirname(__DIR__) . '/.env';
        if (!file_exists($envPath)) {
            throw new \Exception('.env file not found');
        }

        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) continue;
            list($name, $value) = explode('=', $line, 2);
            $_ENV[trim($name)] = trim($value);
        }
    }

    public function getSecretKey($env_name) {
        // Return the JWT secret key from the .env file
        return $_ENV[$env_name];
    }
}
?>