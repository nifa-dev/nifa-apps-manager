<?php
$this->extend('../../Layout/TwitterBootstrap/dashboard');

$this->start('tb_actions');
?>
    <li><?=
    $this->Form->postLink(
        __('Delete'),
        ['action' => 'delete', $application->id],
        ['confirm' => __('Are you sure you want to delete # {0}?', $application->id)]
    )
    ?>
    </li>
    <li><?= $this->Html->link(__('List Applications'), ['action' => 'index']) ?></li>
<?php
$this->end();

$this->start('tb_sidebar');
?>
<ul class="nav nav-sidebar">
    <li><?=
    $this->Form->postLink(
        __('Delete'),
        ['action' => 'delete', $application->id],
        ['confirm' => __('Are you sure you want to delete # {0}?', $application->id)]
    )
    ?>
    </li>
    <li><?= $this->Html->link(__('List Applications'), ['action' => 'index']) ?></li>
</ul>
<?php
$this->end();
?>
<?= $this->Form->create($application); ?>
<fieldset>
    <legend><?= __('Edit {0}', ['Application']) ?></legend>
    <?php
    echo $this->Form->input('name');

    echo $this->Form->input('inactive');
    ?>
</fieldset>
<?= $this->Form->button(__("Save")); ?>
<?= $this->Form->end() ?>
