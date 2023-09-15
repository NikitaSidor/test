<?php
	setlocale (LC_ALL, 'ru_RU.UTF-8');
	$captcha = isset($captcha) ? $captcha : 2;
	$moderated = isset($moderated) ? $moderated : 1;
	$customfields = isset($customfields) ? $customfields : 'name,email,answer,avatar,nodate';
	$validate = isset($validate) ? $validate : 'name:Вы не написали своё имя,email:Неправильный e-mail:email,content:Вы не заполнили поле сообщения';
	$cssFile = isset($cssFile) ? $cssFile : 'assets/snippets/jot/css/faq.css';

	$onBeforePOSTProcess = isset($onBeforePOSTProcess) ? $onBeforePOSTProcess : 'antispam,createdon';
	$onSetFormOutput = isset($onSetFormOutput) ? $onSetFormOutput : 'antispam';

	$tplForm='@CODE:
<div class="blank">
	<div class="jot-form-wrap">
	<a name="jf[+jot.link.id+]"></a>
	<h2 class="h2">[+form.edit:is=`1`:then=`Изменить отзыв`:else=`Написать отзыв`+]</h2>
	[+form.error:isnt=`0`:then=`
	<div class="jot-err">
	[+form.error:select=`
	&-3=Вы пытаетесь отправить одно и то же сообщение. Возможно вы нажали кнопку отправки более одного раза.
	&-2=Ваше сообщение было отклонено.
	&-1=Ваше сообщение сохранёно, оно будет опубликовано после просмотра администратором.
	&1=Вы пытаетесь отправить одно и то же сообщение. Возможно вы нажали кнопку отправки более одного раза.
	&2=Вы ввели неправильный защитный код.
	&3=Вы можете отправлять сообщения не чаще [+jot.postdelay+] секунд.
	&4=Ваше сообщение было отклонено.
	&5=[+form.errormsg:ifempty=`Вы не заполнили все требуемые поля`+]
	`+]
	</div>
	`:strip+]
	[+form.confirm:isnt=`0`:then=`
	<div class="jot-cfm">
	[+form.confirm:select=`
	&1=Ваше сообщение опубликовано.
	&2=Ваше сообщение сохранёно, оно будет опубликовано после просмотра администратором.
	&3=Сообщение сохранено.
	`+]
	</div>
	`:strip+]
	<form class="jot-form form" method="post" enctype="multipart/form-data" action="[+form.action:esc+]#jf[+jot.link.id+]" class="jot-form">
		<input name="JotForm" type="hidden" value="[+jot.id+]" />
		<input name="JotNow" type="hidden" value="[+jot.seed+]" />
		<input name="parent" type="hidden" value="[+form.field.parent+]" />
		
		<div class="row0">
			<div class="w50p s-w100p p10">
				<div class="jot-controls">
					<label for="name-[+jot.link.id+]">Ваше имя:</label>
					<input tabindex="[+jot.seed:math=`?+1`+]" name="name" type="text" size="40" value="[+form.field.custom.name:esc+]" id="name-[+jot.link.id+]" />
				</div>
				<div class="jot-controls">
					<label for="name-[+jot.link.id+]">Тема, место путешествия:</label>
					<input tabindex="[+jot.seed:math=`?+2`+]" name="title" type="text" size="40" value="[+form.field.title:esc+]" id="title-[+jot.link.id+]" />
				</div>
				<div class="jot-controls">
					<label for="email-[+jot.link.id+]">Ваш email:</label>
					<input tabindex="[+jot.seed:math=`?+3`+]" name="email" type="text" size="40" value="[+form.field.custom.email:esc+]" id="email-[+jot.link.id+]" />
				</div>
				<div class="jot-controls">
					<label for="email-[+jot.link.id+]">Ваше фото:</label>
					<div class="btn trigger-file">Выбрать файл</div> <div class="filename"></div>
					<input style="display: none;" tabindex="[+jot.seed:math=`?+4`+]" name="avatar" type="file" value="[+form.field.custom.avatar:esc+]" id="file-[+jot.link.id+]" />
					<input type="hidden" name="avatar" value="[+form.field.custom.avatar:esc+]">
				</div>
			</div>
			

			<div class="w50p s-w100p p10">
				<div class="jot-controls">
					<label for="content-[+jot.link.id+]">Отзыв:</label>
					<textarea tabindex="[+jot.seed:math=`?+5`+]" name="content" cols="50" rows="6" id="content-[+jot.link.id+]">[+form.field.content:esc+]</textarea>
				</div>
     
				<div class="jot-controls [+form.moderation:is=`1`:then=`dn`+]">

					<div class="popup_row clr">
						<div class="popup__field">
							<label class="agreement__label">Подтверждаю согласие на <a href="/[~70~]" target="_blank">обработку персональных данных</a>
								<input type="checkbox" value="1" name="agr" class="agreement__checkbox" checked="checked" required="required">
							</label>
						</div>
					</div>	
				</div>
				
		  
				[+form.moderation:is=`1`:then=`
				<div class="jot-controls">
					<label for="answer-[+jot.link.id+]">Ответ:</label>
					<textarea tabindex="[+jot.seed:math=`?+8`+]" name="answer" cols="50" rows="6" id="answer-[+jot.link.id+]">[+form.field.custom.answer:esc+]</textarea>

				</div>

				<div class="jot-controls">
					<label>Дата отзыва:</label>
					<input tabindex="[+jot.seed:math=`?+1`+]" name="createdon" type="text" value="[+form.field.createdon:gt=`0`:then=`[+form.field.createdon:date=`%d.%m.%Y`+]`+]" id="name-[+jot.link.id+]" />
					<label>
						<input type="checkbox" onchange="if(this.checked) document.getElementById(\'nodate\').value=1; else document.getElementById(\'nodate\').value=\'\';" [+form.field.custom.nodate:is=`1`:then=`checked="checked"`+]> 
						скрыть дату
						<input type="hidden" id="nodate" name="nodate" value="[+form.field.custom.nodate:esc+]">
					</label>
				</div>
				`+]
				[+jot.captcha:is=`1`:then=`
				<div class="jot-controls captcha">
					<div class="row10">
						<div class="w50p pw10">
							<label for="vericode-[+jot.link.id+]">Код:</label>
							<input type="text" name="vericode" id="vericode-[+jot.link.id+]" style="width:150px" size="20" />
						</div>
						<div class="w50p pw10">
							<a href="[+jot.link.current:esc+]" onclick="document.captcha.src=document.captcha.src+\'?rand=\'+Math.random(); return false;" title="Если код не читается, нажмите сюда, чтобы сгенерировать новый"><img src="/'.MODX_MANAGER_URL.'/includes/veriword.php?rand=[+jot.seed+]" name="captcha" class="jot-captcha" width="148" height="60" alt="" /></a>
						</div>
					</div>
				</div>
				`+]
				<div class="jot-form-actions">
					[+form.edit:ne=`1`:then=`
                    <button tabindex="[+jot.seed:math=`?+5`+]" class="submit btn review__submit" type="submit">Отправить</button>
                    `+]
                    [+form.edit:is=`1`:then=`
                    <button tabindex="[+jot.seed:math=`?+5`+]" class="submit btn review__submit" type="submit">Сохранить</button>
					<input tabindex="[+jot.seed:math=`?+6`+]" class="jot-btn jot-btn-cancel" type="button" onclick="history.go(-1);return false;" value="Отмена" />
					`+]
				</div>
			</div>
		</div>


	</form>
	</div>
