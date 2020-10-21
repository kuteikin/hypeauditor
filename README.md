# Тестовое задание для hypeauditor

**Сomposer и PHPUnit я вынес за скобки. Тут толко мой код. Версия PHP 5.6.4**

### Задача 1

_Вам поступила задача:_

_Необходимо создать общую ленту новостей для пользователей с возможностью оценки постов в ленте._

_Лента должна иметь фильтр по категориям. Любой пользователь может поставить "лайк" или отменить его. Необходимо
предусмотреть возможность просмотра списка всех оценивших пост пользователей. Ограничение на размер хранения контента
одного поста - 243 байта._

_Предложите структуру базы данных MySQL, позволяющую реализовать данную задачу. Напишите запросы для выборки и
обновления контента. Обоснуйте выбор индексов._

Сама структура и данные лежат тут **/src/task1/dump.sql**

```SQL
-- Любой пользователь может поставить "лайк" или отменить его.
INSERT INTO `likes` (`id`, `id_user`, `id_post`) VALUES (NULL, '4', '3');
DELETE FROM `likes` WHERE `likes`.`id_user` = 4 AND `id_post` = 3

-- Необходимо предусмотреть возможность просмотра списка всех оценивших пост пользователей. 
SELECT u.id, u.name FROM user u INNER JOIN likes l ON l.id_user = u.id WHERE l.id_post = 4

-- Напишите запросы для выборки и обновления контента.
-- вывести все посты в категории + кол-во лайков
SELECT p.id, p.name, (SELECT count(l.id) FROM likes l WHERE l.id_post = p.id) likes FROM post p INNER JOIN post_category pc ON p.id = pc.id_post WHERE pc.id_category = 2 ORDER BY p.c_date LIMIT 10

UPDATE `post` SET `content` = 'Сухая рыбалка это здорово' WHERE `post`.`id` = 2;
```

### Задача 2

_Имеется таблица пользователей:_

```SQL
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `gender` tinyint(2) NOT NULL,
  `email` varchar(1024) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB;
```

_В таблице более 100 млн записей, и она находится под нагрузкой в production
(идут запросы на добавление / изменение / удаление)._

_В поле email может быть от одного до нескольких перечисленных через запятую адресов. Может быть пусто._

_Напишите скрипт, который выведет список представленных в таблице почтовых доменов с количеством пользователей по
каждому домену._

Можно попробовать кроном читать небольшими партиями записи из таблицы users и складывать их в другую таблицу, можно попробовать поднять копию таблицы из дампа где-то, ну или поработать со слейв сервером, НО я решил
сделать дамп таблицы в XML файл и уже из файла читать данные. Выбрал этот способ как наиболее быстрый и простой.

```bash
mysqldump --xml -u root -p database users > data.xml
```

Сам скрипт лежит тут **/src/task2/EmailDomainParser.php** тесты тут **/tests/EmailDomainParserTest.php**

На выходе получаем массив, где ключ домен, а значение кол-во юзеров с таким доменом
```php
try {
    print_r((new \Hypeauditor\EmailDomainParser('test.xml'))->getData());
} catch (\Exception $e) {
    echo $e->getMessage();
}

/*
Array
(
    [csapi.ru] => 5
    [singerco.ru] => 4
    [child-toys.ru] => 2
    [pupsauto.ru] => 1
    [special-baby.ru] => 1
    [kronmedia.ru] => 1
    ...
)
*/
```

### Задача 3

_Дан текстовый файл размером 2ГБ. Напишите класс, реализующий интерфейс SeekableIterator, для чтения данного файла._


Сам скрипт лежит тут **/src/task3/FileIterator.php** тесты тут **/tests/FileIteratorTest.php**

```php
$iterator = new \Hypeauditor\FileIterator('test.xml');

$iterator->valid();
$iterator->next();
$iterator->next();
$iterator->current();
$iterator->key();

$iterator->seek(3);
$iterator->key();
```
