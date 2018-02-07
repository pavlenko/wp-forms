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
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ChoiceShortcode
{
    /**
     * @var array
     */
    private $choices = [];

    /**
     * TextareaShortcode constructor.
     */
    public function __construct()
    {
        add_shortcode('choice_block', function(array $params, $content){
            $this->block($params, $content);
        });

        add_shortcode('choice_option', function(array $params){
            $this->option($params);
        });
    }

    /**
     * @param array  $params
     * @param string $content
     */
    public function block($params, $content)
    {
        $params = (array) $params;

        $params = shortcode_atts([
            'name'        => '',
            'type'        => 'select',
            'multiple'    => false,
            'label'       => null,
            'placeholder' => null,
        ], $params);

        switch ($params['type']) {
            case 'checkboxes':
                $options = ['expanded' => true, 'multiple' => true];
                break;
            case 'radios':
                $options = ['expanded' => true, 'multiple' => false];
                break;
            default:
                $options = ['expanded' => false, 'multiple' => (bool) $params['multiple']];
        }

        $params['label'] = in_array($params['label'], ['false', '0'], false) ? false : $params['label'];

        if ($params['placeholder']) {
            $options['placeholder'] = (string) $params['placeholder'];
        }

        $this->choices = [];

        do_shortcode($content);

        foreach ($this->choices as $choice) {
            if (!empty($choice['group'])) {
                $options['choices'][$choice['group']][$choice['label']] = $choice['name'];
            } else {
                $options['choices'][$choice['label']] = $choice['name'];
            }
        }

        if ($form = Forms()->getFactory()->form) {
            $form->fields[$params['name']] = new FieldModel($params['name'], ChoiceType::class, $options);
        }
    }

    /**
     * Add choices to field
     *
     * @param array $params
     */
    public function option($params)
    {
        $params = (array) $params;

        $params = shortcode_atts([
            'name'  => '',
            'label' => '',
            'group' => '',
        ], $params);

        if (empty($params['name']) && empty($params['label'])) {
            return;
        }

        if (empty($params['name']) && !empty($params['label'])) {
            $params['name'] = $params['label'];
        }

        if (empty($params['label']) && !empty($params['name'])) {
            $params['label'] = $params['name'];
        }

        $this->choices[] = [
            'name'  => $params['name'],
            'label' => $params['label'],
            'group' => $params['group'],
        ];
    }
}