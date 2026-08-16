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

it('distinguishes original local-part case only in strict equality mode', function (): void {
    $upper = Email::fromString('User@example.com');
    $lower = Email::fromString('user@EXAMPLE.COM');

    expect($upper->equals($lower))->toBeTrue()
        ->and($upper->equals($lower, strict: true))->toBeFalse()
        ->and($upper->equals(Email::fromString('User@EXAMPLE.COM'), strict: true))->toBeTrue();
});

it('masks every valid local-part length without negative repeat counts', function (string $address, string $expected): void {
    expect(Email::fromString($address)->masked())->toBe($expected);
})->with([
    'one character' => ['a@b.co', 'a@b.co'],
    'two characters' => ['ab@b.co', 'ab@b.co'],
    'three characters' => ['abc@b.co', 'ab*@b.co'],
    'longer local part' => ['reader@example.com', 're****@example.com'],
]);
