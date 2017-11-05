# Списки параметров техподдержки
`namespace \Rover\Params\Support`

### `public static function getCategory(array $params = array())`
Возвращает список значений справочника типа "категория".

### `public static function getCriticality(array $params = array())`
Возвращает список значений справочника типа "критичность".

### `public static function getDictionaryByType($type, array $params = array())`
Возвращает список значений справочника по буквенному коду его типа, например `C` - категория, `R` - критичность.

### `public static function getSla(array $params = array())`
Возвращает список уровней поддержки.

