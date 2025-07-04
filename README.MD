# RUMAH IT HUB - Documentation Teknis

## Daftar Isi
1. [Pendahuluan](#pendahuluan)
2. [Struktur Database](#struktur-database)
3. [Modul Sistem](#modul-sistem)
4. [Relasi Antar Entitas](#relasi-antar-entitas)
5. [Implementasi Teknis](#implementasi-teknis)
6. [Syntax ERD](#syntax-erd)
7. [Dash Fitur for Admin](#️-sidebar-menu---role-admin)
8. [Dash Fitur for Mentor](#-sidebar-menu---role-mentor)
9. [API Docs](#api-documentation)

## Pendahuluan

Dokumen ini menjelaskan struktur database dan modul-modul yang dapat diimplementasikan dalam platform e-learning berdasarkan Entity-Relationship Diagram (ERD) yang telah dirancang. Platform ini mencakup fitur manajemen pengguna, pembelajaran, e-commerce, gamifikasi, dan berbagai fitur penunjang lainnya.

## Struktur Database

Database terdiri dari entitas-entitas utama yang saling berhubungan membentuk sistem e-learning yang komprehensif. Berikut adalah pengelompokan tabel-tabel berdasarkan fungsi utamanya:

### Manajemen Pengguna
- `users` - Menyimpan data utama pengguna
- `social_accounts` - Integrasi akun sosial media
- `audit_logs` - Pencatatan aktivitas pengguna

### Struktur Konten
- `categories` - Kategori pembelajaran utama
- `sub_categories` - Sub-kategori pembelajaran
- `classes` - Kelas-kelas pembelajaran
- `educational_contents` - Konten pembelajaran (video, PDF, quiz)
- `webinar_recordings` - Rekaman webinar

### Sistem E-Commerce
- `bundles` - Paket langganan/pembelian
- `bundle_items` - Item-item dalam paket
- `orders` - Transaksi pembelian
- `order_items` - Item dalam transaksi
- `promos` - Kode promosi dan diskon
- `promo_usages` - Penggunaan kode promo

### Akses & Langganan
- `user_subscriptions` - Langganan pengguna
- `enrollments` - Pendaftaran kelas

### Gamifikasi & Pencapaian
- `points` - Sistem poin pengguna
- `badges` - Badge pencapaian
- `user_badges` - Badge yang diperoleh pengguna
- `milestones` - Milestone dalam kelas
- `user_milestones` - Milestone yang dicapai pengguna
- `certificates` - Sertifikat penyelesaian kelas

### Dukungan & Feedback
- `feedbacks` - Ulasan dan rating kelas
- `support_tickets` - Tiket bantuan
- `ticket_responses` - Respon terhadap tiket

### Acara & Pemasaran
- `events` - Acara dan webinar
- `event_attendees` - Peserta acara
- `banners` - Banner promosi
- `notifications` - Pemberitahuan sistem

## Modul Sistem

Berdasarkan struktur database, platform dapat diimplementasikan dengan modul-modul berikut:

### 1. Modul Manajemen Pengguna
**Tabel terkait**: `users`, `social_accounts`, `user_badges`

**Fungsionalitas**:
- Registrasi dan otentikasi pengguna
- Manajemen profil (biodata, foto profil, dll)
- Integrasi dengan platform sosial media
- Manajemen peran (admin, mentor, student)
- Verifikasi akun
- Pemantauan aktivitas pengguna

**Relasi ORM**:
- User memiliki banyak akun sosial (one-to-many)
- User dapat memiliki banyak badge (many-to-many via user_badges)
- User dapat menghasilkan banyak log aktivitas (one-to-many)

### 2. Modul Manajemen Konten Pembelajaran
**Tabel terkait**: `categories`, `sub_categories`, `classes`, `educational_contents`

**Fungsionalitas**:
- Pengorganisasian materi pembelajaran
- Pembuatan dan pengelolaan kelas
- Pengunggahan dan penyusunan konten edukasi
- Pencarian dan filtering konten

**Relasi ORM**:
- Kategori memiliki banyak subkategori (one-to-many)
- Subkategori memiliki banyak kelas (one-to-many)
- Kelas memiliki banyak konten edukasi (one-to-many)
- Mentor (user) membuat banyak kelas (one-to-many)

### 3. Modul E-Commerce & Pembayaran
**Tabel terkait**: `bundles`, `bundle_items`, `orders`, `order_items`, `promos`, `promo_usages`

**Fungsionalitas**:
- Pembuatan dan pengelolaan paket pembelajaran
- Pemrosesan pembayaran
- Integrasi dengan payment gateway
- Manajemen kode promo dan diskon
- Pelacakan transaksi

**Relasi ORM**:
- Bundle terhubung ke berbagai item melalui bundle_items (one-to-many)
- Order memiliki banyak item pesanan (one-to-many)
- Promo dapat digunakan dalam banyak pesanan (one-to-many)
- User membuat banyak pesanan (one-to-many)

### 4. Modul Langganan & Akses Pengguna
**Tabel terkait**: `user_subscriptions`, `enrollments`

**Fungsionalitas**:
- Pendaftaran kelas
- Pelacakan progress pembelajaran
- Manajemen langganan dan akses
- Pengaturan masa aktif langganan

**Relasi ORM**:
- User dapat memiliki banyak langganan (one-to-many)
- Langganan memberikan akses ke berbagai kelas melalui enrollments
- Kelas memiliki banyak pendaftaran (one-to-many)

### 5. Modul Gamifikasi & Pencapaian
**Tabel terkait**: `points`, `badges`, `user_badges`, `milestones`, `user_milestones`

**Fungsionalitas**:
- Sistem perolehan poin
- Perolehan badge berdasarkan pencapaian
- Pelacakan milestone pembelajaran
- Leaderboard dan ranking

**Relasi ORM**:
- User mengumpulkan poin dari berbagai aktivitas (one-to-many)
- User mendapatkan badge berdasarkan kriteria tertentu (many-to-many)
- User mencapai milestone dalam kelas (many-to-many)

### 6. Modul Sertifikasi
**Tabel terkait**: `certificates`, `enrollments`

**Fungsionalitas**:
- Penerbitan sertifikat otomatis
- Verifikasi sertifikat dengan kode unik
- Repositori sertifikat pengguna

**Relasi ORM**:
- Sertifikat terhubung ke enrollment kelas yang sudah selesai
- Sertifikat dimiliki oleh user tertentu (many-to-one)

### 7. Modul Umpan Balik & Dukungan
**Tabel terkait**: `feedbacks`, `support_tickets`, `ticket_responses`

**Fungsionalitas**:
- Pemberian rating dan ulasan kelas
- Pembuatan dan pelacakan tiket dukungan
- Respons dan penyelesaian masalah

**Relasi ORM**:
- User memberikan feedback untuk kelas (many-to-many)
- User membuat tiket dukungan (one-to-many)
- Tiket mendapatkan banyak respons (one-to-many)

### 8. Modul Acara & Webinar
**Tabel terkait**: `events`, `event_attendees`, `webinar_recordings`

**Fungsionalitas**:
- Pembuatan dan pengelolaan acara
- Pendaftaran peserta acara
- Penyimpanan dan akses rekaman webinar
- Notifikasi acara

**Relasi ORM**:
- Event terhubung ke subkategori tertentu (many-to-one)
- Event memiliki banyak peserta (one-to-many via event_attendees)
- Subkategori memiliki banyak rekaman webinar (one-to-many)

### 9. Modul Pemasaran
**Tabel terkait**: `banners`, `promos`

**Fungsionalitas**:
- Manajemen banner promosi
- Pembuatan kampanye pemasaran
- Pembuatan dan pengelolaan kode diskon
- Pemasaran terarah berdasarkan kategori pengguna

**Relasi ORM**:
- Banner dan promo dapat ditargetkan ke berbagai tipe konten
- Promo digunakan dalam banyak order (one-to-many)

### 10. Modul Notifikasi
**Tabel terkait**: `notifications`

**Fungsionalitas**:
- Pengiriman notifikasi real-time
- Pemberitahuan terkait aktivitas platform
- Preferensi notifikasi pengguna

**Relasi ORM**:
- Notifikasi terhubung ke pengguna tertentu (many-to-one)
- Notifikasi dapat mereferensikan berbagai tipe entitas

### 11. Modul Audit & Keamanan
**Tabel terkait**: `audit_logs`

**Fungsionalitas**:
- Pencatatan aktivitas sistem
- Pemantauan keamanan
- Pelacakan perubahan data penting

**Relasi ORM**:
- Log audit terhubung ke pengguna (many-to-one)
- Log menyimpan detail aktivitas dalam format JSON

## Relasi Antar Entitas

Relasi kunci dalam database:

1. **User - Class**:
   - User sebagai mentor membuat banyak kelas
   - User sebagai student mendaftar ke banyak kelas melalui enrollments

2. **Bundle - Class**:
   - Bundle dapat berisi banyak kelas melalui bundle_items
   - Bundle memberikan akses ke kelas melalui user_subscriptions

3. **Category - SubCategory - Class**:
   - Kategori memiliki banyak subkategori
   - Subkategori memiliki banyak kelas

4. **Class - Educational Content**:
   - Kelas memiliki banyak konten edukasi
   - Konten diurutkan dalam kelas melalui order_index

5. **User - Achievement**:
   - User mendapatkan poin dari berbagai aktivitas
   - User mendapatkan badge berdasarkan kriteria
   - User mencapai milestone dalam kelas

## Implementasi Teknis

### Teknologi yang Disarankan
- **Backend Framework**: Laravel/Symfony (PHP), Django/Flask (Python), atau Express.js (Node.js)
- **ORM**: Eloquent (Laravel), Doctrine (Symfony), SQLAlchemy (Python), Sequelize (Node.js)
- **Database**: MySQL, PostgreSQL
- **Caching**: Redis untuk performa kueri
- **Queue System**: RabbitMQ/Redis untuk proses asinkron (notifikasi, sertifikat, dll)

### Contoh Implementasi ORM (Pseudocode - Laravel Eloquent)

```php
// Model User
class User extends Model {

    // === Relationships ===

    public function socialAccount()
    {
        return $this->hasOne(SocialAccount::class);
    }

    public function classesTaught()
    {
        return $this->hasMany(ClassModel::class, 'mentor_id');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
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

}

// Model Class
class Class extends Model {

    // === Relationships ===


    public function mentor()
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'class_id');
    }

    public function educationalContents()
    {
        return $this->hasMany(EducationalContent::class, 'class_id');
    }

    public function feedbacks()
    {
        return $this->hasMany(Feedback::class, 'class_id');
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class, 'class_id');
    }

    public function milestones()
    {
        return $this->hasMany(Milestone::class, 'class_id');
    }

      public function scopeActive($query)
    {
        return $query->where('is_deleted', false);
    }

}

// Model Bundle
class Bundle extends Model {

    // === Relationships ===

    /**
     * Get all items inside the bundle.
     */
    public function items()
    {
        return $this->hasMany(BundleItem::class);
    }

    /**
     * Get all user subscriptions associated with this bundle.
     */
    public function subscriptions()
    {
        return $this->hasMany(UserSubscription::class);
    }

    /**
     * Scope for active bundles only.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Accessor for readable type label (optional).
     */
    public function getTypeLabelAttribute()
    {
        return match ($this->type) {
            'single_class' => 'Single Class',
            'category' => 'Category',
            'sub_category' => 'Sub-category',
            'full_access' => 'Full Access',
            'custom' => 'Custom',
            default => ucfirst($this->type),
        };
    }
}
```

### Contoh Kode Controller (Pseudocode)

```php
// EnrollmentController
class EnrollmentController {
    // Mendaftarkan user ke kelas
    public function enroll($classId) {
        $user = Auth::user();
        $class = Class::findOrFail($classId);

        // Cek apakah user memiliki akses melalui langganan
        $hasAccess = $user->subscriptions()
            ->where('status', 'active')
            ->whereHas('bundle.items', function($query) use ($classId) {
                $query->where('item_type', 'class')
                      ->where('item_id', $classId);
            })->exists();

        if (!$hasAccess) {
            return response()->json(['error' => 'No access to this class'], 403);
        }

        // Buat enrollment
        $enrollment = new Enrollment();
        $enrollment->user_id = $user->id;
        $enrollment->class_id = $classId;
        $enrollment->status = 'confirmed';
        $enrollment->save();

        return response()->json(['message' => 'Successfully enrolled']);
    }
}
```

## ERD

e-learning platform:

```
Table users {
  id int [pk, increment]
  name varchar(255)
  username varchar(50) [unique]
  email varchar(255) [unique]
  phone varchar(20)
  password varchar(255)
  profile_picture varchar(255)
  bio text
  role enum('admin', 'mentor', 'student')
  is_verified boolean [default: false]
  point int [default: 0]
  last_login_at timestamp
  created_at timestamp
  updated_at timestamp
}

Table social_accounts {
  id int [pk, increment]
  user_id int [ref: > users.id]
  provider_name varchar(50)
  provider_id varchar(100)
  avatar_url text
  created_at timestamp
  updated_at timestamp

  indexes {
    (user_id, provider_name)
  }
}

// Content Structure
Table categories {
  id int [pk, increment]
  name varchar(255) [unique]
  description text
  created_at timestamp
  updated_at timestamp
}

Table sub_categories {
  id int [pk, increment]
  category_id int [ref: > categories.id]
  name varchar(255)
  description text
  created_at timestamp
  updated_at timestamp

  indexes {
    (category_id, name) [unique]
  }
}

Table classes {
  id int [pk, increment]
  title varchar(255)
  description text
  mentor_id int [ref: > users.id]
  sub_category_id int [ref: > sub_categories.id]
  duration_minutes int
  price decimal(10,2)
  status enum('upcoming', 'ongoing', 'completed', 'canceled')
  is_deleted boolean [default: false]
  created_at timestamp
  updated_at timestamp
}

// Bundle System
Table bundles {
  id int [pk, increment]
  name varchar(255)
  description text
  type enum('single_class', 'category', 'sub_category', 'full_access', 'custom')
  price decimal(10,2)
  validity_days int [note: 'Duration in days the bundle is valid after purchase']
  is_active boolean [default: true]
  created_at timestamp
  updated_at timestamp
}

Table bundle_items {
  id int [pk, increment]
  bundle_id int [ref: > bundles.id]
  item_type enum('class', 'category', 'sub_category', 'all')
  item_id int [note: 'ID of the class, category, or sub_category, 0 for all']
  created_at timestamp
  updated_at timestamp

  indexes {
    (bundle_id, item_type, item_id) [unique]
  }
}

// Order & Payment
Table orders {
  id int [pk, increment]
  user_id int [ref: > users.id]
  total_amount decimal(10,2)
  discount_amount decimal(10,2) [default: 0]
  final_amount decimal(10,2)
  payment_method varchar(50)
  payment_provider varchar(50)
  payment_order_id varchar(100) [unique]
  payment_response json
  promo_id int [ref: > promos.id, null]
  status enum('pending', 'paid', 'failed', 'canceled')
  created_at timestamp
  updated_at timestamp
}

Table order_items {
  id int [pk, increment]
  order_id int [ref: > orders.id]
  item_type enum('class', 'bundle')
  item_id int
  price decimal(10,2)
  created_at timestamp

  indexes {
    (order_id, item_type, item_id) [unique]
  }
}

// User Subscriptions & Access
Table user_subscriptions {
  id int [pk, increment]
  user_id int [ref: > users.id]
  bundle_id int [ref: > bundles.id]
  order_id int [ref: > orders.id]
  start_date timestamp
  end_date timestamp
  status enum('active', 'expired', 'canceled')
  created_at timestamp
  updated_at timestamp

  indexes {
    (user_id, bundle_id, status)
  }
}

Table enrollments {
  id int [pk, increment]
  user_id int [ref: > users.id]
  class_id int [ref: > classes.id]
  subscription_id int [ref: > user_subscriptions.id, null]
  status enum('pending', 'confirmed', 'canceled', 'completed')
  progress int [default: 0]
  is_certificate_issued boolean [default: false]
  created_at timestamp
  updated_at timestamp

  indexes {
    (user_id, class_id) [unique]
  }
}

// Educational Content
Table educational_contents {
  id int [pk, increment]
  class_id int [ref: > classes.id]
  title varchar(255)
  type enum('video', 'pdf', 'quiz')
  order_index int
  content_path text
  created_at timestamp
  updated_at timestamp
}

Table webinar_recordings {
  id int [pk, increment]
  sub_category_id int [ref: > sub_categories.id]
  title varchar(255)
  video_url text
  duration_minutes int
  recorded_at datetime
  created_at timestamp
  updated_at timestamp
}

// Achievements & Gamification
Table milestones {
  id int [pk, increment]
  class_id int [ref: > classes.id]
  title varchar(255)
  description text
  required_progress_percentage int
  created_at timestamp
  updated_at timestamp
}

Table user_milestones {
  id int [pk, increment]
  user_id int [ref: > users.id]
  milestone_id int [ref: > milestones.id]
  achieved_at timestamp

  indexes {
    (user_id, milestone_id) [unique]
  }
}

Table points {
  id int [pk, increment]
  user_id int [ref: > users.id]
  source enum('join_class', 'testimoni', 'buy_bundle', 'event', 'other')
  description varchar(255)
  point_value int
  created_at timestamp
}

Table badges {
  id int [pk, increment]
  name varchar(255)
  description text
  icon varchar(255)
  rule_type enum('classes_joined', 'feedbacks_given', 'points_reached')
  rule_value int
  created_at timestamp
}

Table user_badges {
  id int [pk, increment]
  user_id int [ref: > users.id]
  badge_id int [ref: > badges.id]
  earned_at timestamp

  indexes {
    (user_id, badge_id) [unique]
  }
}

// Certificates
Table certificates {
  id int [pk, increment]
  user_id int [ref: > users.id]
  class_id int [ref: > classes.id]
  certificate_code varchar(100) [unique]
  certificate_url varchar(255)
  issued_at timestamp

  indexes {
    (user_id, class_id) [unique]
  }
}

// Promos & Discounts
Table promos {
  id int [pk, increment]
  name varchar(255)
  code varchar(50) [unique]
  description text
  discount_type enum('fixed', 'percentage')
  discount_value decimal(10,2)
  applies_to enum('class', 'bundle', 'recording', 'all')
  target_id int [note: 'ID of specific class, bundle, etc., 0 for all']
  min_purchase decimal(10,2) [default: 0]
  max_discount decimal(10,2) [note: 'Maximum discount amount for percentage discounts']
  max_usage_total int [note: 'Total times this promo can be used']
  max_usage_per_user int [default: 1]
  start_date datetime
  end_date datetime
  is_active boolean [default: true]
  created_at timestamp
  updated_at timestamp
}

Table promo_usages {
  id int [pk, increment]
  promo_id int [ref: > promos.id]
  user_id int [ref: > users.id]
  order_id int [ref: > orders.id]
  discount_amount decimal(10,2)
  used_at timestamp

  indexes {
    (promo_id, user_id, order_id) [unique]
  }
}

// Feedback & Support
Table feedbacks {
  id int [pk, increment]
  user_id int [ref: > users.id]
  class_id int [ref: > classes.id]
  rating decimal(2,1)
  comment text
  is_approved boolean [default: false]
  anonymous boolean [default: false]
  created_at timestamp
  updated_at timestamp
}

Table support_tickets {
  id int [pk, increment]
  user_id int [ref: > users.id]
  subject varchar(255)
  description text
  status enum('open', 'in_progress', 'closed', 'resolved')
  created_at timestamp
  updated_at timestamp
}

Table ticket_responses {
  id int [pk, increment]
  ticket_id int [ref: > support_tickets.id]
  responder_id int [ref: > users.id]
  response text
  created_at timestamp
}

// Marketing & Events
Table events {
  id int [pk, increment]
  sub_category_id int [ref: > sub_categories.id]
  title varchar(255)
  description text
  start_time datetime
  end_time datetime
  max_participants integer
  price decimal(10,2)
  status enum('upcoming','ongoing','completed','canceled')
  image_url string
  is_done bool
  created_at timestamp
  updated_at timestamp
}

Table event_attendees {
  id int [pk, increment]
  event_id int [ref: > events.id]
  user_id int [ref: > users.id]
  status enum('attending', 'interested', 'not_interested')
  created_at timestamp

  indexes {
    (event_id, user_id) [unique]
  }
}

Table banners {
  id int [pk, increment]
  title varchar(255)
  subtitle varchar(255)
  image_url varchar(255)
  target_type enum('class', 'bundle', 'category', 'event', 'promo', 'external')
  target_id int [null]
  link_url varchar(255)
  start_date datetime
  end_date datetime
  is_active boolean [default: true]
  order_index int
  created_at timestamp
  updated_at timestamp
}

// System & Activity
Table notifications {
  id int [pk, increment]
  user_id int [ref: > users.id]
  title varchar(255)
  message text
  type enum('event', 'announcement', 'general', 'class', 'payment')
  status enum('read', 'unread') [default: 'unread']
  sent_at timestamp
  created_at timestamp
  updated_at timestamp

  indexes {
    (user_id, status)
  }
}

Table audit_logs {
  id int [pk, increment]
  user_id int [ref: > users.id]
  action varchar(255)
  details json
  ip_address varchar(45)
  created_at timestamp

  indexes {
    (user_id, created_at)
  }
}
```

## 🛡️ Sidebar Menu - Role: Admin

### 📚 Kelas & Bundel
- **Kelas (ClassModel)**
- **Bundel (Bundle)**
- **Item Bundel (BundleItem)**
- **Konten Edukasi (EducationalContent)**
- **Webinar Recordings**
- **Milestone**
- **User Milestone**

### 🧑‍🎓 User & Enrollments
- **Pengguna (User)**
- **Akun Sosial (SocialAccount)**
- **Pendaftaran Kelas (Enrollment)**
- **Langganan (UserSubscription)**

### 🏷️ Kategori & Sub Kategori
- **Kategori (Category)**
- **Sub Kategori (SubCategory)**

### 💰 Transaksi & Promo
- **Pesanan (Order)**
- **Item Pesanan (OrderItem)**
- **Promo**
- **Penggunaan Promo (PromoUsage)**

### 🏆 Gamifikasi
- **Poin (Point)**
- **Badge**
- **User Badge**
- **Sertifikat (Certificate)**
- **Feedback Kelas (Feedback)**

### 🎫 Dukungan
- **Tiket Bantuan (SupportTicket)**
- **Respon Tiket (TicketResponse)**

### 📅 Event
- **Event**
- **Peserta Event (EventAttendee)**

### 📢 Informasi Umum
- **Banner**
- **Notifikasi**
- **Log Aktivitas (AuditLog)**


## 🎓 Sidebar Menu - Role: Mentor

### 📚 Kelas & Edukasi
- **Kelas (ClassModel)** → hanya milik sendiri
- **Konten Edukasi (EducationalContent)**
- **Webinar Recordings**
- **Milestone**
- **Feedback Kelas**

### 👥 Peserta & Enrollments
- **Pendaftaran Kelas (Enrollment)**
- **User Milestone** *(readonly)*
- **User Subscription** *(readonly)*

### 🏆 Gamifikasi
- **User Point** *(readonly)*
- **Sertifikat** *(readonly)*


## 🧩 Catatan
- Beberapa resource diakses **readonly** oleh Mentor.

## API Documentation
- Link Documentation: https://app.swaggerhub.com/apis/rumahit/RIT/1.0.1
