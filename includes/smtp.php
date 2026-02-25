<?php
/**
 * Mico Sage — Minimalist SMTP Client
 * Simple implementation to avoid external dependencies.
 */

class MicoSMTP {
    private $host;
    private $port;
    private $user;
    private $pass;
    private $encryption;
    private $timeout = 10;
    private $socket;
    private $debug = [];

    public function __construct($host, $port, $user, $pass, $encryption = 'tls') {
        $this->host = $host;
        $this->port = $port;
        $this->user = $user;
        $this->pass = $pass;
        $this->encryption = strtolower($encryption);
    }

    public function send($to, $from, $fromName, $subject, $body, $signature = '') {
        $this->debug = [];
        try {
            $this->connect();
            $this->auth();
            
            $fullBody = $body;
            if (!empty($signature)) {
                $fullBody .= "<br><br>--<br>" . $signature;
            }

            $this->sendCommand("MAIL FROM: <$from>");
            $this->sendCommand("RCPT TO: <$to>");
            $this->sendCommand("DATA");

            $headers = [
                "MIME-Version: 1.0",
                "Content-type: text/html; charset=UTF-8",
                "To: <$to>",
                "From: $fromName <$from>",
                "Subject: $subject",
                "Date: " . date('r'),
                "Message-ID: <" . time() . "." . uniqid() . "@" . $this->host . ">"
            ];

            $message = implode("\r\n", $headers) . "\r\n\r\n" . $fullBody . "\r\n.";
            $this->sendCommand($message);
            $this->sendCommand("QUIT");
            
            fclose($this->socket);
            return true;
        } catch (Exception $e) {
            $this->debug[] = "Error: " . $e->getMessage();
            return false;
        }
    }

    private function connect() {
        $remote = $this->host . ":" . $this->port;
        if ($this->encryption === 'ssl') {
            $remote = "ssl://" . $remote;
        }

        $this->socket = fsockopen($remote, $this->port, $errno, $errstr, $this->timeout);
        if (!$this->socket) {
            throw new Exception("Could not connect to $remote: $errstr ($errno)");
        }

        $this->getResponse();

        if ($this->encryption === 'tls') {
            $this->sendCommand("EHLO " . $this->host);
            $this->sendCommand("STARTTLS");
            if (!stream_socket_enable_crypto($this->socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                throw new Exception("Failed to start TLS encryption");
            }
        }

        $this->sendCommand("EHLO " . $this->host);
    }

    private function auth() {
        $this->sendCommand("AUTH LOGIN");
        $this->sendCommand(base64_encode($this->user));
        $this->sendCommand(base64_encode($this->pass));
    }

    private function sendCommand($cmd) {
        fwrite($this->socket, $cmd . "\r\n");
        return $this->getResponse();
    }

    private function getResponse() {
        $response = "";
        while ($line = fgets($this->socket, 515)) {
            $response .= $line;
            if (substr($line, 3, 1) === " ") break;
        }
        $this->debug[] = "S: " . $response;
        return $response;
    }

    public function getDebug() {
        return $this->debug;
    }
}
