<?php
/**
 * SunNY Creative Technologies
 *
 *   #####                                ##     ##    ##      ##
 * ##     ##                              ###    ##    ##      ##
 * ##                                     ####   ##     ##    ##
 * ##           ##     ##    ## #####     ## ##  ##      ##  ##
 *   #####      ##     ##    ###    ##    ##  ## ##       ####
 *        ##    ##     ##    ##     ##    ##   ####        ##
 *        ##    ##     ##    ##     ##    ##    ###        ##
 * ##     ##    ##     ##    ##     ##    ##     ##        ##
 *   #####        #######    ##     ##    ##     ##        ##
 *
 * C  R  E  A  T  I  V  E     T  E  C  H  N  O  L  O  G  I  E  S
 */

namespace PE\WP\Forms\Action;

use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormInterface;

class MailAction extends Action
{
    /**
     * @var string
     */
    protected $from;

    /**
     * @var string
     */
    protected $to;

    /**
     * @var string;
     */
    protected $subject;

    /**
     * @inheritdoc
     */
    public function execute(FormInterface $form)
    {
        $data = $form->getData();

        $this->findFiles($form, $files);

        add_shortcode('data', function(array $params) use ($data) {
            if (array_key_exists('key', $params) && array_key_exists($params['key'], $data)) {
                if (is_array($data[$params['key']])) {
                    $data[$params['key']] = implode(', ', $data[$params['key']]);
                }

                if (is_bool($data[$params['key']])) {
                    $true = isset($params['true_value'])
                        ? $params['true_value']
                        : __('Yes');

                    $false = isset($params['false_value'])
                        ? $params['false_value']
                        : __('No');

                    $data[$params['key']] = $data[$params['key']]
                        ? $true
                        : $false;
                }

                return $data[$params['key']];
            }

            return false;
        });

        $message = do_shortcode($this->content);

        remove_shortcode('data');

        $attachments = [];
        foreach ($files as $file) {
            $attachments[] = [$file['tmp_name'], $file['name'], $file['type']];
        }

        $this->wp_mail($this->to, $this->subject, $message, [
            'From: ' . $this->from,
            'Content-Type: text/html; charset=UTF-8',
        ], $attachments);
    }

    /**
     * @param FormInterface $form
     * @param array $files
     */
    private function findFiles(FormInterface $form, &$files = [])
    {
        if ($form->count()) {
            foreach ($form as $child) {
                $this->findFiles($child, $files);
            }
        } else if ($form->getConfig()->getType()->getInnerType() instanceof FileType) {
            if ($form->getConfig()->getOption('multiple')) {
                foreach ($form->getData() as $key => $data) {
                    $files[$form->getConfig()->getPropertyPath() . '_' . $key] = $data;
                }
            } else {
                $files[$form->getConfig()->getPropertyPath()] = $form->getData();
            }
        }
    }

