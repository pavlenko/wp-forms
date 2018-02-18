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

use PE\WP\Forms\Action\AutoHideAction;
use PE\WP\Forms\Action\MailAction;
use PE\WP\Forms\Action\RedirectAction;
use PE\WP\Forms\Action\SuccessMessageAction;

class ActionShortcode
{
    /**
     * ActionShortcode constructor.
     */
    public function __construct()
    {
        add_shortcode('form-action', $this);
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