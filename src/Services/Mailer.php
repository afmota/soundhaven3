<?php
namespace App\Services;

class Mailer {
    private static function getConfig(): array {
        $envFile = __DIR__ . '/../../.env';
        $env = [];
        if (file_exists($envFile)) {
            $parsed = parse_ini_file($envFile);
            if ($parsed !== false) {
                $env = $parsed;
            }
        }

        return [
            'host' => getenv('SMTP_HOST') ?: ($env['SMTP_HOST'] ?? 'mail'),
            'port' => (int)(getenv('SMTP_PORT') ?: ($env['SMTP_PORT'] ?? 1025)),
            'user' => getenv('SMTP_USER') ?: ($env['SMTP_USER'] ?? ''),
            'pass' => getenv('SMTP_PASS') ?: ($env['SMTP_PASS'] ?? ''),
            'secure' => strtolower(trim((string)(getenv('SMTP_SECURE') ?: ($env['SMTP_SECURE'] ?? 'none')))),
            'from_address' => getenv('MAIL_FROM_ADDRESS') ?: ($env['MAIL_FROM_ADDRESS'] ?? 'soundhaven.midiacollection@gmail.com'),
            'from_name' => getenv('MAIL_FROM_NAME') ?: ($env['MAIL_FROM_NAME'] ?? 'SoundHaven 3'),
        ];
    }