    /**
     * THIS IS COPY FROM WP CORE FOR ALLOW SET NAMES TO ATTACHMENTS
     *
     * Send mail, similar to PHP's mail
     *
     * A true return value does not automatically mean that the user received the
     * email successfully. It just only means that the method used was able to
     * process the request without any errors.
     *
     * Using the two 'wp_mail_from' and 'wp_mail_from_name' hooks allow from
     * creating a from address like 'Name <email@address.com>' when both are set. If
     * just 'wp_mail_from' is set, then just the email address will be used with no
     * name.
     *
     * The default content type is 'text/plain' which does not allow using HTML.
     * However, you can set the content type of the email by using the
     * {@see 'wp_mail_content_type'} filter.
     *
     * The default charset is based on the charset used on the blog. The charset can
     * be set using the {@see 'wp_mail_charset'} filter.
     *
     * @since 1.2.1
     *
     * @global \PHPMailer $phpmailer
     *
     * @param string|array $to          Array or comma-separated list of email addresses to send message.
     * @param string       $subject     Email subject
     * @param string       $message     Message contents
     * @param string|array $headers     Optional. Additional headers.
     * @param string|array $attachments Optional. Files to attach.
     * @return bool Whether the email contents were sent successfully.
     */
    private function wp_mail($to, $subject, $message, $headers = '', $attachments = array())
    {
        // Compact the input, apply the filters, and extract them back out

        /**
         * Filters the wp_mail() arguments.
         *
         * @since 2.2.0
         *
         * @param array $args A compacted array of wp_mail() arguments, including the "to" email,
         *                    subject, message, headers, and attachments values.
         */
        $atts = apply_filters('wp_mail', compact('to', 'subject', 'message', 'headers', 'attachments'));

        if (isset($atts['to'])) {
            $to = $atts['to'];
        }

        if (!is_array($to)) {
            $to = explode(',', $to);
        }

        if (isset($atts['subject'])) {
            $subject = $atts['subject'];
        }

        if (isset($atts['message'])) {
            $message = $atts['message'];
        }

        if (isset($atts['headers'])) {
            $headers = $atts['headers'];
        }

        if (isset($atts['attachments'])) {
            $attachments = $atts['attachments'];
        }

        if (!is_array($attachments)) {
            $attachments = explode("\n", str_replace("\r\n", "\n", $attachments));
        }

        global $phpmailer;

        // (Re)create it, if it's gone missing
        if (!($phpmailer instanceof \PHPMailer)) {
            require_once ABSPATH . WPINC . '/class-phpmailer.php';
            require_once ABSPATH . WPINC . '/class-smtp.php';
            $phpmailer = new \PHPMailer(true);
        }

        // Headers
        $cc = $bcc = $reply_to = array();

        if (empty($headers)) {
            $headers = array();
        } else {
            if (!is_array($headers)) {
                // Explode the headers out, so this function can take both
                // string headers and an array of headers.
                $tempheaders = explode("\n", str_replace("\r\n", "\n", $headers));
            } else {
                $tempheaders = $headers;
            }

            $headers = array();

            // If it's actually got contents
            if (!empty($tempheaders)) {
                // Iterate through the raw headers
                foreach ((array)$tempheaders as $header) {
                    if (strpos($header, ':') === false) {
                        if (false !== stripos($header, 'boundary=')) {
                            $parts = preg_split('/boundary=/i', trim($header));
                            $boundary = trim(str_replace(array("'", '"'), '', $parts[1]));
                        }
                        continue;
                    }
                    // Explode them out
                    list($name, $content) = explode(':', trim($header), 2);

                    // Cleanup crew
                    $name = trim($name);
                    $content = trim($content);

                    switch (strtolower($name)) {
                        // Mainly for legacy -- process a From: header if it's there
                        case 'from':
                            $bracket_pos = strpos($content, '<');
                            if ($bracket_pos !== false) {
                                // Text before the bracketed email is the "From" name.
                                if ($bracket_pos > 0) {
                                    $from_name = substr($content, 0, $bracket_pos - 1);
                                    $from_name = str_replace('"', '', $from_name);
                                    $from_name = trim($from_name);
                                }

                                $from_email = substr($content, $bracket_pos + 1);
                                $from_email = str_replace('>', '', $from_email);
                                $from_email = trim($from_email);

                                // Avoid setting an empty $from_email.
                            } elseif ('' !== trim($content)) {
                                $from_email = trim($content);
                            }
                            break;
                        case 'content-type':
                            if (strpos($content, ';') !== false) {
                                list($type, $charset_content) = explode(';', $content);
                                $content_type = trim($type);
                                if (false !== stripos($charset_content, 'charset=')) {
                                    $charset = trim(str_replace(array('charset=', '"'), '', $charset_content));
                                } elseif (false !== stripos($charset_content, 'boundary=')) {
                                    $boundary = trim(str_replace(array('BOUNDARY=', 'boundary=', '"'), '', $charset_content));
                                    $charset = '';
                                }

                                // Avoid setting an empty $content_type.
                            } elseif ('' !== trim($content)) {
                                $content_type = trim($content);
                            }
                            break;
                        case 'cc':
                            $cc = array_merge((array)$cc, explode(',', $content));
                            break;
                        case 'bcc':
                            $bcc = array_merge((array)$bcc, explode(',', $content));
                            break;
                        case 'reply-to':
                            $reply_to = array_merge((array)$reply_to, explode(',', $content));
                            break;
                        default:
                            // Add it to our grand headers array
                            $headers[trim($name)] = trim($content);
                            break;
                    }
                }
            }
        }

        // Empty out the values that may be set
        $phpmailer->clearAllRecipients();
        $phpmailer->clearAttachments();
        $phpmailer->clearCustomHeaders();
        $phpmailer->clearReplyTos();

        // From email and name
        // If we don't have a name from the input headers
        if (!isset($from_name)) {
            $from_name = 'WordPress';
        }

        /* If we don't have an email from the input headers default to wordpress@$sitename
         * Some hosts will block outgoing mail from this address if it doesn't exist but
         * there's no easy alternative. Defaulting to admin_email might appear to be another
         * option but some hosts may refuse to relay mail from an unknown domain. See
         * https://core.trac.wordpress.org/ticket/5007.
         */

        if (!isset($from_email)) {
            // Get the site domain and get rid of www.
            $sitename = strtolower($_SERVER['SERVER_NAME']);
            if (substr($sitename, 0, 4) == 'www.') {
                $sitename = substr($sitename, 4);
            }

            $from_email = 'wordpress@' . $sitename;
        }

        /**
         * Filters the email address to send from.
         *
         * @since 2.2.0
         *
         * @param string $from_email Email address to send from.
         */
        $from_email = apply_filters('wp_mail_from', $from_email);

        /**
         * Filters the name to associate with the "from" email address.
         *
         * @since 2.3.0
         *
         * @param string $from_name Name associated with the "from" email address.
         */
        $from_name = apply_filters('wp_mail_from_name', $from_name);

        try {
            $phpmailer->setFrom($from_email, $from_name, false);
        } catch (\phpmailerException $e) {
            $mail_error_data = compact('to', 'subject', 'message', 'headers', 'attachments');
            $mail_error_data['phpmailer_exception_code'] = $e->getCode();

            /** This filter is documented in wp-includes/pluggable.php */
            do_action('wp_mail_failed', new \WP_Error('wp_mail_failed', $e->getMessage(), $mail_error_data));

            return false;
        }

        // Set mail's subject and body
        $phpmailer->Subject = $subject;
        $phpmailer->Body = $message;

        // Set destination addresses, using appropriate methods for handling addresses
        $address_headers = compact('to', 'cc', 'bcc', 'reply_to');

        foreach ($address_headers as $address_header => $addresses) {
            if (empty($addresses)) {
                continue;
            }

            foreach ((array)$addresses as $address) {
                try {
                    // Break $recipient into name and address parts if in the format "Foo <bar@baz.com>"
                    $recipient_name = '';

                    if (preg_match('/(.*)<(.+)>/', $address, $matches)) {
                        if (count($matches) == 3) {
                            $recipient_name = $matches[1];
                            $address = $matches[2];
                        }
                    }

                    switch ($address_header) {
                        case 'to':
                            $phpmailer->addAddress($address, $recipient_name);
                            break;
                        case 'cc':
                            $phpmailer->addCc($address, $recipient_name);
                            break;
                        case 'bcc':
                            $phpmailer->addBcc($address, $recipient_name);
                            break;
                        case 'reply_to':
                            $phpmailer->addReplyTo($address, $recipient_name);
                            break;
                    }
                } catch (\phpmailerException $e) {
                    continue;
                }
            }
        }

        // Set to use PHP's mail()
        $phpmailer->isMail();

        // Set Content-Type and charset
        // If we don't have a content-type from the input headers
        if (!isset($content_type)) {
            $content_type = 'text/plain';
        }

        /**
         * Filters the wp_mail() content type.
         *
         * @since 2.3.0
         *
         * @param string $content_type Default wp_mail() content type.
         */
        $content_type = apply_filters('wp_mail_content_type', $content_type);

        $phpmailer->ContentType = $content_type;

        // Set whether it's plaintext, depending on $content_type
        if ('text/html' == $content_type) {
            $phpmailer->isHTML(true);
        }

        // If we don't have a charset from the input headers
        if (!isset($charset)) {
            $charset = get_bloginfo('charset');
        }

        // Set the content-type and charset

        /**
         * Filters the default wp_mail() charset.
         *
         * @since 2.3.0
         *
         * @param string $charset Default email charset.
         */
        $phpmailer->CharSet = apply_filters('wp_mail_charset', $charset);

        // Set custom headers
        if (!empty($headers)) {
            foreach ((array)$headers as $name => $content) {
                $phpmailer->addCustomHeader(sprintf('%1$s: %2$s', $name, $content));
            }

            if (false !== stripos($content_type, 'multipart') && !empty($boundary)) {
                $phpmailer->addCustomHeader(sprintf("Content-Type: %s;\n\t boundary=\"%s\"", $content_type, $boundary));
            }
        }

        if (!empty($attachments)) {
            foreach ($attachments as $attachment) {
                try {
                    //START CUSTOM
                    if (is_array($attachment)) {
                        list($path, $name, $type) = array_pad($attachment, 3, '');
                    } else {
                        $path = $attachment;
                        $name = '';
                        $type = '';
                    }
                    //END CUSTOM

                    $phpmailer->addAttachment($path, $name, 'base64', $type);
                } catch (\phpmailerException $e) {dump($e);
                    continue;
                }
            }
        }

        /**
         * Fires after PHPMailer is initialized.
         *
         * @since 2.2.0
         *
         * @param \PHPMailer $phpmailer The PHPMailer instance (passed by reference).
         */
        do_action_ref_array('phpmailer_init', array(&$phpmailer));

        // Send!
        try {
            return $phpmailer->send();
        } catch (\phpmailerException $e) {
            $mail_error_data = compact('to', 'subject', 'message', 'headers', 'attachments');
            $mail_error_data['phpmailer_exception_code'] = $e->getCode();

            /**
             * Fires after a phpmailerException is caught.
             *
             * @since 4.4.0
             *
             * @param \WP_Error $error A WP_Error object with the phpmailerException message, and an array
             *                         containing the mail recipient, subject, message, headers, and attachments.
             */
            do_action('wp_mail_failed', new \WP_Error('wp_mail_failed', $e->getMessage(), $mail_error_data));

            return false;
        }
    }
}