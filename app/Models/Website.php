<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Website extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'domain',
        'slug',
        'logo',
        'logo_width',
        'logo_height',
        'guest_list_button_text',
        'package_button_text',
        'transportation_confirmation_text',
        'visa_logo',
        'mastercard_logo',
        'amex_logo',
        'google_pay_logo',
        'apple_pay_logo',
        'refundable_fee',
        'gratuity_fee',
        'font_color',
        'text_description',
        'description_label',
        'status',
        'is_archieved',
        'location',
        'lat',
        'long',
        'phone',
        'email',
        'emails',
        'gratuity_name',
        'service_charge_name',
        'sales_tax_name',
        'reservation',
        'payment_method',
        'stripe_public_key',
        'stripe_secret_key',
        'authorize_login_id',
        'authorize_transaction_key',
        'sandbox_mode',
        'success_page',
        'policy',
        'terms',
        'footer_text',
    ];
   
    /**
     * Get the packages for the website.
     */
    public function packages()
    {
        return $this->hasMany(Package::class);
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function emails()
    {
        return $this->hasMany(Email::class, 'website_id', 'id');
    }

    public function smtp()
    {
        return $this->hasOne(SMTP::class, 'website_id', 'id');
    }

    public function paymentLogos()
    {
        return $this->hasMany(PaymentLogo::class)->where('is_active', true)->orderBy('order');
    }

    /**
     * Generate a unique slug from the website name.
     */
    public static function generateSlug($name, $id = null)
    {
        $slug = \Illuminate\Support\Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        // Check if slug exists, if so append number
        while (true) {
            $query = self::where('slug', $slug);
            if ($id) {
                $query->where('id', '!=', $id);
            }
            
            if (!$query->exists()) {
                break;
            }
            
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
