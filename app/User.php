<?php

namespace App;

use App\Scopes\UserScope;
use App\Observers\User as UserObserver;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;

	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'role_id', 'branch_code', 'first_name', 'last_name', 'nik', 'expires_at', 'trustee'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

	protected $appends = [
		'full_name',
		'commercial_bank_limit',
		'sales_limit',
	];

	public function getIsSuperAdministratorAttribute()
    {
		return ($this->role && (strtolower(trim($this->role->name)) === 'super administrator'));
    }

	public function getIsAdministratorAttribute()
    {
		return ($this->role && (strtolower(trim($this->role->name)) === 'administrator'));
    }

	public function getIsItSecurityAttribute()
    {
		return ($this->role && (strtolower(trim($this->role->name)) === 'it security'));
    }

	public function getIsInterbankDealerAttribute()
    {
		return ($this->role && $this->role->is_interbank_dealer);
    }

	public function getIsHeadOfficeDealerAttribute()
    {
        return ((strtolower(trim($this->branch()->firstOrNew([], ['region' => null])->region)) === 'kantor pusat') || $this->is_administrator);
    }

	public function getIsBranchOfficeDealerAttribute()
    {
		return ($this->branch_code && (strtolower(trim($this->branch()->firstOrNew([], ['region' => null])->region)) !== 'kantor pusat'));
    }

	public function getFullNameAttribute()
    {
        return (string) (
            Str::of($this->first_name)->when($this->last_name, function($string) {
                return $string->append(' ', $this->last_name);
            })
            ->title()
            ->whenEmpty( function($string) {
                return $this->email;
            })
        );
    }

	public function getStrukturCabangAttribute()
    {
        try {
            return DB::connection('sqlsrv')
                ->table('StrukturCabang')
                ->where('Id', $this->branch_code)
                ->get(['Company name as branch_name', 'NamaRegion as branch_region'])
                ->whenEmpty( function($collection) {
                    return $collection->push((object) (['branch_name' => null, 'branch_region' => null]));
                })
                ->first();

        } catch (\Exception $e) {
            return $this->branch()->firstOrNew([], ['region' => null])->toArray();
        }
    }

	public function getCommercialBankLimitAttribute()
    {
        return (($this->role_id && $this->role->limit) ? $this->role->limit->commercial_bank_limit : null);
    }

	public function getSalesLimitAttribute()
    {
		return (($this->role_id && $this->role->limit) ? $this->role->limit->sales_limit : (
			Branch::whereNotNull('sales_limit')
			->latest()
			->firstOrNew(
				[
					'code' => $this->branch_code
				],
				[
					'sales_limit' => null
				]
			)
			->sales_limit
		));
    }

	public function role()
    {
        return $this->belongsTo(Role::class);
    }

	public function token()
    {
        return $this->hasOne(Token::class);
    }

	public function branch()
    {
        return $this->hasMany(Branch::class, 'code', 'branch_code')
            ->latest('updated_at');
    }

	protected static function booted()
    {
        static::addGlobalScope(new UserScope);
        static::observe(UserObserver::class);
    }
}
