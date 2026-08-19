<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Affiliate;
use App\Models\AffiliateWebsite;
use App\Models\AffiliatePackage;
use App\Models\Package;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        try {
            $affiliates = Affiliate::with('affiliateWebsites')->get();

            foreach ($affiliates as $affiliate) {
                $websiteIds = $affiliate->affiliateWebsites->where('is_active', true)->pluck('website_id')->toArray();
                if (empty($websiteIds)) {
                    continue;
                }

                $activePackages = Package::whereIn('website_id', $websiteIds)
                    ->where('status', 1)
                    ->where(function ($q) {
                        $q->whereNull('is_archieved')->orWhere('is_archieved', 0);
                    })
                    ->where(function ($q) {
                        $q->whereNull('package_category_id')
                            ->orWhereHas('category', function ($catQ) {
                                $catQ->where(function ($cq) {
                                    $cq->whereNull('is_archieved')->orWhere('is_archieved', 0);
                                });
                            });
                    })
                    ->get();

                foreach ($activePackages as $pkg) {
                    $affWeb = AffiliateWebsite::where('affiliate_id', $affiliate->id)->where('website_id', $pkg->website_id)->first();
                    $comm = $affWeb?->commission_percentage ?? $affiliate->default_commission_percentage ?? 0;

                    AffiliatePackage::updateOrCreate(
                        [
                            'affiliate_id' => $affiliate->id,
                            'package_id' => $pkg->id,
                        ],
                        [
                            'website_id' => $pkg->website_id,
                            'commission_percentage' => $comm,
                            'is_active' => true,
                        ]
                    );
                }
            }
        } catch (\Throwable $e) {
            // Log error silently if table missing during initial setup
            logger()->error('Migration sync_missing_affiliate_package_mappings failed: ' . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op
    }
};
