# Componenta Email

Иммутабельный объект значения для email-адреса: валидация, нормализация, маскирование, методы домена и JSON/string сериализация.

Используйте его на application boundaries, где validated email address должен быть typed value, а не raw string.

## Установка

```bash
composer require componenta/email
```

Требует extension `mbstring`.

## Связанные пакеты

Пакет самодостаточный и валидирует email без соседних библиотек.

| Пакет | Зачем может использоваться рядом |
|---|---|
| `componenta/validation` | Проверяет пользовательский ввод до создания `Email` в DTO или команде. |
| `componenta/auth` | Использует email как идентификатор входа, восстановления пароля или magic-link сценария. |
| `componenta/cqrs` | Команды регистрации, приглашения и рассылки могут типизировать email через этот объект значения. |

## Использование

```php
use Componenta\Stdlib\Email;

$email = Email::fromString('Ada@Example.COM');

(string) $email;        // "ada@example.com"
$email->local;          // "ada"
$email->domain;         // "example.com"
$email->isFromDomain('example.com'); // true
$email->masked();       // "ad*@example.com"
```

## Валидация

`new Email()` и `Email::fromString()` выбрасывают `InvalidArgumentException`, когда:

- address пустой
- address не проходит `FILTER_VALIDATE_EMAIL`
- весь address длиннее 254 символов
- local part длиннее 64 символов
- domain длиннее 253 символов

Используйте `Email::tryFromString()`, если invalid input должен возвращать `null`.

## Normalization

Constructor trim-ит whitespace, удаляет invisible control characters и приводит local/domain parts к lowercase. Stored `value`, `__toString()` и `jsonSerialize()` возвращают normalized address.

## Helpers

- `equals()` сравнивает normalized addresses
- `tld()` возвращает последний сегмент domain
- `isFromDomain()` выполняет case-insensitive domain comparison
- `masked()` возвращает безопасное для logs/UI представление
