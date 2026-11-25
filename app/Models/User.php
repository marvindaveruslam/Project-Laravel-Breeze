<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'role',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
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
        ];
    }

    // ------------------------------------------------------------------
    // Peran & hak akses
    // ------------------------------------------------------------------
    public const ROLE_ADMIN = 'admin';
    public const ROLE_OPERATOR = 'operator';
    public const ROLE_WALIKELAS = 'walikelas';
    public const ROLE_GURU = 'guru';

    /** Admin & operator: akses penuh seperti sebelumnya */
    public function isAdminLike(): bool
    {
        return in_array($this->role, [self::ROLE_ADMIN, self::ROLE_OPERATOR], true);
    }

    /** Walikelas & guru mapel (akses terbatas) */
    public function isTeacher(): bool
    {
        return in_array($this->role, [self::ROLE_WALIKELAS, self::ROLE_GURU], true);
    }

    public function guru()
    {
        return $this->hasOne(Guru::class);
    }

    private ?array $walikelasKelasIdsCache = null;
    private ?array $teachingKelasIdsCache = null;

    /** ID kelas yang diwalikan oleh user ini */
    public function walikelasKelasIds(): array
    {
        if ($this->walikelasKelasIdsCache !== null) {
            return $this->walikelasKelasIdsCache;
        }

        $guruId = $this->guru()->value('id');

        return $this->walikelasKelasIdsCache = $guruId
            ? array_map('intval', Kelas::where('walikelas_id', $guruId)->pluck('id')->all())
            : [];
    }

    /** ID kelas tempat user ini mengajar mapel (tabel kelas_guru) */
    public function teachingKelasIds(): array
    {
        if ($this->teachingKelasIdsCache !== null) {
            return $this->teachingKelasIdsCache;
        }

        $guruId = $this->guru()->value('id');

        return $this->teachingKelasIdsCache = $guruId
            ? \Illuminate\Support\Facades\DB::table('kelas_guru')->where('guru_id', $guruId)->pluck('kelas_id')->map(fn ($v) => (int) $v)->all()
            : [];
    }

    /**
     * Mata pelajaran yang boleh dinilai user ini pada siswa tertentu.
     * null  = semua mapel (admin/operator/walikelas kelas tsb)
     * array = hanya mapel yang diajarkannya di kelas siswa tsb
     */
    public function gradableSubjects(Student $student): ?array
    {
        if ($this->isAdminLike() || in_array((int) $student->kelas_id, $this->walikelasKelasIds(), true)) {
            return null;
        }

        $guru = $this->guru;
        if (! $guru || ! in_array((int) $student->kelas_id, $this->teachingKelasIds(), true)) {
            return [];
        }

        $pivot = \Illuminate\Support\Facades\DB::table('kelas_guru')
            ->where('guru_id', $guru->id)
            ->where('kelas_id', $student->kelas_id)
            ->pluck('mata_pelajaran')
            ->filter()
            ->all();

        $subjects = $pivot ?: array_filter([$guru->mata_pelajaran]);

        return array_values(array_unique(array_map('trim', $subjects)));
    }
}