    public static function send(string $toEmail, string $toName, string $subject, string $htmlBody, string $textBody = ''): bool {
        $config = self::getConfig();

        $host = $config['host'];
        $port = $config['port'];
        $secure = $config['secure'];
        $fromEmail = $config['from_address'];
        $fromName = $config['from_name'];

        $remoteSocket = ($secure === 'ssl' ? 'ssl://' : 'tcp://') . $host . ':' . $port;

        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            ]
        ]);

        $socket = @stream_socket_client($remoteSocket, $errno, $errstr, 15, STREAM_CLIENT_CONNECT, $context);
        if (!$socket) {
            error_log("Erro ao conectar ao servidor SMTP ($remoteSocket): $errstr ($errno)");
            return false;
        }

        stream_set_timeout($socket, 15);

        $getResponse = function() use ($socket): string {
            $data = '';
            while ($str = fgets($socket, 515)) {
                $data .= $str;
                if (substr($str, 3, 1) === ' ') {
                    break;
                }
            }
            return $data;
        };

        $sendCommand = function(string $cmd, array $expectedCodes = [250]) use ($socket, $getResponse): bool {
            fputs($socket, $cmd . "\r\n");
            $response = $getResponse();
            $code = (int)substr($response, 0, 3);
            if (!in_array($code, $expectedCodes, true)) {
                error_log("Comando SMTP falhou: $cmd -> Resposta: $response");
                return false;
            }
            return true;
        };

        // Ler saudação inicial
        $greeting = $getResponse();
        if ((int)substr($greeting, 0, 3) !== 220) {
            fclose($socket);
            return false;
        }

        // EHLO
        if (!$sendCommand('EHLO localhost', [250])) {
            $sendCommand('HELO localhost', [250]);
        }

        // STARTTLS se solicitado
        if ($secure === 'tls') {
            if ($sendCommand('STARTTLS', [220])) {
                if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                    fclose($socket);
                    return false;
                }
                $sendCommand('EHLO localhost', [250]);
            }
        }

        // Autenticação se usuário configurado
        if (!empty($config['user']) && !empty($config['pass'])) {
            if ($sendCommand('AUTH LOGIN', [334])) {
                if (!$sendCommand(base64_encode($config['user']), [334])) {
                    fclose($socket);
                    return false;
                }
                if (!$sendCommand(base64_encode($config['pass']), [235])) {
                    fclose($socket);
                    return false;
                }
            }
        }

        // MAIL FROM
        if (!$sendCommand("MAIL FROM:<$fromEmail>", [250])) {
            fclose($socket);
            return false;
        }

        // RCPT TO
        if (!$sendCommand("RCPT TO:<$toEmail>", [250, 251])) {
            fclose($socket);
            return false;
        }

        // DATA
        if (!$sendCommand('DATA', [354])) {
            fclose($socket);
            return false;
        }

        $boundary = '=_SoundHaven_' . md5(uniqid((string)time(), true));

        $headers = [];
        $headers[] = 'Date: ' . date('r');
        $headers[] = 'From: ' . '=?UTF-8?B?' . base64_encode($fromName) . '?= <' . $fromEmail . '>';
        $headers[] = 'To: ' . '=?UTF-8?B?' . base64_encode($toName) . '?= <' . $toEmail . '>';
        $headers[] = 'Subject: =?UTF-8?B?' . base64_encode($subject) . '?=';
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-Type: multipart/alternative; boundary="' . $boundary . '"';

        $body = [];
        if (!empty($textBody)) {
            $body[] = '--' . $boundary;
            $body[] = 'Content-Type: text/plain; charset=UTF-8';
            $body[] = 'Content-Transfer-Encoding: base64';
            $body[] = '';
            $body[] = chunk_split(base64_encode($textBody));
        }

        $body[] = '--' . $boundary;
        $body[] = 'Content-Type: text/html; charset=UTF-8';
        $body[] = 'Content-Transfer-Encoding: base64';
        $body[] = '';
        $body[] = chunk_split(base64_encode($htmlBody));
        $body[] = '--' . $boundary . '--';
        $body[] = '.';

        $messagePayload = implode("\r\n", $headers) . "\r\n\r\n" . implode("\r\n", $body) . "\r\n";

        fputs($socket, $messagePayload);
        $dataResponse = $getResponse();
        $code = (int)substr($dataResponse, 0, 3);

        $sendCommand('QUIT', [221, 250]);
        fclose($socket);

        return ($code === 250);
    }

    public static function sendApprovalNotification(string $toEmail, string $toName, string $username): bool {
        $subject = 'Seu acesso ao SoundHaven 3 foi autorizado!';

        $textBody = "Olá, {$toName}!\n\n"
                  . "Seu cadastro no SoundHaven 3 foi aprovado pelo administrador.\n"
                  . "Você já pode acessar o sistema com o seu usuário: {$username}\n\n"
                  . "Acesse agora: http://localhost/index.php?url=login\n\n"
                  . "Atenciosamente,\nEquipe SoundHaven 3";

        $htmlBody = <<<HTML
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #0b0f19; color: #f8fafc; margin: 0; padding: 24px; }
        .container { max-width: 580px; margin: 0 auto; background: #121826; border: 1px solid rgba(255,255,255,0.1); border-radius: 16px; padding: 36px 32px; box-shadow: 0 12px 36px rgba(0,0,0,0.5); }
        .header { text-align: center; margin-bottom: 28px; }
        .logo-text { font-size: 26px; font-weight: 800; background: linear-gradient(135deg, #8b5cf6, #ec4899); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .badge { display: inline-block; background: #166534; color: #86efac; padding: 4px 12px; border-radius: 999px; font-size: 13px; font-weight: 700; margin-top: 8px; }
        h1 { font-size: 20px; color: #f8fafc; margin-top: 16px; margin-bottom: 8px; text-align: center; }
        p { color: #94a3b8; font-size: 15px; line-height: 1.6; margin: 12px 0; }
        .user-box { background: rgba(139, 92, 246, 0.12); border: 1px solid rgba(139, 92, 246, 0.3); border-radius: 10px; padding: 14px 18px; margin: 24px 0; }
        .user-box strong { color: #fff; }
        .btn { display: block; text-align: center; background: linear-gradient(135deg, #8b5cf6, #ec4899); color: #ffffff !important; text-decoration: none; padding: 14px 28px; border-radius: 10px; font-weight: 700; font-size: 15px; margin: 28px 0 16px 0; box-shadow: 0 4px 14px rgba(139, 92, 246, 0.4); }
        .footer { text-align: center; font-size: 12px; color: #64748b; margin-top: 28px; border-top: 1px solid rgba(255,255,255,0.08); padding-top: 16px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo-text">SoundHaven 3</div>
            <div class="badge">&#10003; Acesso Aprovado</div>
        </div>
        <h1>Olá, {$toName}!</h1>
        <p>Temos o prazer de informar que o seu cadastro no <strong>SoundHaven 3</strong> foi aprovado com sucesso pelo administrador.</p>
        <div class="user-box">
            <p style="margin: 0; color: #cbd5e1;">Seu usuário de acesso é: <strong>@{$username}</strong></p>
            <p style="margin: 4px 0 0 0; font-size: 13px; color: #94a3b8;">Utilize a senha cadastrada anteriormente para realizar o login.</p>
        </div>
        <a href="http://localhost/index.php?url=login" class="btn">Entrar no SoundHaven 3</a>
        <div class="footer">
            Mensagem automática enviada por <strong>soundhaven.midiacollection@gmail.com</strong>.<br>
            SoundHaven 3 &bull; Acervo Musical Digital
        </div>
    </div>
</body>
</html>
HTML;

        return self::send($toEmail, $toName, $subject, $htmlBody, $textBody);
    }
}