<?php
/* @var $this \Cake\View\View */
$this->extend('../../Layout/TwitterBootstrap/dashboard');
$this->start('tb_actions');
?>
    <li><?= $this->Html->link(__('New Application'), ['action' => 'add']); ?></li>
<?php $this->end(); ?>
<?php $this->assign('tb_sidebar', '<ul class="nav nav-sidebar">' . $this->fetch('tb_actions') . '</ul>'); ?>

<table class="table table-striped" cellpadding="0" cellspacing="0">
    <thead>
        <tr>
            <th><?= $this->Paginator->sort('id'); ?></th>
            <th><?= $this->Paginator->sort('name'); ?></th>
            <th><?= $this->Paginator->sort('inactive'); ?></th>
            <th><?= $this->Paginator->sort('created'); ?></th>
            <th class="actions"><?= __('Actions'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($applications as $application): ?>
        <tr>
            <td><?= $this->Number->format($application->id) ?></td>
            <td><?= h($application->name) ?></td>
            <td><?= h($application->inactive) ?></td>
            <td><?= h($application->created) ?></td>
            <td class="actions">
                <?= $this->Html->link('V', ['action' => 'view', $application->id], ['title' => __('View'), 'class' => 'btn btn-success']) ?>
                <?= $this->Html->link('E', ['action' => 'edit', $application->id], ['title' => __('Edit'), 'class' => 'btn btn-warning']) ?>
                <?= $this->Form->postLink('D', ['action' => 'delete', $application->id], ['confirm' => __('Are you sure you want to delete # {0}?', $application->id), 'title' => __('Delete'), 'class' => 'btn btn-danger']) ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<div class="paginator">
    <ul class="pagination">
        <?= $this->Paginator->prev('< ' . __('previous')) ?>
        <?= $this->Paginator->numbers(['before' => '', 'after' => '']) ?>
        <?= $this->Paginator->next(__('next') . ' >') ?>
    </ul>
    <p><?= $this->Paginator->counter() ?></p>
</div>