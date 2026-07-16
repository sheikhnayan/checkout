<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Support\WebsiteTimezone;

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
        'google_analytics_id',
        'slug',
        'logo',
        'logo_width',
        'logo_height',
        'guest_list_button_text',
        'guest_tab_subtitle',
        'guest_tab_color',
        'guest_tab_icon',
        'package_button_text',
        'package_tab_subtitle',
        'package_tab_color',
        'package_tab_icon',
        'package_tab_ribbon',
        'package_section_title',
        'package_section_subtext',
        'transportation_confirmation_text',
        'operating_days',
        'operating_start_time',
        'operating_end_time',
        'pickup_start_time',
        'pickup_end_time',
        'visa_logo',
        'mastercard_logo',
        'amex_logo',
        'google_pay_logo',
        'apple_pay_logo',
        'refundable_fee',
        'refundable_name',
        'gratuity_fee',
        'gratuity_name',
        'sales_tax_fee',
        'sales_tax_name',
        'service_charge_fee',
        'service_charge_name',
        'processing_fee',
        'processing_fee_type',
        'withdraw_charge',
        'commission_hold_days',
        'commission_hold_days_authorize',
        'promo_code_name',
        'font_color',
        'text_description',
        'description_label',
        'hero_title',
        'hero_subtitle',
        'hero_badge_1_label',
        'hero_badge_1_sub',
        'hero_badge_2_label',
        'hero_badge_2_sub',
        'secondary_description',
        'gallery_images',
        'status',
        'is_archieved',
        'location',
        'lat',
        'long',
        'phone',
        'dispatcher_phone',
        'email',
        'timezone',
        'show_contact_info',
        'entertainer_submission_emails',
        'clublifter_enabled',
        'emails',
        'reservation',
        'payment_method',
        'stripe_app_key',
        'stripe_secret_key',
        'stripe_public_key',
        'authorize_app_key',
        'authorize_secret_key',
        'authorize_login_id',
        'authorize_transaction_key',
        'sandbox_mode',
        'success_page',
        'policy',
        'terms',
        'footer_text',
    ];

    protected $casts = [
        'gallery_images' => 'array',
        'operating_days' => 'array',
        'show_contact_info' => 'boolean',
        'entertainer_submission_emails' => 'array',
        'clublifter_enabled' => 'boolean',
    ];
   
    /**
     * Get the packages for the website.
     */
    public function packages()
    {
        return $this->hasMany(Package::class);
    }

    public function packageCategories()
    {
        return $this->hasMany(PackageCategory::class);
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

    public function checkoutPopups()
    {
        return $this->hasMany(CheckoutPopup::class)->latest();
    }

    public function feedModels()
    {
        return $this->hasMany(FeedModel::class)->latest();
    }

    public function feedPosts()
    {
        return $this->hasMany(FeedPost::class)->latest('posted_at');
    }

    public function getResolvedTimezoneAttribute(): string
    {
        return WebsiteTimezone::forWebsite($this);
    }

    public function getTimezoneLabelAttribute(): string
    {
        return WebsiteTimezone::label($this->resolved_timezone);
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
