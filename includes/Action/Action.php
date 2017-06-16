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

abstract class Action
{
    /**
     * Action content, given from shortcode
     *
     * @var string
     */
    protected $content;

    /**
     * Action constructor.
     *
     * @param array $options
     */
    public function __construct(array $options)
    {
        $known = get_object_vars($this);

        foreach ($options as $name => $value) {
            if (array_key_exists($name, $known)) {
                $this->{$name} = $value;
            }
        }
    }

    /**
     * Execute action
     *
     * @param FormInterface $form
     */
    abstract public function execute(FormInterface $form);
}