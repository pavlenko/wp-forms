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
        add_shortcode('form-text', $this);
    }

    /**
     * @param array  $params
     * @param string $content
     */
    public function __invoke($params, $content = null)
    {
        $params = shortcode_atts([
            'name'        => '',
            'type'        => 'text',
            'label'       => null,
            'placeholder' => null,
        ], (array) $params);

        /**
         * @var string $name
         * @var string $type
         * @var string $label
         */
        extract($params, EXTR_OVERWRITE);

        unset($params['name'], $params['type'], $params['label']);

        switch ($type) {
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

        $factory = Forms()->getFactory();

        // Save reference to parent field
        $parent = $factory->form;

        // Add field and set it as parent for allow add children
        $parent->children[$name] = $factory->form = new FormModel($name, $type, [
            'label' => in_array($label, ['false', '0'], false) ? false : $label,
            'attr'  => $params,
        ]);

        // Execute inner shortcodes
        do_shortcode($content);

        // Return reference to parent
        $factory->form = $parent;
    }
}