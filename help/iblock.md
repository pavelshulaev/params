# Списки параметров инфоблоков
`namespace \Rover\Params\Iblock`

### `public static function getTypes(array $params = array())`
Возвращает типы инфоблоков.
### `public static function getByType($type, $siteId = null, array $params = array())`
Возвращает инфоблоки по типу.
* `$type` - тип инфоблока
* `$siteId` - доп. фильтр по ид сайта

### `public static function getSections($iblockId, $withSubsections = true, array $params = array())`
Возвращает разделы по id инфоблока
* `$iblockId` - id инфоблока
* `$withSubsections` - включать ли в результат подразделы

### `public static function getElements($iblockId, $sectionId = null, array $params = array())`
Возвращает элементы инфоблока
* `$iblockId` - id инфоблока
* `$sectionId` - дополнительный фильтр по id раздела инфоблока

### `public static function getProps($iblockId, array $params = array())`
Возвращает свойства инфоблока
* `$iblockId` - id инфоблока