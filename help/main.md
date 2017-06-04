# `\Rover\Params\Main` 
## Списки параметров главного модуля 
### `public static function getSysGroups($hideAdmin = false, array $params = [])`
Возвращает системные группы
* `$hideAdmin` - скрывать ли группу администраторов (2) из результата

### `public static function getEventTypes($lid = 'ru', array $params = [])`
Возвращает типы почтовых событий
* `$lid` - фильтр по языку

### `public static function getSites(array $params = [])`
Возвращает список существующих сайтов.

### `public static function getUsers(array $params = [])`
Возвращает список пользователей.