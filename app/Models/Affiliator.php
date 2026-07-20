<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Affiliator extends Model
{

    public function user()
{
    return $this->belongsTo(User::class);
}

    public function province() {

    return $this->belongsTo(Province::class);
}

    public function city() {

    return $this->belongsTo(City::class);
}

    public function district() {

    return $this->belongsTo(District::class);
}

    public function subDistrict() {

    return $this->belongsTo(SubDistrict::class);
}

    public function postalCode()
{
    return $this->belongsTo(PostalCode::class, 'postal_code_id');
}

    public function projects()
{
    return $this->hasMany(Project::class);
}

public function scopeMembership($query, $level)
    {
        return $query->where('membership', $level);
    }

    /**
     * 🔹 Accessor: Format membership jadi huruf besar pertama (Silver, Gold, Platinum)
     */
    public function getMembershipFormattedAttribute()
    {
        return ucfirst($this->membership);
    }

    public function getDisplayNameAttribute()
    {
        return $this->user?->fullname;
    }

    /**
     * 🔹 Accessor: Status aktif/nonaktif readable
     */
    // public function getStatusTextAttribute()
    // {
    //     return $this->is_active ? 'Aktif' : 'Nonaktif';
    // }

    public static function generateNia()
    {
        $lastNumber = self::selectRaw("MAX(CAST(SUBSTRING(nia, 3) AS INTEGER)) as max_nia")->value('max_nia');
        $newNumber = ($lastNumber ?? 0) + 1;

        return 'A-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    public static function getDefaultAttributes($user)
    {
        return [
            'nia' => self::generateNia(),
            'membership' => 2,
            'saldo' => 0,
        ];
    }

    public function getReadableMembershipAttribute()
    {
    return [
        1 => 'Putih',
        2 => 'Biru',
        3 => 'Merah',
    ][$this->membership] ?? 'Tidak diketahui';
    }

    use HasFactory, HasUuids;

    protected $table = 'affiliators';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'nia',
        'category',
        'membership',
        'saldo',
    ];
}
