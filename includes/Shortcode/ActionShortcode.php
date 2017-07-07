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

use SunNYCT\WP\Forms\Action\AutoHideAction;
use SunNYCT\WP\Forms\Action\MailAction;
use SunNYCT\WP\Forms\Action\RedirectAction;
use SunNYCT\WP\Forms\Action\SuccessMessageAction;

class ActionShortcode
{
    /**
     * ActionShortcode constructor.
     */
    public function __construct()
    {
        add_shortcode('action', $this);
    }

    /**
     * @param array  $params
     * @param string $content
     */
    public function __invoke($params, $content = null)
    {
        $params = (array) $params;

        // Save content to params for future use
        $params['content'] = trim($content);

        /* @var $type string */
        extract(shortcode_atts([
            'type' => '',
        ], $params), EXTR_OVERWRITE);

        // Collect possible actions
        $types = apply_filters(SUNNYCT_WP_FORMS_PLUGIN_NAME . '-action-types', [
            'mail'            => MailAction::class,
            'success_message' => SuccessMessageAction::class,
            'redirect'        => RedirectAction::class,
            'auto_hide'       => AutoHideAction::class,
        ]);

        if (!array_key_exists($type, $types)) {
            return;
        }

        if ($form = Forms()->getFactory()->form) {
            $form->actions[] = new $types[$type]($params);
        }
    }
}