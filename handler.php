<?php
//подключаем класс интеграции с amocrm
require_once 'Amo.php';
//создаем новый объект класса, передаем параметры для авторизации в конструктор
$amo = new Amo('login', 'api-key', 'hash');
var_dump($amo);/*
//создаем нужный нам массив
$fields = [
    [
        'text' => 'Действие',
        'val' => $_POST['action']
    ],
    [
        'text' => 'Имя',
        'val' => $_POST['name']
    ],
    [
        'text' => 'Телефон',
        'val' => $_POST['phone']
    ],
    [
        'text' => 'Артикул',
        'val' => $_POST['art']
    ],
    [
        'text' => 'Вопрос',
        'val' => $_POST['message']
    ],
];
//раскладываем UTM метки
$utms = [
    [
        'id' => 1339364,
        'text' => 'Канал кампании',
        'val' => $_POST['utm_source']
    ],
    [
        'id' => 1339366,
        'text' => 'Источник кампании',
        'val' => $_POST['utm_medium']
    ],
    [
        'id' => 1339368,
        'text' => 'Название кампании',
        'val' => $_POST['utm_campaign']
    ],
    [
        'id' => 1339370,
        'text' => 'Ключевое слово',
        'val' => $_POST['utm_term']
    ],
    [
        'id' => 1339372,
        'text' => 'Содержание кампании',
        'val' => $_POST['utm_content']
    ],
];
//кладем всё в дату
$data = [
    'name' => $_POST['action'],
    'price' => 0,
    'tags' => 'С сайта',
    'fields' => $fields,
    'utms' => $utms
];
//вызываем метод создания контакта, в котором попутно создается и сделка
$amo->setContact($data);
