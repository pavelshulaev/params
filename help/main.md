# `\Rover\Params\Main` 
## Списки параметров главного модуля 
### `public static function getSysGroups($hideAdmin = false, array $params = array())`
Возвращает системные группы
* `$hideAdmin` - скрывать ли группу администраторов (1) из результата

### `public static function getUserSysGroups($userId, $hideAdmin = false, array $params = array())`
Возвращает системные группы для пользователя с ID = $userId
* `$usetId` - ID пользователя, для которого надо получить список;
* `$hideAdmin` - скрывать ли группу администраторов (1) из результата

### `public static function getEventTypes($lid = 'ru', array $params = array())`
Возвращает типы почтовых событий
* `$lid` - фильтр по языку

### `public static function getEventMessages($siteId = '', $eventName = '', array $params = array())`
Возвращает шаблоны почтовых событий
* `$siteId` - фильтр по сайту
* `$eventName` - фильтр по названию почтового события

### `public static function getSites(array $params = array())`
Возвращает список существующих сайтов.

### `public static function getUsers(array $params = array())`
Возвращает список пользователей.

### `public static function getUserType($object, array $params = array())`
Возвращает список пользовательских полей.
* `$object` - объект, для которого необходимо вывести список