
# Что сделано

* Валидация основана на пользовательских calback функциях.
* В видимости calback ф-ции есть доступ к public полям класса формы реализующей AbstractForm,
есть доступ к текущему состоянию валидации отработанных фильтров, 
также есть возможность узнать исход валидации конкретных ранее проинициализированных фильтров.

* Наследники класса AbstractValidator могут реализовать логику обработки результатов набора фильтров.
 
    * К примеру: класс FilterAnd считает результ успешным если все фильтры прошли валидацию,
а класс FilterOr считает результат успешным в случае любого успешного завершения фильтра.

* Предусмотрен случай вызова циклических ссылок и ограничен синтаксис пользовательской callback функции за счет класса BuildFilter.


### Пример построения фильтров валидации.
```php

     // Построитель фильтра создается с именем поля и текстом в случае ошибки и успеха
        $build_login1 = new BuildFilter("login", "сообщение в случае false", "сообщение в случае true");
        $build_login1->addWork(function () {
            // работа с полем
            // для успешного прохождения валидации вернуть true, иначе false
            return true;
        });

        $build_login2 = new BuildFilter("login", "сообщение в случае false");
        $build_login2->addWork(function (AbstractValidator $worker) {
            // работа с полем
            // $worker->currentValidate() доступ к текущему ходу валидации, возвращает bool
            return true;
        });

        // FilterAnd фильтр будет ожидать успеха всех фильтров
        $filter_login = new FilterAnd();
        $filter_login->addFilter($build_login1);
        $filter_login->addFilter($build_login2);

        // обьект ManagerValidator управляет ходом выполнения валидации и содержит итоговый результат, возвращает bool
        $this->manager_validator->add($filter_login);


        // Создадим еще один фильтр для OR логики

        $build_name = new BuildFilter("name", "сообщение в случае false");
        $build_name->addWork(function (AbstractValidator $worker) use($filter_login) {
            // работа с полем
            // $filter_login->isValidate(); доступ к результату выполнения конкретного фильтра, возвращает bool
            return true;
        });

        $build_name2 = new BuildFilter("name", "сообщение в случае false");
        $build_name2->addWork(function (AbstractValidator $worker) {
            // работа с полем
            return true;
        });


        $filter_name = new FilterOr();
        $filter_name->addFilter($build_name);
        $filter_name->addFilter($build_name2);

        $this->manager_validator->add($filter_name);

```

## Не сделано
* Файлы js разбросаны по коду
* Роутин грязный
* Дублирование кода в js файлах
* Тесты js не написаны 