# `\Rover\Params\Main` 
## Списки параметров главного модуля 
### `public static function getSysGroups($hideAdmin = false, array $params = [])`
Возвращает системные группы
* `$hideAdmin` - скрывать ли группу администраторов (1) из результата

### `public static function getUserSysGroups($userId, $hideAdmin = false, array $params = [])`
Возвращает системные группы для пользователя с ID = $userId
* `$usetId` - ID пользователя, для которого надо получить список;
* `$hideAdmin` - скрывать ли группу администраторов (1) из результата

### `public static function getEventTypes($lid = 'ru', array $params = [])`
Возвращает типы почтовых событий
* `$lid` - фильтр по языку

### `public static function getEventMessages($siteId = '', $eventName = '', array $params = [])`
Возвращает шаблоны почтовых событий
* `$siteId` - фильтр по сайту
* `$eventName` - фильтр по названию почтового события

### `public static function getSites(array $params = [])`
Возвращает список существующих сайтов.

### `public static function getUsers(array $params = [])`
Возвращает список пользователей.

### `public static function getUserType($object, array $params = [])`
Возвращает список пользовательских полей.
* `$object` - объект, для которого необходимо вывести список