# Блог на Symfony
Для запуска проекта необходимо выполнить следующую последовательность действий:
1. Склонировать проект  
``git clone https://github.com/Fenris-v/symfo-skill.git``
2. Установить зависимости для composer  
``php composer install``
3. Создать БД  
``php bin/console doctrine:database:create``
4. Выполнить миграции  
``php bin/console doctrine:migrations:migrate``
5. Загрузить демо данные  
``doctrine:fixtures:load``
6. Запустить окружение фреймворка  
``symfony server:start -d``
