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
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
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
    public function __invoke(array $params, $content)
    {
        $params = shortcode_atts([
            'name'        => '',
            'type'        => 'text',
            'label'       => '',
            'placeholder' => '',
        ], $params);

        $field_attr = [];

        if (!empty($params['placeholder'])) {
            $field_attr['placeholder'] = $params['placeholder'];
        }

        switch ($params['type']) {
            case 'email':
                $type = EmailType::class;
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

        if ($form = Forms()->getFactory()->form) {
            $form->fields[$params['name']] = new FieldModel($params['name'], $type, [
                'label' => $params['label'],
                'attr' => $field_attr,
            ]);
        }
    }
}