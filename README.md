# Модуль для Битрикс «Списки параметров»
Модуль предоставляет api для получения данных в виде двумерных массивов из многих моделей битрикс. Модуль можно использовать для получения списков параметров в файлах `.parameters.php` компонентов, а так же для других целей.

Модуль доступен на [Маркетплейсе Битрикса](http://marketplace.1c-bitrix.ru/solutions/rover.params/).

## Возможности
На данный момент доступны списки параметров из:
* [Торгового каталога (catalog)](./help/catalog.md)
* [Валют (currency)](./help/currency.md)
* [Веб-форм (form)](./help/form.md)
* [Форума (forum)](./help/forum.md)
* [Highload - блоков (highloadblock)](./help/highloadblock.md)
* [Инфоблоков (iblock)](./help/iblock.md)
* [Главного модуля (main)](./help/main.md)
* [Интернет-магазина (sale)](./help/sale.md)
* [Социальной сети (socialnetwork)](./help/socialnetwork.md)
* [Веб-статистики (statistic)](./help/statistic.md)
* [Документооборота (workflow)](./help/workflow.md)
* [Блога (blog)](./help/blog.md)

## Использование
Практически каждый метод (с полным переходм Битрикса на d7 - каждый) последним параметром принимает массив `$params`. Этот массив может содеражать слежующие поля, влияющие на поведение метода:
* `empty` - подпись для первого, "пустого" элемента списка. Если задана и равняется `null` (`['empty' => null]`), то пустой элемент не создается;
* `template` - массив, содержащий шаблон элементов возвращаемого списка. Заменяемые поля должны быть заключены в фигурные скобки. Например `['template' => ['{ID}' => '[{ID}] {NAME}']]` означает, что ключем списке будет `ID` сущности, а значением - `ID` в квадратных скобках, а, затем, имя;
* `filter` - переопределение ключа `filter` запроса orm getList, задаёт правила выборки;
* `add_filter` - задаёт дополнительные условия выборки запроса orm getList. Присоденияется к полям, переданным в ключе `filter`, не переопределяя их;
* `order` - переопределение ключа `order` запроса orm getList, задаёт сортировку списка;
* `select` - переопределение ключа `select` запроса orm getList, указывает, какие поля следует выбрать при запросе. Если не передано, то формируется из полей, переданных в ключе `template`;
* `reload` - флаг прямого обращения к базе, минуя кеш. Если не передан, то значение только первый раз берется из базы, а затем - из кеша.

## Пример
Получить свойства инфоблока в формате `[CODE => [CODE] NAME]` без пустого значения.

    use Rover\Params\Iblock;
    use Bitrix\Main\Loader;
    
    if (Loader::includeModule('rover.params')){
    
        $iblockId = 1; // for example
        $params = [
            'template'  => ['{CODE}' => '[{CODE}] {NAME}'],
            'empty'     => null,
        ];
    
        $props = Iblock::getProps($iblockId, $params);
    
        echo '<pre>';
        print_r($props);
        echo '</pre>'; 
    
    } else {
        ShowError('module "rover.params" not found');
    }

## Контакты
Модуль активно развивается. Свои вопросы и плжелания вы можете отправлять на электропочту rover.webdev@gmail.com, либо через форму на сайте https://rover-it.me.

## Пожертвования
Если решение оказалось вам полезным, вы можете оставить пожертование

[![Donate](https://img.shields.io/badge/Donate-PayPal-green.svg)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=QBLE74K4BND7C)