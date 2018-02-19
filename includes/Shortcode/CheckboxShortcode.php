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

use PE\WP\Forms\Model\FormModel;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class CheckboxShortcode extends BaseShortcode
{
    /**
     * CheckboxShortcode constructor.
     */
    public function __construct()
    {
        add_shortcode('form-checkbox', $this);
    }

    /**
     * @param array  $params
     * @param string $content
     */
    public function __invoke($params, $content)
    {
        $params = (array) $params;

        /**
         * @var string $name
         * @var string $label
         * @var bool   $required
         */
        extract(
            shortcode_atts([
                'name'     => '',
                'label'    => '',
                'required' => null,
            ], $params),
            EXTR_OVERWRITE
        );

        unset($params['name'], $params['label'], $params['required']);

        if ($form = Forms()->getFactory()->form) {
            $form->children[$name] = new FormModel($name, CheckboxType::class, [
                'label'    => $label,
                'required' => $this->parseBoolean($required),
                'attr'     => $params,
            ]);
        }
    }
}