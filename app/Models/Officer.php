<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Officer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /** Only officers who may still be assigned new work. */
    public function scopeActive(Builder $q): Builder
    {
        return $q->where('is_active', true);
    }

    /**
     * Officers offered in an assignment dropdown: the active ones, plus the
     * officer already saved on the record being edited — otherwise a name that
     * was deactivated after the fact would silently drop off on the next save.
     */
    public static function assignable($keepId = null)
    {
        return static::query()
            ->where(function ($q) use ($keepId) {
                $q->where('is_active', true);
                if ($keepId) {
                    $q->orWhere('id', $keepId);
                }
            })
            ->orderBy('name')
            ->get();
    }

    /** Same as assignable(), matched on the stored name instead of the id. */
    public static function assignableByName(?string $keepName = null)
    {
        return static::query()
            ->where(function ($q) use ($keepName) {
                $q->where('is_active', true);
                if ($keepName !== null && $keepName !== '') {
                    $q->orWhere('name', $keepName);
                }
            })
            ->orderBy('name')
            ->get();
    }

    public function packages()
    {
        return $this->hasMany(Package::class, 'assigned_officer_id');
    }
}
