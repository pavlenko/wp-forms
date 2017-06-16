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

class RedirectAction extends Action
{
    /**
     * @var string
     */
    protected $url;

    /**
     * @var int
     */
    protected $permanent;

    /**
     * @inheritdoc
     */
    public function execute(FormInterface $form)
    {
        wp_redirect($this->url, $this->permanent ? 301 : 302);
    }
}