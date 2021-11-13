<noscript>
<div class="error_message">����������� �� �� ��������. ����� ��������� ������������ JavaScript.</div>
</noscript>
<a name="comments"></a>
  <div id="comment_form">
  <? if (!$tpl['if-logged']){ ?>
  ���<br /><input type="text" name="name" value="<?=$tpl['form']['saved']['name']; ?>"><br />
  E-mail<br /><input type="text" name="mail" value="<?=$tpl['form']['saved']['mail']; ?>"><br />
  �����������<br /><input type="text" name="homepage" value="<?=($tpl['form']['saved']['homepage'] ? $tpl['form']['saved']['homepage'] : 'http://'); ?>"><br />
  <? } ?>
<div id="blokbbcodes"><noindex><?=tpl('bbcodes', 1); ?></noindex></div>
<?=$tpl['form']['smilies']; ?><br/>
<textarea name="comments" id="comments" onkeydown="message_onkeydown(this, <?=$config['comments_length']; ?>);" placeholder="��� �����������"></textarea><br/>
<label for="rememberme"><input type="checkbox" id="rememberme" name="rememberme" value="on" checked>��������� ���?</label>
<label for="sendcomments"><input type="checkbox" id="sendcomments" name="sendcomments" value="on"> �������� ����������� �� ��� e-mail?</label><br/> 
<input type="submit" id="submit" name="submit" value=" �������� " accesskey="s" />
</div>