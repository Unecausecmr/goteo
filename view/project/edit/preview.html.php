<?php

use Goteo\Core\View,
    Goteo\Library\Text,
    Goteo\Library\SuperForm;

$project = $this['project'];

// miramos el pruimer paso con errores para mandarlo a ese
$goto = 'view-step-userProfile';
foreach ($this['steps'] as $id => $data) {

    if (empty($step) && !empty($project->errors[$id])) {
        $goto = 'view-step-' . $id;
        break;
    }
}

// boton de revisar que no sirve para mucho
$buttons = array(
    'review' => array(
        'type'  => 'submit',
        'name'  => $goto,
        'label' => Text::get('form-self_review-button'),
        'class' => 'retry'
    )
);

// si es enviable ponemos el boton
if ($project->finishable) {
    $buttons['finish'] = array(
        'type'  => 'submit',
        'name'  => 'finish',
        'label' => Text::get('form-send_review-button'),
        'class' => 'confirm'
    );
}

// elementos generales de preview
$elements      = array(
    'process_preview' => array (
        'type' => 'hidden',
        'value' => 'preview'
    ),

    'preview' => array(
        'type'      => 'html',
        'class'     => 'fullwidth',
        'html'      =>   '<div class="project-preview" style="position: relative"><div>'
                       . '<div class="overlay" style="position: absolute; left: 0; top: 0; right: 0; bottom: 0; z-index: 999"></div>'
                       . '<div style="z-index: 0">'
                       . new View('view/project/widget/support.html.php', array('project' => $project))
                       . new View('view/project/widget/collaborations.html.php', array('project' => $project))
                       . new View('view/project/widget/rewards.html.php', array('project' => $project))
                       . new View('view/user/widget/user.html.php', array('user' => $project->user))
                       . new View('view/project/widget/media.html.php', array('project' => $project))
                       . new View('view/project/widget/share.html.php', array('project' => $project))
                       . new View('view/project/widget/summary.html.php', array('project' => $project))
                       . new View('view/project/widget/needs.html.php', array('project' => $project))
                       . '</div>'
                       . '</div></div>'
    )
);

// si es enviable ponemos el campo de comentario
if ($project->finishable) {
    $elements['comment'] = array(
            'type'  =>'textarea',
            'title' => Text::get('preview-send-comment'),
            'rows'  => 8,
            'cols'  => 100,
            'hint'  => Text::get('tooltip-project-comment'),
            'value' => $project->comment
        );
}

// lanzamos el superform
echo new SuperForm(array(
    'action'        => '',
    'level'         => $this['level'],
    'method'        => 'post',
    'title'         => Text::get('preview-main-header'),
    'hint'          => Text::get('guide-project-preview'),    
    'footer'        => $buttons,
    'elements'      => $elements
));