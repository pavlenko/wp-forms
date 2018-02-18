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

class TextareaShortcode
{
    /**
     * TextareaShortcode constructor.
     */
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

        $params = shortcode_atts([
            'name'        => '',
            'label'       => null,
            'placeholder' => null,
        ], $params);

        $field_attr = [];

        if (!empty($params['placeholder'])) {
            $field_attr['placeholder'] = $params['placeholder'];
        }

        $params['label'] = in_array($params['label'], ['false', '0'], false) ? false : $params['label'];

        if ($form = Forms()->getFactory()->form) {
            $form->children[$params['name']] = new FormModel($params['name'], TextareaType::class, [
                'label' => $params['label'],
                'attr'  => $field_attr,
            ]);
        }
    }
}