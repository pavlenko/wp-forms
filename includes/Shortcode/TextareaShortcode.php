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

namespace SunNYCT\WP\Forms\Shortcode;

use SunNYCT\WP\Forms\Model\FieldModel;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class TextareaShortcode
{
    /**
     * TextareaShortcode constructor.
     */
    public function __construct()
    {
        add_shortcode('textarea', $this);
    }

    /**
     * @param array  $params
     * @param string $content
     */
    public function __invoke($params, $content)
    {
        $params = (array) $params;

        $params = shortcode_atts([
            'name'        => '',
            'label'       => '',
            'placeholder' => '',
        ], $params);

        $field_attr = [];

        if (!empty($params['placeholder'])) {
            $field_attr['placeholder'] = $params['placeholder'];
        }

        if ($form = Forms()->getFactory()->form) {
            $form->fields[$params['name']] = new FieldModel($params['name'], TextareaType::class, [
                'label' => $params['label'],
                'attr' => $field_attr,
            ]);
        }
    }
}