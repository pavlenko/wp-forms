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

namespace PE\WP\Forms\Action;


use Symfony\Component\Form\FormInterface;

class AutoHideAction extends Action
{
    /**
     * @var int
     */
    protected $delay;

    /**
     * @inheritdoc
     */
    public function execute(FormInterface $form)
    {
        $id = uniqid(SUNNYCT_WP_FORMS_PLUGIN_NAME, false);

        ?>
        <div id="<?php echo $id ?>"></div>
        <script>
            setTimeout(function(){
                var $target = $('#<?php echo $id ?>');

                while (!$target.is('html')) {
                    if ($target.data('bs.modal')) {
                        $target.modal('hide');
                        break;
                    }

                    $target = $target.parent();
                }
            }, <?php echo max(abs($this->delay), 1) * 1000 ?>)
        </script>
        <?php
    }
}