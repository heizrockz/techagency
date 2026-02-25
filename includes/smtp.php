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
    private $timeout = 15;
    private $socket;
    private $debug = [];

    public function __construct($host, $port, $user, $pass, $encryption = 'tls') {
        $this->host = $host;
        $this->port = intval($port);
        $this->user = $user;
        $this->pass = $pass;
        $this->encryption = strtolower($encryption);
    }

    public function testConnection() {
        $this->debug = [];
        try {
            $this->connect();
            $this->auth();
            $this->sendCommand("QUIT", 221);
            fclose($this->socket);
            $this->socket = null;
            return true;
        } catch (Exception $e) {
            $this->debug[] = "Connection Test Failed: " . $e->getMessage();
            if ($this->socket) { @fclose($this->socket); $this->socket = null; }
            return $e->getMessage();
        }
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

            $this->sendCommand("MAIL FROM: <$from>", 250);
            $this->sendCommand("RCPT TO: <$to>", 250);
            $this->sendCommand("DATA", 354);

            $hostname = parse_url(BASE_URL, PHP_URL_HOST);
            if (empty($hostname)) {
                $hostname = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'micosage.com';
            }

            $boundary = "----=_NextPart_" . md5(uniqid(time()));

            $headers = [
                "MIME-Version: 1.0",
                "Content-type: multipart/alternative; boundary=\"$boundary\"",
                "To: <$to>",
                "From: $fromName <$from>",
                "Reply-To: <$from>",
                "Return-Path: <$from>",
                "Subject: $subject",
                "Date: " . date('r'),
                "Message-ID: <" . time() . "." . uniqid() . "@" . $hostname . ">",
                "X-Mailer: MicoSage-SMTP",
                "Precedence: bulk",
                "Auto-Submitted: auto-generated",
                "List-Unsubscribe: <mailto:unsubscribe@" . $hostname . "?subject=unsubscribe>"
            ];

            // Generate Plain Text fallback from HTML
            $plainBody = strip_tags(str_replace(['<br>', '<br/>', '</p>', '</h1>', '</h2>', '</h3>'], "\r\n", $fullBody));
            $plainBody = html_entity_decode($plainBody, ENT_QUOTES, 'UTF-8');
            $plainBody = trim(preg_replace('/[\r\n]+/', "\r\n", $plainBody));

            // SMTP Dot-stuffing for both structures
            $plainBody = preg_replace('/^\./m', '..', $plainBody);
            $fullHtmlBody = preg_replace('/^\./m', '..', $fullBody);

            $messageParts = [];
            $messageParts[] = implode("\r\n", $headers);
            $messageParts[] = "";
            $messageParts[] = "This is a multi-part message in MIME format.";
            $messageParts[] = "--$boundary";
            $messageParts[] = "Content-Type: text/plain; charset=\"utf-8\"";
            $messageParts[] = "Content-Transfer-Encoding: 8bit";
            $messageParts[] = "";
            $messageParts[] = $plainBody;
            $messageParts[] = "--$boundary";
            $messageParts[] = "Content-Type: text/html; charset=\"utf-8\"";
            $messageParts[] = "Content-Transfer-Encoding: 8bit";
            $messageParts[] = "";
            $messageParts[] = $fullHtmlBody;
            $messageParts[] = "--$boundary--";
            $messageParts[] = ".";

            $message = implode("\r\n", $messageParts);

            $this->sendCommand($message, 250);
            $this->sendCommand("QUIT", 221);
            
            fclose($this->socket);
            $this->socket = null;
            return true;
        } catch (Exception $e) {
            $this->debug[] = "Error: " . $e->getMessage();
            if ($this->socket) { @fclose($this->socket); $this->socket = null; }
            return false;
        }
    }

    private function connect() {
        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            ]
        ]);

        if ($this->encryption === 'ssl') {
            // SSL: connect directly over SSL on port 465
            $remote = "ssl://" . $this->host;
            $this->socket = stream_socket_client(
                "$remote:{$this->port}",
                $errno, $errstr, $this->timeout,
                STREAM_CLIENT_CONNECT, $context
            );
        } else {
            // TLS or None: connect plaintext first
            $this->socket = stream_socket_client(
                "tcp://{$this->host}:{$this->port}",
                $errno, $errstr, $this->timeout,
                STREAM_CLIENT_CONNECT, $context
            );
        }

        if (!$this->socket) {
            throw new Exception("Could not connect to {$this->host}:{$this->port} — $errstr ($errno)");
        }

        stream_set_timeout($this->socket, $this->timeout);

        // Read server greeting
        $this->getResponse(220);

        // Send EHLO
        $this->sendCommand("EHLO " . gethostname(), 250);

        // Upgrade to TLS if requested
        if ($this->encryption === 'tls') {
            $this->sendCommand("STARTTLS", 220);
            $crypto = STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT;
            if (defined('STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT')) {
                $crypto |= STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT;
            }
            $result = stream_socket_enable_crypto($this->socket, true, $crypto);
            if (!$result) {
                throw new Exception("Failed to start TLS encryption with {$this->host}");
            }
            // Re-send EHLO after TLS upgrade
            $this->sendCommand("EHLO " . gethostname(), 250);
        }
    }

    private function auth() {
        $this->sendCommand("AUTH LOGIN", 334);
        $this->sendCommand(base64_encode($this->user), 334);
        $this->sendCommand(base64_encode($this->pass), 235);
    }

    private function sendCommand($cmd, $expectedCode = null) {
        $logCmd = (strpos($cmd, 'AUTH ') === 0 || preg_match('/^[A-Za-z0-9+\/]+={0,2}$/', $cmd)) && $expectedCode === 235 ? 'AUTH ***' : $cmd;
        $this->debug[] = "C: " . $logCmd;
        fwrite($this->socket, $cmd . "\r\n");
        return $this->getResponse($expectedCode);
    }

    private function getResponse($expectedCode = null) {
        $response = "";
        $startTime = time();
        while (true) {
            $line = fgets($this->socket, 515);
            if ($line === false) {
                $info = stream_get_meta_data($this->socket);
                if (!empty($info['timed_out'])) {
                    throw new Exception("Connection timed out waiting for server response");
                }
                break;
            }
            $response .= $line;
            // SMTP multi-line: lines with format "123-..." continue, "123 ..." is last
            if (strlen($line) >= 4 && substr($line, 3, 1) === " ") break;
            if ((time() - $startTime) > $this->timeout) break;
        }
        
        $this->debug[] = "S: " . trim($response);
        
        if ($expectedCode !== null) {
            if (empty($response)) {
                throw new Exception("Expected $expectedCode but got empty response (Connection dropped or timed out)");
            }
            $code = intval(substr($response, 0, 3));
            if ($code !== $expectedCode) {
                throw new Exception("Expected $expectedCode but got $code: " . trim($response));
            }
        }
        
        return $response;
    }

    public function getDebug() {
        return $this->debug;
    }
}
