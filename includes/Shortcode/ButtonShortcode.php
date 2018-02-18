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
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ButtonShortcode
{
    /**
     * ButtonShortcode constructor.
     */
    public function __construct()
    {
        add_shortcode('form-button', $this);
    }

    /**
     * @param array  $params
     * @param string $content
     */
    public function __invoke($params, $content)
    {
        $params = (array) $params;

        $params = shortcode_atts([
            'name'  => '',
            'type'  => '',
            'label' => '',
            'class' => null,
        ], $params);

        switch ($params['type']) {
            case 'submit':
                $type = SubmitType::class;
                break;
            case 'reset':
                $type = ResetType::class;
                break;
            default:
                $type = ButtonType::class;
        }

        if ($form = Forms()->getFactory()->form) {
            $form->children[$params['name']] = new FormModel($params['name'], $type, [
                'label' => $params['label'] ?: $content,
                'attr'  => ['class' => $params['class']]
            ]);
        }
    }
}