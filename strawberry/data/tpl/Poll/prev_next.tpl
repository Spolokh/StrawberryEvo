<div class="pagination pagination-centered">
	<ul>
	<?=($tpl['prev-next']['prev-link'] ? '<li><a class="prev icon-chevron-left" href="'.$tpl['prev-next']['prev-link'].'"></a>' : ''); ?>
	<?=$tpl['prev-next']['pages']; ?> 
	<?=($tpl['prev-next']['next-link'] ? '<li><a class="next icon-chevron-right" href="'.$tpl['prev-next']['next-link'].'"></a>' : ''); ?>
	</ul>
</div>