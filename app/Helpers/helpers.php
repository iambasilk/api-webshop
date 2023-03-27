<?php

function explodeFullName(string $fullName): array
{
    $nameParts = explode(' ', $fullName);
    $firstName = $nameParts[0] ?? '';
    $lastName = isset($nameParts[1]) ? implode(' ', array_slice($nameParts, 1)) : '';
    
    return [
        'firstName' => $firstName,
        'lastName' => $lastName,
    ];
}
