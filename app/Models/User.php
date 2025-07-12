<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Filament\Panel;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\LogOptions;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Storage;
use App\Notifications\CustomVerifyEmail;
use Filament\Models\Contracts\HasAvatar;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Jeffgreco13\FilamentBreezy\Traits\TwoFactorAuthenticatable;


class User extends Authenticatable implements FilamentUser, HasAvatar, MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, TwoFactorAuthenticatable, HasApiTokens, LogsActivity, HasPanelShield;

    // public function sendEmailVerificationNotification()
    // {
    //     $this->notify(new CustomVerifyEmail);
    // }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar_url',
        'cover_photo_url',
        'username',
        'phone',
        'bio',
        'role',
        'profession',
        'is_verified',
        'point',
        'last_login_at',
        'email_verified_at',
        'preference',
        'price',
        'lifetime_price',
        'social_media',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_verified' => 'boolean',
            'point' => 'integer',
            'last_login_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'social_media' => 'array',
        ];
    }

    //TODO manipulasi respon buat url biar pake full urlnya lgsg di responnya

    // === Relationships ===
    // User yang saya follow
    public function followings()
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'followed_id')->withTimestamps();
    }

    // User yang mem-follow saya
    public function followers()
    {
        return $this->belongsToMany(User::class, 'follows', 'followed_id', 'follower_id')->withTimestamps();
    }

    // User Class content
    public function userCompletedContents()
    {
        return $this->hasMany(UserClassContents::class);
    }

    public function bookmarks()
    {
        return $this->hasMany(Bookmark::class);
    }

    public function bookmarked()
    {
        return $this->morphMany(Bookmark::class, 'bookmarkable');
    }

    public function classesTaught()
    {
        return $this->hasMany(ClassModel::class, 'mentor_id');
    }

    public function enrollments()
    {
        return $this->morphMany(Enrollment::class, 'enrollable');
    }

    public function subscriptions()
    {
        return $this->hasMany(UserSubscription::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function points()
    {
        return $this->hasMany(Point::class);
    }

    public function userMilestones()
    {
        return $this->hasMany(UserMilestone::class);
    }

    public function userBadges()
    {
        return $this->hasMany(UserBadge::class);
    }

    public function badges()
    {
        return $this->belongsToMany(Badge::class, 'user_badges')
            ->withPivot('earned_at')
            ->withTimestamps();
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    public function feedbacks()
    {
        return $this->hasMany(Feedback::class);
    }

    public function supportTickets()
    {
        return $this->hasMany(SupportTicket::class);
    }

    public function ticketResponses()
    {
        return $this->hasMany(TicketResponse::class, 'responder_id');
    }

    public function eventAttendances()
    {
        return $this->hasMany(EventAttendee::class);
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    public function promoUsages()
    {
        return $this->hasMany(PromoUsage::class);
    }

    public function commissionSetting()
    {
        return $this->hasOne(CommissionSetting::class, 'mentor_id');
    }

    public function commissionEarnings()
    {
        return $this->hasMany(CommissionEarning::class, 'mentor_id');
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class, 'mentor_id');
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class, 'mentor_id');
    }


    // =============

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnly([
                'name',
                'email',
                'password',
                'avatar_url',
                'username',
                'phone',
                'bio',
                'role',
                'profession',
                'is_verified',
                'point',
                'last_login_at',
                'email_verified_at',
                'preference',
                'mentor_subscription_price',
                'social_media'
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function getFilamentAvatarUrl(): ?string
    {
        $avatar = $this->getAttributes()['avatar_url'] ?? null;

        if (!$avatar) {
            return null;
        }

        if (Str::startsWith($avatar, ['http://', 'https://'])) {
            return $avatar;
        }

        return Storage::url($avatar);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return ($this->hasAnyRole(['super_admin', 'mentor']) || in_array($this->role, ['admin', 'mentor']));
        return true;
    }
}
