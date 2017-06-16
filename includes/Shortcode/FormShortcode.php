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

class FormShortcode
{
    /**
     * FormShortcode constructor.
     */
    public function __construct()
    {
        add_shortcode('sunnyct-wp-forms', $this);
    }

    /**
     * @param array $params
     *
     * @return bool|string
     */
    public function __invoke(array $params)
    {
        $params = shortcode_atts([
            'id' => null,
        ], $params);

        if (empty($params['id'])) {
            return false;
        }

        if ($formModel = Forms()->getFactory()->create($params['id'])) {
            return Forms()->render($formModel->instance);
        };

        return false;
    }
}