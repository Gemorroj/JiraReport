# Формирование отчета из JIRA

### Требования:

- PHP >= 5.4


### Установка через composer:

- Добавьте проект в ваш файл composer.json:

```json
{
    "require": {
        "gemorroj/jira-report": "dev-master"
    }
}
```
- Установите проект:

```bash
$ php composer.phar update gemorroj/jira-report
```


### Пример работы:

```php
<?php
require __DIR__ . '/vendor/autoload.php';

use JiraReport\Jira;
use JiraReport\Excel;
use JiraReport\Filter;

// авторизация
$jira = new Jira('myusername', 'password');

// дополнительный фильтр дат для worklog
$filter = (new Filter())
    ->setUsername('myusername')
    ->setWorklogDateFrom(new \DateTime('2016-04-01 00:00:00'))
    ->setWorklogDateTo(new \DateTime('2016-04-30 23:59:59'));
$jira->setFilter($filter);

// строка запроса JQL
$jira->findIssues('(worklogAuthor = myusername AND worklogDate >= 2016-04-01 AND worklogDate <= 2016-04-30) OR (timespent IS NULL AND labels = mylabel AND resolutiondate >= 2016-04-01 AND resolutiondate <= 2016-04-30) ORDER BY key DESC');
// вытаскиваем данные из jira
$jira->makeData();

// формируем отчет
$excel = new Excel(__DIR__ . '/report.xlsx');
$excel->makeExcel($jira);
```
