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

class FormShortcode
{
    public function __construct()
    {
        add_shortcode('form', $this);
    }

    /**
     * @param array  $params
     * @param string $content
     */
    public function __invoke($params, $content = null)
    {
        /**
         * @var string $theme
         */
        extract(
            shortcode_atts(['theme' => null], (array) $params),
            EXTR_OVERWRITE
        );

        unset($params['theme']);

        if ($form = Forms()->getFactory()->form) {
            $form->options['attr'] = array_replace(
                (array) $form->options['attr'],
                array_filter($params)
            );

            if ($theme) {
                $form->theme = $theme;
            }

            do_shortcode($content);
        }
    }
}