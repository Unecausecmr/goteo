<?php
use Goteo\Core\View,
    Goteo\Library\Text;

if ($this['action'] == 'list') : ?>
    <a class="button" href="/dashboard/projects/commons/add">Retorno adicional</a>
    <br /><br />
    <div class="widget projects">
        <h2 class="widget-title title"><?php echo Text::get('rewards-fields-social_reward-title'); ?></h2>
        <?php echo new View('view/project/edit/rewards/view_commons.html.php', $this); ?>
    </div>
    <?php echo new View('view/project/edit/rewards/commons.js.php'); ?>
<?php else: ?>
    <a class="button" href="/dashboard/projects/commons">Volver (sin guardar)</a>
    <br /><br />
    <div class="widget projects">
        <h2 class="widget-title title"><?php echo Text::get('rewards-fields-social_reward-title'); ?></h2>
        <?php echo new View('view/project/edit/rewards/edit_commons.html.php', $this); ?>
    </div>
<?php endif; ?>