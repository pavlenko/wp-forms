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

namespace SunNYCT\WP\Forms;

class AJAXHandler
{
    public function __construct()
    {
        add_action('wp_ajax_nopriv_' . SUNNYCT_WP_FORMS_PLUGIN_NAME, $this);
        add_action('wp_ajax_' . SUNNYCT_WP_FORMS_PLUGIN_NAME, $this);
    }

    /**
     * Call to be executed on AJAX request.
     */
    public function __invoke()
    {
        try {
            if (!($formModel = Forms()->getFactory()->create($_POST['id']))) {
                throw new \InvalidArgumentException('Form does not contain fields');
            }

            $formModel->instance->handleRequest();

            if ($formModel->instance->isValid()) {
                foreach ($formModel->actions as $action) {
                    $action->execute($formModel->instance);
                }
            } else {
                echo Forms()->render($formModel->instance);
                http_response_code(400);
            }

            die();
        } catch (\InvalidArgumentException $exception) {
            echo json_encode(['errors' => [$exception->getMessage()]]);
            http_response_code(400);
            die();
        }
    }
}