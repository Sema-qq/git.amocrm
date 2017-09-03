<?php
require_once 'amocrm/Amo.php';


$amo = new Amo('test@mail.ru', '1234e8867754ae56db88ed412eca06b0', 'domen');
var_dump($amo);
$fields = array(
    0 => array(
        'text' => 'Действие',
        'val' => $_POST['action'] //!
    ),
    1 => array(
        'text' => 'Имя',
        'val' => $_POST['name'] //!
    ),
    2 => array(
        'text' => 'Телефон',
        'val' => $_POST['phone']
    ),
    3 => array(
        'text' => 'Текст',
        'val' => $_POST['text']
    ),
);

$data = array(
	'name' => $fields[0]['val'],
	'price' => 0,
	'tags' => 'C сайта',
	'fields' => $fields
);

//$amo->setLead($data);
$amo->setContact($data);
/*
$redicet = $_SERVER['HTTP_REFERER'];
header ("Location: $redicet");
*/
