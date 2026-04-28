<?php
class AuthException extends Exception {
    public function errorMessage() {
        return "Auth Error: [{$this->code}] {$this->message}";
    }
}