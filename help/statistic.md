# Списки параметров веб-статистики
`namespace \Rover\Params\Statistic`

### `public static function getAdvCompanies($referer1 = null, $referer2 = null)`
Устарел, вместо него лучше использовать `public static function getAdvCampaigns(array $params = array())`

### `public static function getAdvCampaigns(array $params = array())`
Возвращает существующие рекламные кампании с возможностью отфильтровать по $referer1 и $referer2, переданных в ключе `filter` параметра `$params`.

### `public static function getEventTypes(array $params = array())`
Возвращает список существующих типов событий модуля веб-аналитики