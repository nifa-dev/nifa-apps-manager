<?php
$this->extend('../../Layout/TwitterBootstrap/dashboard');


$this->start('tb_actions');
?>
<li><?= $this->Html->link(__('Edit Application'), ['action' => 'edit', $application->id]) ?> </li>
<li><?= $this->Form->postLink(__('Delete Application'), ['action' => 'delete', $application->id], ['confirm' => __('Are you sure you want to delete # {0}?', $application->id)]) ?> </li>
<li><?= $this->Html->link(__('List Applications'), ['action' => 'index']) ?> </li>
<li><?= $this->Html->link(__('New Application'), ['action' => 'add']) ?> </li>
<?php
$this->end();

$this->start('tb_sidebar');
?>
<ul class="nav nav-sidebar">
<li><?= $this->Html->link(__('Edit Application'), ['action' => 'edit', $application->id]) ?> </li>
<li><?= $this->Form->postLink(__('Delete Application'), ['action' => 'delete', $application->id], ['confirm' => __('Are you sure you want to delete # {0}?', $application->id)]) ?> </li>
<li><?= $this->Html->link(__('List Applications'), ['action' => 'index']) ?> </li>
<li><?= $this->Html->link(__('New Application'), ['action' => 'add']) ?> </li>
</ul>
<?php
$this->end();
?>
<div class="panel panel-default">
    <!-- Panel header -->
    <div class="panel-heading">
        <h3 class="panel-title"><?= h($application->name) ?></h3>
    </div>
    <table class="table table-striped" cellpadding="0" cellspacing="0">
        <tr>
            <td><?= __('Name') ?></td>
            <td><?= h($application->name) ?></td>
        </tr>
        <tr>
            <td><?= __('Public Key') ?></td>
            <td><?= h($application->public_key) ?></td>
        </tr>
        <tr>
            <td><?= __('Secret Key') ?></td>
            <td><?= h($application->secret_key) ?></td>
        </tr>
        <tr>
            <td><?= __('Id') ?></td>
            <td><?= $this->Number->format($application->id) ?></td>
        </tr>
        <tr>
            <td><?= __('Created') ?></td>
            <td><?= h($application->created) ?></td>
        </tr>
        <tr>
            <td><?= __('Modified') ?></td>
            <td><?= h($application->modified) ?></td>
        </tr>
        <tr>
            <td><?= __('Inactive') ?></td>
            <td><?= $application->inactive ? __('Yes') : __('No'); ?></td>
        </tr>
    </table>
</div>

