<?php

$form['siteorder']=array(
	'subject'=>'Заказ сайта',
	'template'=>"
        <table>
		<tr>
            <td style='padding:4px 10px'>ФИО</td>
            <td style='padding:4px 10px'>[+os-name+]</td>
		</tr>
		<tr>
			<td style='padding:4px 10px'>Агентство</td>
			<td style='padding:4px 10px'>[+os-agency+]</td>
		</tr>
		<tr>
			<td style='padding:4px 10px'>Телефон</td>
			<td style='padding:4px 10px'>[+os-phone+]</td>
        </tr>
		<tr>
			<td style='padding:4px 10px'>E-mail</td>
			<td style='padding:4px 10px'>[+os-email+]</td>
        </tr>
		<tr>
			<td style='padding:4px 10px'>Комментарий</td>
			<td style='padding:4px 10px'>[+os-comment+]</td>
        </tr>
		<tr>
			<td style='padding:4px 10px'>Логотип</td>
			<td style='padding:4px 10px'>[+os-logosrc+]</td>
        </tr>
		<tr>
			<td style='padding:4px 10px'>Цветовая схема</td>
			<td style='padding:4px 10px'>[+os-colorscheme+]</td>
        </tr>
		<tr>
			<td style='padding:4px 10px'>Шаблон главной</td>
			<td style='padding:4px 10px'>[+os-hometpl+]</td>
        </tr>
    </table>",
    
    /*'to'=>'order@7vetrov.msk.ru,director@7vetrov.com, rn@7vetrov.com',*/
	'to'=>'andreyoren@gmail.ru',
	'replyTo'=>$_POST['e'],
	/* span protect  config */
	'isAjax'=>true,
	'emptyFieldName'=>'name',
	'noEmptyFieldName'=>'phone',
	'noEmptyFieldValue'=>'19X84-lider',
	'cookieName'=>''

);



