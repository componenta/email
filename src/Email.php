<?php

declare(strict_types=1);

namespace Componenta\Stdlib;

use InvalidArgumentException;
use JsonSerializable;
use Stringable;

final readonly class Email implements Stringable, JsonSerializable
{
    public string $value;
    public string $local;
    public string $domain;

    private string $originalLocal;

    /**
     * @throws InvalidArgumentException If the email address is empty, malformed, or too long.
     */
    public function __construct(string $email)
    {
        $email = trim($email);
        $email = preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', $email) ?? $email;

        if ($email === '') {
            throw new InvalidArgumentException('Email cannot be empty');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Invalid email format: {$email}");
        }

        if (mb_strlen($email) > 254) {
            throw new InvalidArgumentException('Email cannot exceed 254 characters');
        }

        [$local, $domain] = explode('@', $email, 2);

        if (mb_strlen($local) > 64) {
            throw new InvalidArgumentException('Email local part cannot exceed 64 characters');
        }

        if (mb_strlen($domain) > 253) {
            throw new InvalidArgumentException('Email domain cannot exceed 253 characters');
        }

        $this->originalLocal = $local;
        $this->local = mb_strtolower($local);
        $this->domain = mb_strtolower($domain);
        $this->value = $this->local . '@' . $this->domain;
    }

    public static function fromString(string $email): self
    {
        return new self($email);
    }

    public static function tryFromString(string $email): ?self
    {
        try {
            return new self($email);
        } catch (InvalidArgumentException) {
            return null;
        }
    }

    /**
     * Compares two email addresses.
     *
     * @param bool $strict When true, the original local part is compared case-sensitively.
     *                     The domain remains case-insensitive in both modes.
     */
    public function equals(self $other, bool $strict = false): bool
    {
        if ($strict) {
            return $this->originalLocal === $other->originalLocal
                && $this->domain === $other->domain;
        }

        return $this->value === $other->value;
    }

    /**
     * Returns the TLD of the domain: "example.co.uk" -> "uk".
     */
    public function tld(): string
    {
        return substr($this->domain, (int) strrpos($this->domain, '.') + 1);
    }

    /**
     * Checks whether the address belongs to the given domain.
     */
    public function isFromDomain(string $domain): bool
    {
        return $this->domain === mb_strtolower(trim($domain));
    }

    /**
     * Masks the address for logs and UI.
     */
    public function masked(): string
    {
        $length = mb_strlen($this->local);
        $visible = min($length, max(1, (int) floor($length / 3)));
        $masked = mb_substr($this->local, 0, $visible)
            . str_repeat('*', max(0, $length - $visible));

        return $masked . '@' . $this->domain;
    }

    public function jsonSerialize(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
