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
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class TextareaShortcode extends BaseShortcode
{
    public function __construct()
    {
        add_shortcode('form-textarea', $this);
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
         * @var string $required
         */
        extract(
            shortcode_atts([
                'name'     => '',
                'label'    => null,
                'required' => false,
            ], $params),
            EXTR_OVERWRITE
        );

        unset($params['name'], $params['label'], $params['required']);

        $factory = Forms()->getFactory();

        // Save reference to parent field
        $parent = $factory->form;

        // Add field and set it as parent for allow add children
        $parent->children[$name] = $factory->form = new FormModel($name, TextareaType::class, [
            'label'    => in_array($label, ['false', '0'], false) ? false : $label,
            'required' => $this->parseBoolean($required),
            'attr'     => $params,
        ]);

        // Execute inner shortcodes
        do_shortcode($content);

        // Return reference to parent
        $factory->form = $parent;
    }
}