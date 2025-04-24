<?php
namespace App\Enums;

enum TransactionStatus: string
{
    case Completed = 'completed';
    case Reversed  = 'reversed';
    case Pending   = 'pending';

    public function label(): string
    {
        return match($this) {
            self::Completed => 'Concluído',
            self::Reversed  => 'Revertido',
            self::Pending   => 'Pendente',
        };
    }
}
