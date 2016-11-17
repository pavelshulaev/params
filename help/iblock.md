#`\Rover\Params\Iblock` Списки параметров инфоблоков
##`public static function getTypes(array $params = [])`
Возвращает типы инфоблоков.
##`public static function getByType($type, $siteId = null, array $params = [])`
Возвращает инфоблоки по типу.
* `$type` - тип инфоблока
* `$siteId` - доп. фильтр по ид сайта
##`public static function getSections($iblockId, $withSubsections = true, array $params = [])`
Возвращает разделы по id инфоблока
* `$iblockId` - id инфоблока
* `$withSubsections` - включать ли в результат подразделы
##`public static function getElements($iblockId, $sectionId = null, array $params = [])`
Возвращает элементы инфоблока
* `$iblockId` - id инфоблока
* `$sectionId` - дополнительный фильтр по id раздела инфоблока
##`public static function getProps($iblockId, array $params = [])`
Возвращает свойства инфоблока
* `$iblockId` - id инфоблока