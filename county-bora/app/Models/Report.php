<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne; // Added for the rating
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
        'ward_id', 
        'title',          
        'location',       
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
     * Include the tracking number automatically in API responses
     */
    protected $appends = ['tracking_number'];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * ACCESSOR: Generates the Tracking Number for the UI
     */
    public function getTrackingNumberAttribute()
    {
        $shortId = strtoupper(substr($this->id, 0, 8));
        return "NCC-{$shortId}";
    }

    public function isRecentlyResolved()
    {
        return $this->status === 'resolved' && 
               $this->updated_at->greaterThanOrEqualTo(Carbon::now()->subDays(7));
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });

        static::updated(function ($report) {
            if ($report->isDirty('status')) {
                $messages = [
                    'pending'     => "Your report has been received and is currently being processed.",
                    'dispatched'  => "Update: Your incident has been dispatched to the relevant department for action.",
                    'in_progress' => "Action Taken: A maintenance team has been assigned and is working on your report.",
                    'resolved'    => "Success! Your reported incident has been marked as Resolved.",
                    'rejected'    => "Note: Your report could not be processed. Check dashboard for comments."
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
     * NEW: Link to the Photo References
     */
    public function media(): HasMany
    {
        return $this->hasMany(ReportMedia::class, 'report_id');
    }

    /**
     * NEW: Link to User Rating/Feedback
     */
    public function rating(): HasOne
    {
        return $this->hasOne(ReportRating::class, 'report_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ward()
    {
        return $this->belongsTo(Ward::class, 'ward_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'dept_id');
    }
}