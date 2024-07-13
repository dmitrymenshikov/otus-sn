# otus-sn

# Стек
- Symfony 7.1
- Postgres 16

# Эбаут
- Домашка для ОТУС - хайлоад архитект
- Апишка будет доступна на хосте `localhost:8051`
- Коллекция для тестирования лежит в корне `OTUS sn.postman_collection.json`

## 0. Подготовка
- Стянуть репозиторий к себе
- Переключиться с мастера на нужную ветку
- Перейти к запуску окружения

## 1. Запуск окружения
`cd docker && docker compose up -d`

## 2. Накатывание миграций
- `docker compose exec php sh`
- `bin/console doctrine:migration:migrate -n`

## 3. Кайфуем
 Чекаем роут `get:/ping` - если все ок, получим ответ `["pong"]`. Если нет - надо чето делать, пиши мне, обсудим.