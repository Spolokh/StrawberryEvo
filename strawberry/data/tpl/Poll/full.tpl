<article id="news<?=$tpl['post']['id']; ?>" class="post">
<div class="<?=$tpl['post']['alternating']; ?>">
<div class="fonts" style="float:right"> 
  <?=$tpl['post']['rating']; ?> 
  <small><a href="javascript:changeFontSize(-1)">A</a></small> 
  <big><a href="javascript:changeFontSize(1)">A</a></big> 
</div>
<div class="date"><i class="icon-time"></i> <?=$tpl['post']['date']; ?> &nbsp; &nbsp;
<a href="<?=$tpl['post']['user']; ?>"><i class="icon-user"></i> <?=$tpl['post']['author']; ?></a></div>

<h1><a href="javascript:history.back(1)"><?=$tpl['post']['title']; ?></a></h1>
<? if($tpl['post']['image']){ ?>
	<a class="full-icon" href="<?=$tpl['post']['image']; ?>" title="<?=$tpl['post']['description']; ?>" onclick="return hs.expand(this)">
		<img src="<?=$tpl['post']['icon']; ?>" width="<?=$config['newsicon'];?>" alt="<?=$tpl['post']['description']; ?>">
	</a>
<? } ?>

<!--
<img src="/cp/thumb.php?src=<?//=$tpl['post']['image']; ?>&w=620&h=350&s=1" alt="<?//=$tpl['post']['description']; ?>
-->

<div class="story full">
<?php
echo $tpl['post']['full-story'] ? $tpl['post']['full-story'] : $tpl['post']['short-story'];

if ($tpl['post']['attachment']){
	echo $tpl['post']['attachment'];
} 
 
if ($tpl['post']['pages']){ ?>
<center>(<?=$tpl['post']['pages']; ?>)</center>
<? } ?>
</div>

<script type="text/javascript" src="//yandex.st/share/share.js" charset="utf-8"></script>
<div class="yashare-auto-init" data-yashareL10n="ru" data-yashareType="none"></div>


<div class="attr">
<? if ($tpl['post']['category']['name']){ ?>
���������: <?=$tpl['post']['category']['name']; ?> 
<? } ?>
<? if ($tpl['post']['tags']['name']){ ?>
���������: <?=$tpl['post']['tags']['name']; ?> 
<? } ?>

 <a title="������" href="<?=$tpl['post']['link']['print.php/post']; ?>"></a> 
 <a title="RSS ������������" href="<?=$tpl['post']['link']['rss.php/post']; ?>"></a> 
 <?php //echo $tpl['post']['sendFriend']; ?>
</div>

</div>
</article>
