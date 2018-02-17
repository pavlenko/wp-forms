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
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class TextShortcode
{
    /**
     * TextareaShortcode constructor.
     */
    public function __construct()
    {
        add_shortcode('text', $this);
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
            'type'        => 'text',
            'label'       => null,
            'placeholder' => null,
        ], $params);

        $field_attr = [];

        if (!empty($params['placeholder'])) {
            $field_attr['placeholder'] = $params['placeholder'];
        }

        switch ($params['type']) {
            case 'email':
                $type = EmailType::class;
                break;
            case 'tel':
                $type = TelType::class;
                break;
            case 'password':
                $type = PasswordType::class;
                break;
            case 'search':
                $type = SearchType::class;
                break;
            default:
                $type = TextType::class;
        }

        $params['label'] = in_array($params['label'], ['false', '0'], false) ? false : $params['label'];

        if ($form = Forms()->getFactory()->form) {
            $form->children[$params['name']] = new FormModel($params['name'], $type, [
                'label' => $params['label'],
                'attr'  => $field_attr,
            ]);
        }
    }
}