</div>
	';

	$tplComments='@CODE:
<div class="jot-comment item cf lastcomment">
	<a name="jc[+jot.link.id+][+comment.id+]"></a>
	<div class="jot-row [+chunk.rowclass+] [+comment.published:is=`0`:then=`jot-row-up`+]">
		<div class="jot-comment-head sideLeft">
			<div class="round120 img">
				<img src="/[[phpthumb? &input=`[+comment.custom.avatar:ifempty=`/assets/images/uploads/noavatar.png`+]` &options=`w=180,h=180,q=84,zc=1`]]">
			</div>
			[+jot.moderation.enabled:is=`100000`:then=`<span class="jot-extra"><a target="_blank" href="http://www.ripe.net/perl/whois?searchtext=[+comment.secip+]">([+comment.secip+])</a></span>`+]
			<span class="jot-perma"><a rel="nofollow" title="Ссылка на вопрос" href="[+jot.link.current+]#jc[+jot.link.id+][+comment.id+]">#[+comment.postnumber+]</a> |</span>
			<div class="bgblank cf">
				<div class="center jot-name">
					[+comment.custom.name:ifempty=`[+jot.guestname:ifempty=`[+comment.username+]`:esc+]`+]
				</div>
				[[if? &is=`[+comment.custom.nodate+]:notempty` &then=`
					[+comment.custom.nodate:ne=`1`:then=`
						[+comment.createdon:gt=`0`:then=`
						<div class="center jot-date">
							[+comment.createdon:date=`%d.%m.%Y`+]
						</div>
						`+]
					`+]
				` &else=`
					[+comment.createdon:gt=`0`:then=`
					<div class="center jot-date">
						[+comment.createdon:date=`%d.%m.%Y`+]
					</div>
					`+]
				`]]
			</div>
		</div>
		<div class="jot-comment-entry sideRight">
			[+comment.title:length:ne=`0`:then=`<div class="jot-subject">[+comment.title:esc+]</div>`+]
			<div class="jot-message">[+comment.content:wordwrap:esc:nl2br+]</div>
			<div class="jot-mod cf">
				[+jot.user.canedit:is=`1`:and:if=`[+comment.createdby+]`:is=`[+jot.user.id+]`:or:if=`[+jot.moderation.enabled+]`:is=`1`:then=`
					<a class="jot-btn jot-btn-edit" href="[+jot.link.edit:esc+][+jot.querykey.id+]=[+comment.id+]#jf[+jot.link.id+]" title="Изменить"><i class="jot-icon-edit"></i> Изменить</a>
				`:strip+]
				[+jot.moderation.enabled:is=`1`:then=`
					[+comment.published:is=`0`:then=`<a class="jot-btn jot-btn-pub" href="[+jot.link.publish:esc+][+jot.querykey.id+]=[+comment.id+]#jotmod[+jot.link.id+]" title="Показать"><i class="jot-icon-pub"></i> Показать</a>`+]
					[+comment.published:is=`1`:then=`<a class="jot-btn jot-btn-unpub" href="[+jot.link.unpublish:esc+][+jot.querykey.id+]=[+comment.id+]#jotmod[+jot.link.id+]" title="Скрыть"><i class="jot-icon-unpub"></i> Скрыть</a>`+]
					<a class="jot-btn jot-btn-del" href="[+jot.link.delete:esc+][+jot.querykey.id+]=[+comment.id+]#jotmod[+jot.link.id+]" onclick="return confirm(\'Вы действительно хотите удалить это сообщение?\')" title="Удалить"><i class="jot-icon-del"></i> Удалить</a>
				`:strip+]
			</div>
		</div>
		[+comment.custom.answer:length:ne=`0`:then=`
		<div class="jot-answer cf">
			<span class="jot-answer-author">[(site_name)]:</span>
			[+comment.custom.answer:wordwrap:esc:nl2br+]
		</div>
		`+]
	</div>
</div>
	';

	$tplNav='@CODE:
<a name="jotnav[+jot.id+]"></a>
<div class="jot-nav">
	[+jot.page.current:gt=`1`:then=`
	<a rel="nofollow" href="[+jot.link.navigation:esc+][+jot.querykey.navigation+]=1#jotnav[+jot.id+]">Первая страница</a> |
	<a rel="nofollow" href="[+jot.link.navigation:esc+][+jot.querykey.navigation+]=[+jot.page.current:math=`?-1`+]#jotnav[+jot.id+]">Предыдущяя страница</a> |
	`+]
	Показаны сообщения с <b>[+jot.nav.start+]</b> по <b>[+jot.nav.end+]</b> из <b>[+jot.nav.total+]</b>
	[+jot.page.current:lt=`[+jot.page.total+]`:then=`
	| <a rel="nofollow" href="[+jot.link.navigation:esc+][+jot.querykey.navigation+]=[+jot.page.current:math=`?+1`+]#jotnav[+jot.id+]">Следующая страница</a>
	| <a rel="nofollow" href="[+jot.link.navigation:esc+][+jot.querykey.navigation+]=[+jot.page.total+]#jotnav[+jot.id+]">Последняя страница</a>
	`+]
</div>
	';
?>