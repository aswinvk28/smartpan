<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Drupal\smartpan\Mail;

use Drupal\Core\Mail\PhpMail;

/**
 * Description of SmartPanMail
 *
 * @author aswinvk28
 */
class SmartPanMail extends PhpMail
{
    public function __construct()
    {
        $this->settings = \Drupal::getContainer()->get('settings');
    }
    
    /**
    * Sends an e-mail message.
    *
    * @param array $message
    *   A message array, as described in hook_mail_alter().
    *
    * @return bool
    *   TRUE if the mail was successfully accepted, otherwise FALSE.
    *
    * @see http://php.net/manual/en/function.mail.php
    * @see drupal_mail()
    */
    public function mail(array $message) {
        // If 'Return-Path' isn't already set in php.ini, we pass it separately
        // as an additional parameter instead of in the header.
        // However, if PHP's 'safe_mode' is on, this is not allowed.
        if (isset($message['headers']['Return-Path']) && !ini_get('safe_mode')) {
          $return_path_set = strpos(ini_get('sendmail_path'), ' -f');
          if (!$return_path_set) {
            $message['Return-Path'] = $message['headers']['Return-Path'];
            unset($message['headers']['Return-Path']);
          }
        }
        $mimeheaders = array();
        foreach ($message['headers'] as $name => $value) {
          $mimeheaders[] = $name . ': ' . mime_header_encode($value);
        }
        $line_endings = settings()->get('mail_line_endings', PHP_EOL);
        // Prepare mail commands.
        $mail_subject = mime_header_encode($message['subject']);
        // Note: e-mail uses CRLF for line-endings. PHP's API requires LF
        // on Unix and CRLF on Windows. Drupal automatically guesses the
        // line-ending format appropriate for your system. If you need to
        // override this, adjust $settings['mail_line_endings'] in settings.php.
        $mail_body = preg_replace('@\r?\n@', $line_endings, $message['body']);
        // For headers, PHP's API suggests that we use CRLF normally,
        // but some MTAs incorrectly replace LF with CRLF. See #234403.
        $mail_headers = join("\n", $mimeheaders);

        $request = \Drupal::request();
        
        $smtp_settings = $this->settings->get('mail_settings');
        
        $pear = new \PEAR();
        
        if (! ($smtp = new \Net_SMTP($smtp_settings['host'], $smtp_settings['port'], $smtp_settings['localhost']))) {
            die("Unable to instantiate Net_SMTP object\n");
        }

        if ($pear->isError($e = $smtp->connect())) {
            die($e->getMessage() . "\n");
        }

        if ($pear->isError($e = $smtp->auth($smtp_settings['username'], $smtp_settings['password']))) {
            die('Could not authenticate');
        }

        if ($pear->isError($smtp->mailFrom($smtp_settings['sendmail_from']))) {
            die('Unable to set sender to <' . TEST_FROM . ">\n");
        }

        if ($pear->isError($res = $smtp->rcptTo($message['to']))) {
            die('Unable to add recipient <' . TEST_TO . '>: ' .
            $res->getMessage() . "\n");
        }

        if ($pear->isError($smtp->data($mail_subject . "\r\n" . $mail_body, $mail_headers))) {
            die("Unable to send data\n");
        }

        $mail_result = $smtp->disconnect();

        // We suppress warnings and notices from mail() because of issues on some
        // hosts. The return value of this method will still indicate whether mail
        // was sent successfully.
//        if (!$request->server->has('WINDIR') && strpos($request->server->get('SERVER_SOFTWARE'), 'Win32') === FALSE) {
//          if (isset($message['Return-Path']) && !ini_get('safe_mode')) {
//            // On most non-Windows systems, the "-f" option to the sendmail command
//            // is used to set the Return-Path. There is no space between -f and
//            // the value of the return path.
//            $mail_result = @mail(
//              $message['to'],
//              $mail_subject,
//              $mail_body,
//              $mail_headers,
//              '-f' . $message['Return-Path']
//            );
//          }
//          else {
//            // The optional $additional_parameters argument to mail() is not
//            // allowed if safe_mode is enabled. Passing any value throws a PHP
//            // warning and makes mail() return FALSE.
//            $mail_result = @mail(
//              $message['to'],
//              $mail_subject,
//              $mail_body,
//              $mail_headers
//            );
//          }
//        }
//        else {
//          // On Windows, PHP will use the value of sendmail_from for the
//          // Return-Path header.
//          $old_from = ini_get('sendmail_from');
//          ini_set('sendmail_from', $message['Return-Path']);
//          $mail_result = @mail(
//            $message['to'],
//            $mail_subject,
//            $mail_body,
//            $mail_headers
//          );
//          ini_set('sendmail_from', $old_from);
//        }

        return $mail_result;
    }
}

?>
