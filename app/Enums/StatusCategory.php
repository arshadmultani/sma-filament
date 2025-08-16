<?php

namespace App\Enums;

enum StatusCategory: string
{
    case DRAFT = 'draft';
    case PENDING = 'pending';
    case FINALIZED = 'finalized';

    case CANCELLED = 'cancelled';

    public function isEditable(): bool
    {
        return $this === self::DRAFT;
    }

    public function getColor(): string
    {
        return match($this) {
            self::DRAFT => '#6b7280',
            self::PENDING => '#10b981',
            self::FINALIZED => '#10b981',
            self::CANCELLED => '#ef4444',
        };
    }
}