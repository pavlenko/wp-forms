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
use SunNYCT\WP\Forms\Recaptcha\ReCaptchaConstraint;
use SunNYCT\WP\Forms\Recaptcha\ReCaptchaType;

class ReCaptchaShortcode
{
    /**
     * ReCaptchaShortcode constructor.
     */
    public function __construct()
    {
        add_shortcode('recaptcha', $this);
    }

    /**
     * Render reCaptcha
     *
     * Example
     * [recaptcha site_key="6Lcy7yEUAAAAALm9J1v0jpIsiF0XYnLF6l4hc7j1" secret_key="6Lcy7yEUAAAAAAm3ZUY7yDAA1lQ7VJbDllrHbMxQ"]
     *
     * @param array  $params
     * @param string $content
     */
    public function __invoke($params, $content)
    {
        /**
         * @var string $name
         * @var string $type
         * @var string $size
         * @var string $theme
         * @var string $site_key
         * @var string $secret_key
         */
        extract(shortcode_atts([
            'site_key'   => '',
            'secret_key' => '',
            'type'       => '',
            'size'       => '',
            'theme'      => '',
        ], $params), EXTR_OVERWRITE);

        if (empty($site_key) || empty($secret_key)) {
            return;
        }

        $type  = $type === 'audio' ? 'audio' : 'image';
        $size  = $size === 'compact ' ? 'compact ' : 'normal';
        $theme = $theme === 'dark' ? 'dark' : 'light';

        if ($form = Forms()->getFactory()->form) {
            $form->fields['g-recaptcha-response'] = new FieldModel('g-recaptcha-response', ReCaptchaType::class, [
                'constraints' => array(
                    new ReCaptchaConstraint(['secretKey' => $secret_key])
                ),
                'attr' => [
                    'data-sitekey' => $site_key,
                    'data-type'    => $type,
                    'data-size'    => $size,
                    'data-theme'   => $theme,
                ],
            ]);
        }
    }
}