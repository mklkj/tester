<?php include '_head.html.php'; ?>

<?php if (!empty($this->data['exams_list'])): ?>
<ul>
<?php foreach ($this->data['exams_list'] as $key => $value): ?>
  <li><a href="<?=$this->dir();?>/test/<?=$value['id'];?>"><?=$value['title'];?></a> - <?=$value['questions'];?> pytań</li>
<?php endforeach; ?>
</ul>
<?php else: ?>
<div class="alert alert-info">Brak testów do wyświetlenia!</div>
<?php endif;?>

<?php include '_foot.html.php'; ?>