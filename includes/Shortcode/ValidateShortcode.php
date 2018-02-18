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

namespace PE\WP\Forms\Shortcode;

use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ValidateShortcode
{
    /**
     * ValidateShortcode constructor.
     */
    public function __construct()
    {
        add_shortcode('form-validate', $this);
    }

    /**
     * @param array $params
     */
    public function __invoke($params)
    {
        $params = (array) $params;

        /**
         * @var $type string
         */
        extract(shortcode_atts([
            'type' => '',
        ], $params), EXTR_OVERWRITE);

        unset($params['type']);

        switch ($type) {
            case 'length':
                $constraint = new Length($params);
                break;
            case 'required':
                $constraint = new NotBlank($params);
                break;
            case 'email':
                $constraint = new Email($params);
                break;
            default:
                $constraint = null;
        }

        if ($constraint && ($form = Forms()->getFactory()->form)) {
            $form->options['constraints'][] = $constraint;
        }
    }
}