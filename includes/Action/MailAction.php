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

namespace SunNYCT\WP\Forms\Action;

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

        add_shortcode('data', function(array $params) use ($data) {
            if (array_key_exists('key', $params) && array_key_exists($params['key'], $data)) {
                if (is_array($data[$params['key']])) {
                    $data[$params['key']] = implode(', ', $data[$params['key']]);
                }

                return $data[$params['key']];
            }

            return false;
        });
        //TODO if from, to or subject is missing try to get it from form data
        /*$this->from    = do_shortcode($this->from);
        $this->to      = do_shortcode($this->to);
        $this->subject = do_shortcode($this->subject);*/

        $message = do_shortcode($this->content);

        remove_shortcode('data');

        wp_mail($this->to, $this->subject, $message, [
            'From: ' . $this->from,
            'Content-Type: text/html; charset=UTF-8',
        ]);
    }
}