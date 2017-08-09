<?php

//подключаем класс
require_once 'amocrm/Amo.php';
//создаем новый объект класса, передаем параметры для авторизации
$amo = new Amo('antony105@mail.ru', '57b8596346cc4e963b43cd6b26aecba7', 'arte24');

//var_dump($amo);

//перебор массива пост
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

//передаем в массив дата
$data = array(
	'name' => $fields[0]['val'],
	'price' => 0,
	'tags' => 'C сайта',
	'fields' => $fields
);


//вызываем метод создания контакта
$amo->setContact($data);
//возвращаем на страницу
$redicet = $_SERVER['HTTP_REFERER'];
header ("Location: $redicet");
