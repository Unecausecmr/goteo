<?php
use Goteo\Core\View,
    Goteo\Library\Text;

$promotes = $vars['promotes'];
?>
<div id="node-projects-promote" class="content_widget node-projects rounded-corners" <?php if ($vars['hide_promotes']) : ?>style="display:none;"<?php endif; ?>>

    <h2><?php echo Text::get('node-side-searcher-promote'); ?>
    <span class="line"></span>
    </h2>

    <ul>
        <?php foreach ($promotes as $promo) {
            $project = $promo->projectData;
            $project->per_amount = round(($project->amount / $project->mincost) * 100);
            echo View::get('project/widget/tiny_project.html.php', array('project'=>$project));
        }?>
    </ul>

    <div class="see_more"><a href="/discover"><?php echo Text::get('regular-see_more') ?></a></div>
</div>