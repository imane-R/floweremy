<?php

namespace App\Enum;

enum OrderStatus: string
{
    case PENDING = 'pending';
    case CANCELLED = 'cancelled';
    case CONFIRMED = 'confirmed';
    case COMPLETED = 'completed';

    public static function fromString(string $status): self
    {
        return match ($status) {
            'pending' => self::PENDING,
            'cancelled' => self::CANCELLED,
            'confirmed' => self::CONFIRMED,
            'completed' => self::COMPLETED,
            default => throw new \InvalidArgumentException("Invalid order status: $status"),
        };
    }
}
