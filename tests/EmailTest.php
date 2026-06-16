<?php

declare(strict_types=1);

namespace Componenta\Stdlib\Tests;

use Componenta\Stdlib\Email;

it('normalizes valid email addresses to lowercase', function (): void {
    $email = Email::fromString(' John.Doe@Example.COM ');

    expect((string) $email)->toBe('john.doe@example.com')
        ->and($email->local)->toBe('john.doe')
        ->and($email->domain)->toBe('example.com')
        ->and($email->jsonSerialize())->toBe('john.doe@example.com');
});

it('rejects invalid email addresses and offers a nullable constructor', function (): void {
    expect(fn () => Email::fromString('not-an-email'))->toThrow(\InvalidArgumentException::class)
        ->and(Email::tryFromString('not-an-email'))->toBeNull();
});

it('exposes domain helpers and a masked representation', function (): void {
    $email = Email::fromString('reader@example.co.uk');

    expect($email->isFromDomain('EXAMPLE.CO.UK'))->toBeTrue()
        ->and($email->tld())->toBe('uk')
        ->and($email->masked())->toBe('re****@example.co.uk');
});
