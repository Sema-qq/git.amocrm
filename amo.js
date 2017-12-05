//обработка отправки форм для amocrm

$(document).ready(function () {
    $('form').submit(function () {
        // var form = $(this).serializeArray();
        //инициализируем нужные переменные
        var action = $(this).find('input[name=action]').val();
        var name = 'Без имени';
        var phone = $(this).find('input[name^="tel"]').val();
        var art = '';
        var message = '';
        //utm метки
        var utm_source = $(this).find('input[name=utm_source]').val();
        var utm_campaign = '';
        var utm_content = '';
        var utm_term = '';
        var utm_medium = '';
        //если имя в форме есть, то положим куда надо
        if ($(this).find('input[name^="text"]').length > 0){
            name = $(this).find('input[name^="text"]').val();
        }//если в форме есть артикул, то кладем его куда надо
        if ($(this).find('input[name=art]').length > 0){
            art = 'Артикул: '+$(this).find('input[name=art]').val();
        }//если в форме есть сообщение, то кладем его куда надо
        if ($(this).find('input[name^="textarea"]').length > 0){
            message = 'Вопрос: '+$(this).find('input[name^="textarea"]').val();
        }
        //собираем utm метки
        if ($(this).find('input[name=utm_campaign]').length > 0){
            utm_campaign = $(this).find('input[name=utm_campaign]').val();
        }
        if ($(this).find('input[name=utm_content]').length > 0){
            utm_content = $(this).find('input[name=utm_content]').val();
        }
        if ($(this).find('input[name=utm_term]').length > 0){
            utm_term = $(this).find('input[name=utm_term]').val();
        }
        if ($(this).find('input[name=utm_medium]').length > 0){
            utm_medium = $(this).find('input[name=utm_medium]').val();
            if (utm_medium == 'cpc'){
                //если cpc, то сделаем подмены некоторых значений
                utm_source = sourceReplace(utm_source);
            }
        }
        //отправляем ajax в файл обработчик
        $.ajax({
            url:'/wp-content/themes/akvavit/amo/handler.php',
            type: 'POST',
            data: {
                action:action,
                name:name,
                phone:phone,
                art:art,
                message:message,
                utm_source:utm_source,
                utm_campaign:utm_campaign,
                utm_content:utm_content,
                utm_term:utm_term,
                utm_medium:utm_medium
            },
            dataType: 'html'
        });
        // return false; //отключает перезагрузку страницы
    });
    setTimeout(getUtm(), 5000);
});

// Utm
function getUtm() {
    //если реклама отработала, то берем гет запрос,
    // substr убирает вопросительный знак
    // replace убирает лишние символы из ключевых слов (utm_term)
    var search = decodeURI(window.location.search.substr(1)).replace(/%2B/g, '');
    var keys = new Object();
    var time = 60 * 60 * 24 * 30;
    var cookieUtm = document.cookie.replace(/(?:(?:^|.*;\s*)utm\s*\=\s*([^;]*).*$)|^.*$/, "$1");
    //если не по рекламе зашли, то проверяем куки
    if (search === '') {
        if (cookieUtm) {
            keys = getUtmParts(cookieUtm);
        } else { //иначе кладем СЕО
            keys.utm_source = 'SEO';
        }
    } else {
        keys = getUtmParts(search);
        setCookie('utm', search, time, '/');
    }

    var fieldset = $('<fieldset></fieldset>');

    for (var p in keys) {
        fieldset.prepend('<input type="hidden" name="' + p + '" value="' + keys[p] + '">');
    }

    $('form').prepend(fieldset);
}

function getUtmParts(value) {
    var parts = new Object();

    value.split('&').forEach(function(item) {
        item = item.split('=');
        parts[item[0]] = item[1];
    });

    return parts;
}

function setCookie(name, value, exp, path) {
    var cookie = name + '=' + value;
    exp = exp || null;
    path = path || null;

    if (exp) {
        cookie += '; max-age=' + exp;
    }

    if (path) {
        cookie += '; path=' + path;
    }

    document.cookie = cookie;
}
//подмена некоторых значений для utm_source
function sourceReplace(utm_source) {
    if (utm_source == 'yandex.search'){
        utm_source = 'ЯД поиск';
    } else if (utm_source == 'yandex'){
        utm_source = 'ЯД РСЯ';
    } else if (utm_source == 'google'){
        utm_source = 'Google поиск';
    }
    return utm_source;
}
