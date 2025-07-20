<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Foundation\Auth\User as Authenticatable;
    use Illuminate\Notifications\Notifiable;
    use Spatie\Permission\Traits\HasRoles;

    class User extends Authenticatable
    {
        use HasFactory, Notifiable, HasRoles;

        /**
         * @var list<string>
         */
        protected $fillable = [
            'name',
            'email',
            'password',
            'empresa_id',
            'activo',
            'email_verified_at',
            'provider_name', 
            'provider_id',   
        ];

        /**
         * @var list<string>
         */
        protected $hidden = [
            'password',
            'remember_token',
        ];

        /**
         * @return array<string, string>
         */
        protected function casts(): array
        {
            return [
                'email_verified_at' => 'datetime',
                'password' => 'hashed',
                'activo' => 'boolean',
            ];
        }

        public function entradas(){
            return $this->hasMany(Entrada::class);
        }

        public function empresa()
        {
            return $this->belongsTo(Empresa::class);
        }

        public function cliente()
        {
            return $this->hasOne(Cliente::class);
        }

        public function cart() {
            return $this->hasOne(Cart::class);
        }

    }
