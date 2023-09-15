<?php

$form['callback']=array(
	'subject'=>'Заявка на обратный звонок',
	'template'=>"
		<table>
		<tr>
			<td style='padding:4px 10px'>ФИО</td>
			<td style='padding:4px 10px'>[+n+]</td>
		</tr>
		<tr>
			<td style='padding:4px 10px'>Телефон</td>
			<td style='padding:4px 10px'>[+t+]</td>
		</tr>
		<tr>
			<td style='padding:4px 10px'>Сообщение</td>
			<td style='padding:4px 10px'>[+m+]</td>
		</tr>
	</table>",
	//'to'=>'mail@mail.ru',
	'replyTo'=>$_POST['e'],
	/* span protect  config */
	'isAjax'=>true,
	'emptyFieldName'=>'name',
	'noEmptyFieldName'=>'phone',
	'noEmptyFieldValue'=>'19X84-lider',
	'cookieName'=>''

);


$form['quickorder']=array(
	'subject'=>'Заявка на подбор тура',
	'template'=>"
		<table>
		<tr>
			<td style='padding:4px 10px'>Телефон</td>
			<td style='padding:4px 10px'>[+t+]</td>
		</tr>
		<tr>
			<td style='padding:4px 10px'>Пожелание к туру</td>
			<td style='padding:4px 10px'>[+m+]</td>
		</tr>
	</table>",
	//'to'=>'mail@mail.ru',
	/* span protect  config */
	'isAjax'=>true,
	'emptyFieldName'=>'name',
	'noEmptyFieldName'=>'phone',
	'noEmptyFieldValue'=>'19X84-lider',
	'cookieName'=>''

);


$form['order-country']=array(
	'subject'=>'Заявка на подбор тура в страну',
	'template'=>"
        <table>
        <tr>
			<td style='padding:4px 10px'>Тур в</td>
			<td style='padding:4px 10px'>[+pagetitle+]</td>
		</tr>
        <tr>
			<td style='padding:4px 10px'>ФИО</td>
			<td style='padding:4px 10px'>[+n+]</td>
		</tr>
		<tr>
			<td style='padding:4px 10px'>Телефон</td>
			<td style='padding:4px 10px'>[+t+]</td>
		</tr>
		<tr>
			<td style='padding:4px 10px'>Пожелание к туру</td>
			<td style='padding:4px 10px'>[+m+]</td>
		</tr>
	</table>",
	//'to'=>'mail@mail.ru',
	/* span protect  config */
	'isAjax'=>true,
	'emptyFieldName'=>'name',
	'noEmptyFieldName'=>'phone',
	'noEmptyFieldValue'=>'19X84-lider',
	'cookieName'=>''

);

$form['order-tour']=array(
	'subject'=>'Заявка на тур',
	'template'=>"
        <table>
        <tr>
			<td style='padding:4px 10px'>Тур</td>
			<td style='padding:4px 10px'><a href='[+url+]' target='_blank'>[+pagetitle+]</a></td>
		</tr>
        <tr>
			<td style='padding:4px 10px'>ФИО</td>
			<td style='padding:4px 10px'>[+n+]</td>
		</tr>
		<tr>
			<td style='padding:4px 10px'>Телефон</td>
			<td style='padding:4px 10px'>[+t+]</td>
		</tr>
		<tr>
			<td style='padding:4px 10px'>Пожелание к туру</td>
			<td style='padding:4px 10px'>[+m+]</td>
		</tr>
	</table>",
	//'to'=>'mail@mail.ru',
	/* span protect  config */
	'isAjax'=>true,
	'emptyFieldName'=>'name',
	'noEmptyFieldName'=>'phone',
	'noEmptyFieldValue'=>'19X84-lider',
	'cookieName'=>''

);


