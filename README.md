# Componenta Email

Immutable email address value object with validation, normalization, masking, domain helpers, and JSON/string serialization.

Use it at application boundaries where a validated email address should be represented as a typed value instead of a raw string.

## Installation

```bash
composer require componenta/email
```

Requires the `mbstring` extension.

## Related Packages

This package validates email values without neighboring Componenta packages.

| Package | Why it may be used nearby |
|---|---|
| `componenta/validation` | Validates user input before creating `Email`. |
| `componenta/auth` | Can use email for login, password reset, or magic-link flows. |
| `componenta/cqrs` | Commands can type email fields with this value object. |

## Usage

```php
use Componenta\Stdlib\Email;

$email = Email::fromString('Ada@Example.COM');

(string) $email;        // "ada@example.com"
$email->local;          // "ada"
$email->domain;         // "example.com"
$email->isFromDomain('example.com'); // true
$email->masked();       // "ad*@example.com"
```

## Validation

`new Email()` and `Email::fromString()` throw `InvalidArgumentException` when:

- the address is empty
- the address is not accepted by `FILTER_VALIDATE_EMAIL`
- the full address exceeds 254 characters
- the local part exceeds 64 characters
- the domain exceeds 253 characters

Use `Email::tryFromString()` when invalid input should produce `null`.

## Normalization

The constructor trims whitespace, removes invisible control characters, and lowercases both local and domain parts. The stored `value`, `__toString()`, and `jsonSerialize()` all return the normalized address.

## Helpers

- `equals()` compares normalized addresses
- `tld()` returns the last domain segment
- `isFromDomain()` performs case-insensitive domain comparison
- `masked()` returns a log/UI-safe representation
