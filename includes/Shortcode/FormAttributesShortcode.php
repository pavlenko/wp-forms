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

class FormAttributesShortcode
{
    /**
     * TextareaShortcode constructor.
     */
    public function __construct()
    {
        add_shortcode('form_attributes', $this);
    }

    /**
     * @param array  $params
     * @param string $content
     */
    public function __invoke( $params, $content)
    {
        $params = (array) $params;

        $params = shortcode_atts([
            'id'    => '',
            'class' => '',
            'theme' => null,
        ], $params);

        if ($form = Forms()->getFactory()->form) {
            $form->options['attr'] = array_replace(
                (array) $form->options['attr'],
                array_filter($params)
            );

            if (!empty($params['theme'])) {
                $form->theme = (string) $params['theme'];
            }
        }
    }
}