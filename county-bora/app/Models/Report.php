<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class Report extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;
    protected $primaryKey = 'id'; 

    protected $fillable = [
        'user_id',       
        'ward_id',        // Added for geographic tracking
        'category',      
        'description',   
        'latitude',      
        'longitude',     
        'status',        
        'priority',      
        'dept_id',       
        'admin_comment', 
        'audit_remarks', 
    ];

    /**
     * Cast coordinates to high-precision decimals.
     */
    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * HELPER: Check if the report was resolved in the last 7 days.
     */
    public function isRecentlyResolved()
    {
        return $this->status === 'resolved' && 
               $this->updated_at->greaterThanOrEqualTo(Carbon::now()->subDays(7));
    }

    /**
     * ACCESSOR: Generates the NCC Tracking Number (First 8 chars of UUID).
     */
    public function getTrackingNumberAttribute()
    {
        $shortId = strtoupper(substr($this->id, 0, 8));
        return "NCC-{$shortId}";
    }

    protected static function boot()
    {
        parent::boot();

        // Auto-generate UUID for new reports
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });

        /**
         * PROFESSIONAL TRACKING: Automated Status Notifications
         */
        static::updated(function ($report) {
            if ($report->isDirty('status')) {
                
                $messages = [
                    'pending'     => "Your report has been received and is currently being processed.",
                    'dispatched'  => "Update: Your incident has been dispatched to the relevant department for action.",
                    'in_progress' => "Action Taken: A maintenance team has been assigned and is currently working on your report.",
                    'resolved'    => "Success! Your reported incident has been marked as Resolved. Thank you for using County Bora.",
                    'rejected'    => "Note: Your report could not be processed at this time. Please check the dashboard for comments."
                ];

                $statusMessage = $messages[$report->status] ?? "The status of your report has been updated to " . ucfirst($report->status);

                try {
                    \App\Models\Notification::create([
                        'user_id' => $report->user_id,
                        'title'   => "Status Update: " . $report->tracking_number,
                        'message' => $statusMessage,
                        'is_read' => false,
                    ]);
                } catch (\Exception $e) {
                    Log::error("Notification failed for Report {$report->id}: " . $e->getMessage());
                }
            }
        });
    }

    /**
     * Relationship: A report is submitted by a citizen.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: A report belongs to a specific geographic ward.
     */
    public function ward()
    {
        return $this->belongsTo(Ward::class, 'ward_id');
    }

    /**
     * Relationship: A report is assigned to a department.
     */
    public function department()
    {
        return $this->belongsTo(Department::class, 'dept_id');
    }
}