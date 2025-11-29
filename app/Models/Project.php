<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Project extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'description',
        'target_amount',
        'collected_amount',
        'progress',
        'status',
        'image',
        'start_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'target_amount' => 'decimal:2',
            'collected_amount' => 'decimal:2',
            'progress' => 'decimal:2',
            'start_date' => 'date',
        ];
    }

    /**
     * Get the donations associated with the project.
     */
    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    /**
     * Approved donations only.
     */
    public function approvedDonations(): HasMany
    {
        return $this->donations()->where('status', Donation::STATUS_APPROVED);
    }

    /**
     * Project bank accounts.
     */
    public function bankAccounts(): BelongsToMany
    {
        return $this->belongsToMany(BankAccount::class, 'project_accounts')->withTimestamps();
    }

    /**
     * Refresh the collected amount, progress, and status based on approved donations.
     */
    public function refreshProgressFromDonations(): void
    {
        $totals = $this->approvedDonations()
            ->selectRaw('COALESCE(SUM(amount), 0) as total_amount, COUNT(*) as donation_count')
            ->first();

        $collected = $totals?->total_amount ?? 0;
        $progress = $this->target_amount > 0
            ? round(min(100, ($collected / $this->target_amount) * 100), 2)
            : 0;

        $status = $progress >= 100 ? 'completed' : ($progress > 0 ? 'in_progress' : 'open');

        DB::transaction(function () use ($collected, $progress, $status) {
            $this->update([
                'collected_amount' => $collected,
                'progress' => $progress,
                'status' => $status,
            ]);
        });
    }
}
