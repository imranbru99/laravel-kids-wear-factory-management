<?php

declare(strict_types=1);

namespace App\Modules\KidsWear\Support;

final class Status
{
    public const DRAFT = 'draft';

    public const SAMPLING = 'sampling';

    public const APPROVED = 'approved';

    public const PUBLISHED = 'published';

    public const CONFIRMED = 'confirmed';

    public const IN_PRODUCTION = 'in_production';

    public const SHIPPED = 'shipped';

    public const CLOSED = 'closed';

    public const CANCELLED = 'cancelled';

    public const PLANNED = 'planned';

    public const CUTTING = 'cutting';

    public const SEWING = 'sewing';

    public const FINISHING = 'finishing';

    public const PACKING = 'packing';

    public const COMPLETED = 'completed';

    public const DELAYED = 'delayed';

    public const PASS = 'pass';

    public const FAIL = 'fail';

    public const HOLD = 'hold';

    public const SENT = 'sent';

    public const PARTIAL = 'partial';

    public const RECEIVED = 'received';

    public const ISSUED = 'issued';

    public const PAID = 'paid';

    public const ACTIVE = 'active';

    public const PENDING = 'pending';

    public const REJECTED = 'rejected';

    public const RUNNING = 'running';

    public const IDLE = 'idle';

    public const BREAKDOWN = 'breakdown';

    public const NEW = 'new';

    public const OPEN = 'open';

    public const IN_PROCESS = 'in_process';

    public const PRESENT = 'PRESENT';

    public const LATE = 'LATE';

    public const ABSENT = 'ABSENT';

    public const HALF_DAY = 'HALF_DAY';

    /** @return list<string> */
    public static function productionStages(): array
    {
        return [self::CUTTING, self::SEWING, self::FINISHING, self::PACKING];
    }
}
