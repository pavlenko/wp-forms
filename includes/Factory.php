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

use SunNYCT\WP\Forms\Model\FormModel;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormFactoryInterface;

class Factory
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var FormModel
     */
    public $form;

    /**
     * Factory constructor.
     *
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    /**
     * @param int|\WP_Post $post
     *
     * @return bool|FormModel
     */
    public function create($post)
    {
        if (is_numeric($post) && $post > 0) {
            $post = get_post($post);
        }

        if (!($post instanceof \WP_Post)) {
            return false;
        }

        $this->form = new FormModel();

        do_shortcode($post->post_content);

        if (!count($this->form->fields)) {
            return false;
        }

        $this->form->options['attr'] = array_merge(
            (array) $this->form->options['attr'],
            ['data-role' => SUNNYCT_WP_FORMS_PLUGIN_NAME, 'novalidate' => 'novalidate']
        );

        $builder = $this->formFactory->createNamedBuilder(null, FormType::class, null, $this->form->options);

        $builder->setMethod('POST');
        $builder->setAction(admin_url('admin-ajax.php'));

        $builder->add('id', HiddenType::class, ['data' => $post->ID]);
        $builder->add('action', HiddenType::class, ['data' => SUNNYCT_WP_FORMS_PLUGIN_NAME]);

        foreach ($this->form->fields as $field) {
            $builder->add($field->name, $field->type, $field->options);
        }

        wp_enqueue_script(SUNNYCT_WP_FORMS_PLUGIN_NAME);

        $this->form->instance = $builder->getForm();

        return $this->form;
    }
}