$form['feedback']=array(
	'subject'=>'Обратная связь с сайта',
	'template'=>"
		<table>
		<tr>
			<td style='padding:4px 10px'>ФИО</td>
			<td style='padding:4px 10px'>[+n+]</td>
		</tr>
		<tr>
			<td style='padding:4px 10px'>Телефон</td>
			<td style='padding:4px 10px'>[+t+]</td>
		</tr>
		<tr>
			<td style='padding:4px 10px'>Сообщение</td>
			<td style='padding:4px 10px'>[+m+]</td>
		</tr>
	</table>",
	//'to'=>'mail@mail.ru',
	'replyTo'=>$_POST['e'],
	/* span protect  config */
	'isAjax'=>true,
	'emptyFieldName'=>'name',
	'noEmptyFieldName'=>'phone',
	'noEmptyFieldValue'=>'19X84-lider',
	'cookieName'=>''

);


$form['order-short']=array(
	'subject'=>'Заявка на тур',
	'template'=>"
		<table>
		<tr>
			<td style='padding:4px 10px'>Телефон</td>
			<td style='padding:4px 10px'>[+t+]</td>
		</tr>
		<tr>
			<td style='padding:4px 10px'>Сообщение</td>
			<td style='padding:4px 10px'>[+msg+]</td>
		</tr>
	</table>",
	//'to'=>'mail@mail.ru',
	'replyTo'=>$_POST['e'],
	/* span protect  config */
	'isAjax'=>true,
	'emptyFieldName'=>'name',
	'noEmptyFieldName'=>'phone',
	'noEmptyFieldValue'=>'19X84-lider',
	'cookieName'=>''

);


$form['order-long']=array(
	'subject'=>'Заявка на тур',
	'template'=>"
	<table>
		<tr>
			<td style='padding:4px 10px'>ФИО</td>
			<td style='padding:4px 10px'>{$_POST['n']}</td>
		</tr>
		<tr>
			<td style='padding:4px 10px'>Телефон</td>
			<td style='padding:4px 10px'>{$_POST['t']}</td>
		</tr>
		<tr>
			<td style='padding:4px 10px'>Email</td>
			<td style='padding:4px 10px'>{$_POST['e']}</td>
		</tr>
		<tr>
			<td style='padding:4px 10px'>Сообщение</td>
			<td style='padding:4px 10px'>{$_POST['msg']}</td>
		</tr>


		<tr>
			<td style='padding:4px 10px'>Откуда</td>
			<td style='padding:4px 10px'>{$_POST['from']}</td>
		</tr>
		<tr>
			<td style='padding:4px 10px'>Куда</td>
			<td style='padding:4px 10px'>{$_POST['to']}</td>
		</tr>

		<tr>
			<td style='padding:4px 10px'>Поедут</td>
			<td style='padding:4px 10px'>Взрослых: {$_POST['adults']}  Детей: {$_POST['children']}</td>
		</tr>

		<tr>
			<td style='padding:4px 10px'>Вылет</td>
			<td style='padding:4px 10px'>{$_POST['date']}</td>
		</tr>

		<tr>
			<td style='padding:4px 10px'>Ночей</td>
			<td style='padding:4px 10px'>от {$_POST['nightsfrom']} {$_POST['nightsto']}<br>
			Один день, без ночевки?: {$_POST['day']}
			</td>
		</tr>

		<tr>
			<td style='padding:4px 10px'>Класс отеля</td>
			<td style='padding:4px 10px'>{$_POST['hotel']}</td>
		</tr>

		<tr>
			<td style='padding:4px 10px'>Питание</td>
			<td style='padding:4px 10px'>{$_POST['meal']}</td>
		</tr>

		<tr>
			<td style='padding:4px 10px'>Цена</td>
			<td style='padding:4px 10px'>от {$_POST['pricefrom']} до {$_POST['priceto']} {$_POST['currency']} за {$_POST['pricepart']}</td>
		</tr>
	</table>",
	//'to'=>'mail@mail.ru',
	'replyTo'=>$_POST['e'],
	/* span protect  config */
	'isAjax'=>true,
	'emptyFieldName'=>'name',
	'noEmptyFieldName'=>'phone',
	'noEmptyFieldValue'=>'19X84-lider',
	'cookieName'=>''

);

