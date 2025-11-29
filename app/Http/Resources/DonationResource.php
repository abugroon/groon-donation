<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Donation */
class DonationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'project_id' => $this->project_id,
            'donor_name' => $this->donor_name,
            'amount' => (float) $this->amount,
            'anonymous' => (bool) $this->anonymous,
            'method' => $this->method,
            'status' => $this->status,
            'bank_account_id' => $this->bank_account_id,
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
