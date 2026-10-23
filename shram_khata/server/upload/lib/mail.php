<?php
declare(strict_types=1);

/**
 * Sends one plain-text email.
 *
 * config.php may contain an 'smtp' block:
 *   'smtp' => ['host' => 'mail.oneweblink.com', 'port' => 465, 'secure' => 'ssl',   // or 587 + 'tls'
 *              'user' => 'noreply@oneweblink.com', 'pass' => '...'],
 * Without it PHP's mail() is used (works on most shared hosting).
 */
function send_mail(string $to, string $subject, string $body): bool {
    $from = (string) cfg('mail_from', 'noreply@oneweblink.com');
    $fromName = (string) cfg('mail_from_name', 'HazriBook');
    $smtp = cfg('smtp', null);
    if (is_array($smtp) && !empty($smtp['host'])) return smtp_send($smtp, $from, $fromName, $to, $subject, $body);

    $headers = [
        'From: ' . mb_encode_mimeheader($fromName) . " <$from>",
        'Reply-To: ' . $from,
        'MIME-Version: 1.0',
        'Content-Type: text/plain; charset=UTF-8',
        'X-Mailer: HazriBook',
    ];
    return @mail($to, mb_encode_mimeheader($subject), $body, implode("\r\n", $headers), '-f' . $from);
}

function smtp_send(array $c, string $from, string $fromName, string $to, string $subject, string $body): bool {
    $secure = (string) ($c['secure'] ?? 'ssl');
    $host = (string) $c['host'];
    $port = (int) ($c['port'] ?? ($secure === 'ssl' ? 465 : 587));
    $fp = @stream_socket_client(($secure === 'ssl' ? 'ssl://' : 'tcp://') . "$host:$port", $errno, $err, 12);
    if (!$fp) {
        error_log("HazriBook SMTP connect failed: $err");
        return false;
    }
    stream_set_timeout($fp, 15);
    $read = function () use ($fp): string {
        $out = '';
        while (($line = fgets($fp, 515)) !== false) {
            $out .= $line;
            if (strlen($line) < 4 || $line[3] === ' ') break;
        }
        return $out;
    };
    $cmd = function (string $line, string $expect) use ($fp, $read): bool {
        fwrite($fp, $line . "\r\n");
        return str_starts_with($read(), $expect);
    };
    $ok = str_starts_with($read(), '220') && $cmd('EHLO oneweblink.com', '250');
    if ($ok && $secure === 'tls') {
        $ok = $cmd('STARTTLS', '220') && stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)
            && $cmd('EHLO oneweblink.com', '250');
    }
    $ok = $ok && $cmd('AUTH LOGIN', '334') && $cmd(base64_encode((string) $c['user']), '334')
        && $cmd(base64_encode((string) $c['pass']), '235')
        && $cmd("MAIL FROM:<$from>", '250') && $cmd("RCPT TO:<$to>", '250') && $cmd('DATA', '354');
    if ($ok) {
        $msg = 'From: ' . mb_encode_mimeheader($fromName) . " <$from>\r\nTo: <$to>\r\nSubject: " . mb_encode_mimeheader($subject)
            . "\r\nDate: " . date('r') . "\r\nMIME-Version: 1.0\r\nContent-Type: text/plain; charset=UTF-8\r\n"
            . "Content-Transfer-Encoding: 8bit\r\n\r\n" . preg_replace('/^\./m', '..', str_replace("\n", "\r\n", str_replace("\r\n", "\n", $body)));
        fwrite($fp, $msg . "\r\n.\r\n");
        $ok = str_starts_with($read(), '250');
    }
    @fwrite($fp, "QUIT\r\n");
    fclose($fp);
    if (!$ok) error_log('HazriBook SMTP send failed');
    return $ok;
}
