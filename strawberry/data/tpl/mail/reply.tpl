<?
// inc/show.add-comments.php
?>

Subject: <?=$name; ?> ??????? ?? ??? ???????????

????????????!

?? ????????? ??????????? ? ?<?=$row['title']; ?>?, <?=$name; ?> ?? ???? ???????.

???????????:
------------
<?=str_replace('<br />', "\n", $comments); ?>

?????????? ????? ????? <?=cute_get_link($row); ?>