<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\NightlyReports\NrLocation;
use App\Models\NightlyReports\NrQuote;
use App\Models\NightlyReports\NrFormConfig;
use App\Models\NightlyReports\NrBenchmark;

class NightlyReportsSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed 24 Corporate Locations
        $locations = [
            ['id' => 1, 'name' => "Larry Flynt's Hustler Club Detroit", 'type' => 'Adult with Liquor', 'city' => 'Detroit', 'state' => 'MI', 'nightly_goal' => 15000, 'break_even' => 280000, 'historical_best' => 45000],
            ['id' => 2, 'name' => "Larry Flynt's Hustler Club New York", 'type' => 'Adult with Liquor', 'city' => 'New York', 'state' => 'NY', 'nightly_goal' => 35000, 'break_even' => 650000, 'historical_best' => 95000],
            ['id' => 3, 'name' => "Larry Flynt's Hustler Club New Orleans", 'type' => 'Adult with Liquor', 'city' => 'New Orleans', 'state' => 'LA', 'nightly_goal' => 20000, 'break_even' => 380000, 'historical_best' => 60000],
            ['id' => 4, 'name' => "Larry Flynt's Hustler Club Shreveport", 'type' => 'Adult with Liquor', 'city' => 'Shreveport', 'state' => 'LA', 'nightly_goal' => 12000, 'break_even' => 220000, 'historical_best' => 32000],
            ['id' => 5, 'name' => "Larry Flynt's Hustler Club St. Louis", 'type' => 'Adult with Liquor', 'city' => 'Washington Park', 'state' => 'IL', 'nightly_goal' => 25000, 'break_even' => 450000, 'historical_best' => 70000],
            ['id' => 6, 'name' => "Déjà Vu Showgirls Stockton", 'type' => 'Adult Alcohol Free', 'city' => 'Stockton', 'state' => 'CA', 'nightly_goal' => 10000, 'break_even' => 180000, 'historical_best' => 28000],
            ['id' => 7, 'name' => "Déjà Vu Showgirls Flint", 'type' => 'Adult Alcohol Free', 'city' => 'Flint', 'state' => 'MI', 'nightly_goal' => 8000, 'break_even' => 150000, 'historical_best' => 22000],
            ['id' => 8, 'name' => "Déjà Vu Showgirls Kalamazoo", 'type' => 'Adult Alcohol Free', 'city' => 'Kalamazoo', 'state' => 'MI', 'nightly_goal' => 9000, 'break_even' => 160000, 'historical_best' => 24000],
            ['id' => 9, 'name' => "Déjà Vu Showgirls Lansing", 'type' => 'Adult Alcohol Free', 'city' => 'Lansing', 'state' => 'MI', 'nightly_goal' => 11000, 'break_even' => 190000, 'historical_best' => 29000],
            ['id' => 10, 'name' => "Déjà Vu Showgirls Ypsilanti", 'type' => 'Adult Alcohol Free', 'city' => 'Ypsilanti', 'state' => 'MI', 'nightly_goal' => 10000, 'break_even' => 175000, 'historical_best' => 26000],
            ['id' => 11, 'name' => "Déjà Vu Showgirls Oklahoma", 'type' => 'Adult Alcohol Free', 'city' => 'Oklahoma City', 'state' => 'OK', 'nightly_goal' => 12000, 'break_even' => 210000, 'historical_best' => 31000],
            ['id' => 12, 'name' => "Deja Vu Tampa", 'type' => 'Adult with Liquor', 'city' => 'Tampa', 'state' => 'FL', 'nightly_goal' => 18000, 'break_even' => 320000, 'historical_best' => 48000],
            ['id' => 13, 'name' => "Little Darlings Kalamazoo", 'type' => 'Adult Alcohol Free', 'city' => 'Kalamazoo', 'state' => 'MI', 'nightly_goal' => 14000, 'break_even' => 250000, 'historical_best' => 38000],
            ['id' => 14, 'name' => "Little Darlings Oklahoma", 'type' => 'Adult Alcohol Free', 'city' => 'Oklahoma City', 'state' => 'OK', 'nightly_goal' => 16000, 'break_even' => 290000, 'historical_best' => 42000],
            ['id' => 15, 'name' => "Larry Flynt's Hustler Barely Legal", 'type' => 'Adult Alcohol Free', 'city' => 'New Orleans', 'state' => 'LA', 'nightly_goal' => 15000, 'break_even' => 270000, 'historical_best' => 39000],
            ['id' => 16, 'name' => "Cat's Meow New Orleans", 'type' => 'Bar/Night Club', 'city' => 'New Orleans', 'state' => 'LA', 'nightly_goal' => 22000, 'break_even' => 400000, 'historical_best' => 58000],
            ['id' => 17, 'name' => "Hammered Harry's", 'type' => 'Bar/Night Club', 'city' => 'Key West', 'state' => 'FL', 'nightly_goal' => 12000, 'break_even' => 210000, 'historical_best' => 30000],
            ['id' => 18, 'name' => "Paradise Lounge and Grill", 'type' => 'Bar/Night Club', 'city' => 'Ypsilanti', 'state' => 'MI', 'nightly_goal' => 9000, 'break_even' => 160000, 'historical_best' => 21000],
            ['id' => 19, 'name' => "Fantasy's Traverse City", 'type' => 'Adult Alcohol Free', 'city' => 'Traverse City', 'state' => 'MI', 'nightly_goal' => 7000, 'break_even' => 130000, 'historical_best' => 18000],
            ['id' => 20, 'name' => "Lucky's", 'type' => 'Bar/Night Club', 'city' => 'New Orleans', 'state' => 'LA', 'nightly_goal' => 8000, 'break_even' => 140000, 'historical_best' => 19000],
            ['id' => 21, 'name' => "Love Boutique Flint", 'type' => 'Boutique', 'city' => 'Flint', 'state' => 'MI', 'nightly_goal' => 5000, 'break_even' => 90000, 'historical_best' => 14000],
            ['id' => 22, 'name' => "Boutique Erotica Washington Park", 'type' => 'Boutique', 'city' => 'Washington Park', 'state' => 'IL', 'nightly_goal' => 6000, 'break_even' => 110000, 'historical_best' => 16000],
            ['id' => 23, 'name' => "Love Boutique Shreveport", 'type' => 'Boutique', 'city' => 'Shreveport', 'state' => 'LA', 'nightly_goal' => 4500, 'break_even' => 80000, 'historical_best' => 12000],
            ['id' => 24, 'name' => "Love Boutique Lansing", 'type' => 'Boutique', 'city' => 'Lansing', 'state' => 'MI', 'nightly_goal' => 5500, 'break_even' => 95000, 'historical_best' => 15000],
            ['id' => 30001, 'name' => "Larry Flynt's Hustler Club Las Vegas", 'type' => 'Adult with Liquor', 'city' => 'Las Vegas', 'state' => 'NV', 'nightly_goal' => 45000, 'break_even' => 850000, 'historical_best' => 135000],
        ];

        foreach ($locations as $loc) {
            NrLocation::updateOrCreate(
                ['id' => $loc['id']],
                [
                    'name' => $loc['name'],
                    'type' => $loc['type'],
                    'city' => $loc['city'],
                    'state' => $loc['state'],
                    'nightly_goal' => $loc['nightly_goal'],
                    'break_even' => $loc['break_even'],
                    'historical_best' => $loc['historical_best'],
                    'active' => true,
                ]
            );

            NrBenchmark::updateOrCreate(
                ['location_id' => $loc['id']],
                [
                    'historical_best' => $loc['historical_best'],
                    'break_even' => $loc['break_even'],
                ]
            );
        }

        // 2. Seed Quotes
        $quotes = [
            ['text' => "Fun is contagious — make sure your guests catch it.", 'author' => 'Reports', 'category' => 'Hospitality'],
            ['text' => "A team that communicates clearly never leaves a guest behind.", 'author' => 'Operations', 'category' => 'Teamwork'],
            ['text' => "Excellence is not an act but a habit. Build it shift by shift.", 'author' => 'Aristotle (adapted)', 'category' => 'Excellence'],
            ['text' => "When the team wins, the guest wins — and the numbers follow.", 'author' => 'Reports', 'category' => 'Leadership'],
            ['text' => "The best hospitality teams make the extraordinary look effortless.", 'author' => 'Operations', 'category' => 'Excellence'],
            ['text' => "Own your numbers, own your night.", 'author' => 'Reports', 'category' => 'Leadership'],
            ['text' => "Accountability isn't a burden; it's the foundation every great team builds on.", 'author' => 'Reports', 'category' => 'Leadership'],
            ['text' => "Great teams win together — and document it together.", 'author' => 'Operations', 'category' => 'Teamwork'],
            ['text' => "Every detail you capture is a gift to the next shift.", 'author' => 'Operations', 'category' => 'Excellence'],
            ['text' => "One accurate report tonight saves ten conversations tomorrow.", 'author' => 'Reports', 'category' => 'Operations'],
            ['text' => "The guest experience starts with the team's attitude — set the tone tonight.", 'author' => 'Operations', 'category' => 'Hospitality'],
            ['text' => "Excellence is a nightly discipline.", 'author' => 'Reports', 'category' => 'Excellence'],
            ['text' => "Hospitality shines brightest when details are handled.", 'author' => 'Reports', 'category' => 'Hospitality'],
            ['text' => "Leadership starts with accurate information.", 'author' => 'Reports', 'category' => 'Leadership'],
            ['text' => "Every venue tells a story. File it well.", 'author' => 'Operations', 'category' => 'Operations'],
            ['text' => "Count it, document it, improve it.", 'author' => 'Reports', 'category' => 'Leadership'],
            ['text' => "Clear reports create clear decisions.", 'author' => 'Operations', 'category' => 'Leadership'],
            ['text' => "Never let the fear of striking out keep you from playing the game.", 'author' => 'Babe Ruth', 'category' => 'Inspiration'],
        ];

        foreach ($quotes as $i => $q) {
            NrQuote::updateOrCreate(
                ['quote_text' => $q['text']],
                [
                    'author' => $q['author'],
                    'category' => $q['category'],
                    'sort_order' => $i + 1,
                    'active' => true,
                ]
            );
        }

        // 3. Seed Form Builder Configurations for 5 Core Forms
        $formFields = [
            'nightly' => [
                ['key' => 'locationId', 'label' => 'Club / Location', 'required' => true, 'order' => 1],
                ['key' => 'businessDate', 'label' => 'Business Date', 'required' => true, 'order' => 2],
                ['key' => 'submitterName', 'label' => 'Submitter Name', 'required' => true, 'order' => 3],
                ['key' => 'submitterEmail', 'label' => 'Submitter Email', 'required' => true, 'order' => 4],
                ['key' => 'additionalContributor', 'label' => 'Additional Contributor', 'required' => false, 'order' => 5],
                ['key' => 'netSales', 'label' => 'Net Sales ($)', 'required' => true, 'order' => 6],
                ['key' => 'nightlyGoal', 'label' => 'Nightly Goal ($)', 'required' => false, 'order' => 7],
                ['key' => 'lastYearNetSales', 'label' => 'Last Year Net Sales ($)', 'required' => false, 'order' => 8],
                ['key' => 'weeklyRunningNetSales', 'label' => 'Weekly Running Net Sales ($)', 'required' => false, 'order' => 9],
                ['key' => 'dayShiftNetSales', 'label' => 'Day Shift Net Sales ($)', 'required' => false, 'order' => 10],
                ['key' => 'voids', 'label' => 'Voids ($)', 'required' => false, 'order' => 11],
                ['key' => 'comps', 'label' => 'Comps ($)', 'required' => false, 'order' => 12],
                ['key' => 'danceDollarsSold', 'label' => 'Dance Dollars Sold ($)', 'required' => false, 'order' => 13],
                ['key' => 'danceDollarsRedeemed', 'label' => 'Dance Dollars Redeemed ($)', 'required' => false, 'order' => 14],
                ['key' => 'vipRoomsSold', 'label' => 'VIP Rooms Sold (#)', 'required' => false, 'order' => 15],
                ['key' => 'totalGuests', 'label' => 'Total Guests', 'required' => true, 'order' => 16],
                ['key' => 'paidGuests', 'label' => 'Paid Guests', 'required' => false, 'order' => 17],
                ['key' => 'freeDiscountGuests', 'label' => 'Free / Discount Guests', 'required' => false, 'order' => 18],
                ['key' => 'passesRedeemed', 'label' => 'Passes Redeemed', 'required' => false, 'order' => 19],
                ['key' => 'ipes', 'label' => 'IPEs (#)', 'required' => false, 'order' => 20],
                ['key' => 'taxiPayout', 'label' => 'Taxi / Rideshare Payout ($)', 'required' => false, 'order' => 21],
                ['key' => 'atmPayout', 'label' => 'ATM Payout ($)', 'required' => false, 'order' => 22],
                ['key' => 'otherPayouts', 'label' => 'Other Payouts ($)', 'required' => false, 'order' => 23],
                ['key' => 'deposit', 'label' => 'Actual Bank Deposit ($)', 'required' => false, 'order' => 24],
                ['key' => 'safeBalance', 'label' => 'Ending Safe Balance ($)', 'required' => false, 'order' => 25],
                ['key' => 'weather', 'label' => 'Weather', 'required' => false, 'order' => 26],
                ['key' => 'incidentFlag', 'label' => 'Incident Flag', 'required' => true, 'order' => 27],
                ['key' => 'teamMemberNotes', 'label' => 'Team Member Notes', 'required' => false, 'order' => 28],
                ['key' => 'ipeNotes', 'label' => 'IPE Notes', 'required' => false, 'order' => 29],
                ['key' => 'socialMediaContent', 'label' => 'Social Media Content', 'required' => false, 'order' => 30],
                ['key' => 'orderingNotes', 'label' => 'Ordering Notes', 'required' => false, 'order' => 31],
                ['key' => 'passDistributionLocations', 'label' => 'Pass Distribution Locations', 'required' => false, 'order' => 32],
                ['key' => 'nightSummary', 'label' => 'Night Summary', 'required' => false, 'order' => 33],
                ['key' => 'superStarNomination', 'label' => 'Superstar Nomination', 'required' => false, 'order' => 34],
                ['key' => 'shiftComments', 'label' => 'Shift Comments', 'required' => false, 'order' => 35],
            ],
            'boutique' => [
                ['key' => 'locationId', 'label' => 'Boutique Store', 'required' => true, 'order' => 1],
                ['key' => 'businessDate', 'label' => 'Business Date', 'required' => true, 'order' => 2],
                ['key' => 'submitterName', 'label' => 'Submitter Name', 'required' => true, 'order' => 3],
                ['key' => 'submitterEmail', 'label' => 'Submitter Email', 'required' => true, 'order' => 4],
                ['key' => 'grossDailySales', 'label' => 'Gross Daily Sales ($)', 'required' => true, 'order' => 5],
                ['key' => 'dailySalesGoal', 'label' => 'Daily Sales Goal ($)', 'required' => false, 'order' => 6],
                ['key' => 'totalGuestCount', 'label' => 'Total Guest Count', 'required' => true, 'order' => 7],
                ['key' => 'arcadeTheaterGuestCount', 'label' => 'Arcade / Theater Guest Count', 'required' => false, 'order' => 8],
                ['key' => 'currentWeekTotalSales', 'label' => 'Current Week Total Sales ($)', 'required' => false, 'order' => 9],
                ['key' => 'totalReturns', 'label' => 'Total Returns ($)', 'required' => false, 'order' => 10],
                ['key' => 'totalDiscount', 'label' => 'Total Discounts ($)', 'required' => false, 'order' => 11],
                ['key' => 'totalPayouts', 'label' => 'Total Payouts ($)', 'required' => false, 'order' => 12],
                ['key' => 'atmPayOuts', 'label' => 'ATM Payouts ($)', 'required' => false, 'order' => 13],
                ['key' => 'giftCardsSold', 'label' => 'Gift Cards Sold ($)', 'required' => false, 'order' => 14],
                ['key' => 'beginningSafeBalance', 'label' => 'Beginning Safe Balance ($)', 'required' => false, 'order' => 15],
                ['key' => 'endingSafeBalance', 'label' => 'Ending Safe Balance ($)', 'required' => false, 'order' => 16],
                ['key' => 'saidDeposit', 'label' => 'Said Deposit ($)', 'required' => false, 'order' => 17],
                ['key' => 'actualDeposit', 'label' => 'Actual Deposit ($)', 'required' => false, 'order' => 18],
                ['key' => 'salesDirection', 'label' => 'Sales Direction (UP/DOWN)', 'required' => true, 'order' => 19],
                ['key' => 'salesDirectionReason', 'label' => 'Sales Direction Reason', 'required' => true, 'order' => 20],
                ['key' => 'incidentFlag', 'label' => 'Incident Flag', 'required' => true, 'order' => 21],
            ],
            'coh' => [
                ['key' => 'locationId', 'label' => 'Club / Location', 'required' => true, 'order' => 1],
                ['key' => 'businessDate', 'label' => 'Business Date', 'required' => true, 'order' => 2],
                ['key' => 'submitterName', 'label' => 'Submitter Name', 'required' => true, 'order' => 3],
                ['key' => 'submitterEmail', 'label' => 'Submitter Email', 'required' => true, 'order' => 4],
                ['key' => 'dropSafe', 'label' => 'Drop Safe ($)', 'required' => false, 'order' => 5],
                ['key' => 'mainSafe', 'label' => 'Main Safe ($)', 'required' => false, 'order' => 6],
                ['key' => 'register1', 'label' => 'Register 1 ($)', 'required' => false, 'order' => 7],
                ['key' => 'register2', 'label' => 'Register 2 ($)', 'required' => false, 'order' => 8],
                ['key' => 'register3', 'label' => 'Register 3 ($)', 'required' => false, 'order' => 9],
                ['key' => 'register4', 'label' => 'Register 4 ($)', 'required' => false, 'order' => 10],
                ['key' => 'atm1', 'label' => 'ATM 1 ($)', 'required' => false, 'order' => 11],
                ['key' => 'atm2', 'label' => 'ATM 2 ($)', 'required' => false, 'order' => 12],
                ['key' => 'atm3', 'label' => 'ATM 3 ($)', 'required' => false, 'order' => 13],
                ['key' => 'atm4', 'label' => 'ATM 4 ($)', 'required' => false, 'order' => 14],
                ['key' => 'other', 'label' => 'Other Reserves ($)', 'required' => false, 'order' => 15],
                ['key' => 'paidOutsTotal', 'label' => 'Paid Outs Total ($)', 'required' => false, 'order' => 16],
                ['key' => 'paidOutsExplanation', 'label' => 'Paid Outs Explanation', 'required' => false, 'order' => 17],
            ],
            'incident' => [
                ['key' => 'locationId', 'label' => 'Club / Location', 'required' => true, 'order' => 1],
                ['key' => 'incidentDate', 'label' => 'Incident Date', 'required' => true, 'order' => 2],
                ['key' => 'timeOfIncident', 'label' => 'Time of Incident', 'required' => true, 'order' => 3],
                ['key' => 'reportTypeField', 'label' => 'Incident Type', 'required' => true, 'order' => 4],
                ['key' => 'submitterName', 'label' => 'Submitter Name', 'required' => true, 'order' => 5],
                ['key' => 'managersOnDuty', 'label' => 'Managers on Duty', 'required' => false, 'order' => 6],
                ['key' => 'managerPhone', 'label' => 'Manager Phone', 'required' => false, 'order' => 7],
                ['key' => 'castMembersOnDuty', 'label' => 'Cast Members & Staff on Duty', 'required' => false, 'order' => 8],
                ['key' => 'involvedPersons', 'label' => 'Involved Persons', 'required' => false, 'order' => 9],
                ['key' => 'incidentDescription', 'label' => 'Incident Description', 'required' => true, 'order' => 10],
                ['key' => 'witnesses', 'label' => 'Witnesses', 'required' => false, 'order' => 11],
                ['key' => 'policeReportNumber', 'label' => 'Police Report Number', 'required' => false, 'order' => 12],
                ['key' => 'policeOfficersBadges', 'label' => 'Responding Officers & Badges', 'required' => false, 'order' => 13],
                ['key' => 'cameraAngles', 'label' => 'Camera Angles', 'required' => false, 'order' => 14],
                ['key' => 'cameraTimestamp', 'label' => 'Camera Timestamp', 'required' => false, 'order' => 15],
            ],
            'witness' => [
                ['key' => 'locationId', 'label' => 'Club / Location', 'required' => true, 'order' => 1],
                ['key' => 'incidentDate', 'label' => 'Incident Date', 'required' => true, 'order' => 2],
                ['key' => 'witnessName', 'label' => 'Witness Name', 'required' => true, 'order' => 3],
                ['key' => 'witnessAddress', 'label' => 'Witness Address', 'required' => false, 'order' => 4],
                ['key' => 'witnessPhone', 'label' => 'Witness Phone', 'required' => false, 'order' => 5],
                ['key' => 'witnessEmail', 'label' => 'Witness Email', 'required' => false, 'order' => 6],
                ['key' => 'witnessType', 'label' => 'Witness Type', 'required' => true, 'order' => 7],
                ['key' => 'statementText', 'label' => 'Witness Statement', 'required' => true, 'order' => 8],
            ]
        ];

        foreach ($formFields as $type => $fields) {
            foreach ($fields as $f) {
                NrFormConfig::updateOrCreate(
                    ['report_type' => $type, 'field_key' => $f['key']],
                    [
                        'label' => $f['label'],
                        'visible' => true,
                        'required' => $f['required'],
                        'sort_order' => $f['order'],
                    ]
                );
            }
        }
    }
}
