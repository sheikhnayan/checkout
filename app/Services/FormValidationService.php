<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class FormValidationService
{
    /**
     * Validate form submission for bot patterns
     * Returns: ['valid' => bool, 'errors' => array]
     */
    public static function validateSubmission($data, $ipAddress, $formType = 'general')
    {
        $errors = [];

        // 1. Validate email format (strict)
        if (isset($data['email'])) {
            $email = trim($data['email']);

            // Basic format check
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Invalid email format';
            }

            // Additional strict checks for obviously fake emails
            if (!self::isValidEmail($email)) {
                $errors[] = 'Please enter a valid email address';
            }
        }

        // 2. Validate phone format (if provided)
        if (isset($data['phone'])) {
            $phone = preg_replace('/\D/', '', $data['phone']);
            if (strlen($phone) < 10) {
                $errors[] = 'Invalid phone format';
            }
        }

        // 3. Check for spam content in text fields
        $spamPatterns = [
            'viagra', 'casino', 'crypto', 'bitcoin', 'poker', 'pharmacy',
            'lottery', 'forex', 'cheap cialis', 'buy now', 'click here',
            'http://', 'https://', '<script', 'onclick=', 'javascript:'
        ];

        $textFields = ['name', 'first_name', 'last_name', 'description', 'note'];
        foreach ($textFields as $field) {
            if (isset($data[$field])) {
                $value = strtolower($data[$field]);
                foreach ($spamPatterns as $pattern) {
                    if (strpos($value, $pattern) !== false) {
                        $errors[] = 'Suspicious content detected';
                        Log::warning('Spam content detected', ['field' => $field, 'pattern' => $pattern]);
                        break;
                    }
                }
            }
        }

        // 4. Check submission speed (if form_load_time is provided)
        if (isset($data['form_load_time'])) {
            $submissionTime = time() - (int)$data['form_load_time'];
            if ($submissionTime < 5) { // Less than 5 seconds = suspicious
                $errors[] = 'Submission too fast - likely automated';
                Log::warning('Suspiciously fast submission', [
                    'submission_time' => $submissionTime,
                    'form_type' => $formType,
                    'ip' => $ipAddress
                ]);
            }
        }

        // 5. Check for duplicate submissions within short timeframe
        $duplicateCheck = self::checkForDuplicate($data, $ipAddress, $formType);
        if ($duplicateCheck['isDuplicate']) {
            $errors[] = $duplicateCheck['message'];
            Log::warning('Duplicate submission detected', [
                'form_type' => $formType,
                'ip' => $ipAddress,
                'email' => $data['email'] ?? 'unknown'
            ]);
        }

        return [
            'valid' => count($errors) === 0,
            'errors' => $errors,
        ];
    }

    /**
     * Strict email validation - reject obviously fake emails
     */
    private static function isValidEmail($email)
    {
        // Check length (realistic emails are 5-254 chars)
        if (strlen($email) < 5 || strlen($email) > 254) {
            return false;
        }

        // Split email into local and domain parts
        $parts = explode('@', $email);
        if (count($parts) !== 2) {
            return false;
        }

        list($localPart, $domain) = $parts;

        // Local part validation
        if (strlen($localPart) === 0 || strlen($localPart) > 64) {
            return false;
        }

        // Local part shouldn't be all numbers or all special chars
        if (!preg_match('/[a-zA-Z0-9]/', $localPart)) {
            return false;
        }

        // Domain validation
        if (strlen($domain) < 4) { // Minimum: a.co
            return false;
        }

        // Domain must have at least one dot
        if (strpos($domain, '.') === false) {
            return false;
        }

        // Domain parts validation
        $domainParts = explode('.', $domain);

        // Each domain part must have alphanumeric characters
        foreach ($domainParts as $part) {
            if (strlen($part) === 0) {
                return false;
            }
            // Part should contain only alphanumeric, dash, and not start/end with dash
            if (!preg_match('/^[a-zA-Z0-9]([a-zA-Z0-9-]*[a-zA-Z0-9])?$/', $part)) {
                return false;
            }
        }

        // TLD (last part) should be at least 2 chars and only letters
        $tld = end($domainParts);
        if (strlen($tld) < 2 || !preg_match('/^[a-zA-Z]{2,}$/', $tld)) {
            return false;
        }

        // Check for obviously fake patterns
        $fakePatterns = [
            '/^[a-z]{20,}@/', // 20+ letter local part (common in spam)
            '/[!@#$%^&*()+=\[\]{};:\'",<>?\\\\/]{3,}/', // 3+ special chars in a row
            '/test|spam|fake|bot|scam|hack/i', // Common spam keywords
            '/aaa|bbb|ccc|ddd|eee|fff|ggg|hhh|iii|jjj|kkk|lll|mmm|nnn|ooo|ppp|qqq|rrr|sss|ttt|uuu|vvv|www|xxx|yyy|zzz/' // Repeated chars
        ];

        foreach ($fakePatterns as $pattern) {
            if (preg_match($pattern, $email)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check for duplicate submissions
     */
    private static function checkForDuplicate($data, $ipAddress, $formType)
    {
        // This is a placeholder - actual implementation depends on your database structure
        // You'll need to implement this based on your affiliate/entertainer/reservation models

        return [
            'isDuplicate' => false,
            'message' => '',
        ];
    }

    /**
     * Validate reservation form specific checks
     */
    public static function validateReservation($data, $ipAddress)
    {
        $result = self::validateSubmission($data, $ipAddress, 'reservation');

        // Additional reservation-specific checks
        if (isset($data['reservation_date']) && empty($data['reservation_date'])) {
            $result['errors'][] = 'Reservation date is required';
            $result['valid'] = false;
        }

        if (isset($data['men_count']) && isset($data['women_count'])) {
            $menCount = (int)$data['men_count'];
            $womenCount = (int)$data['women_count'];

            if (($menCount + $womenCount) === 0) {
                $result['errors'][] = 'At least one guest is required';
                $result['valid'] = false;
            }
        }

        return $result;
    }

    /**
     * Validate affiliate registration
     */
    public static function validateAffiliateRegistration($data, $ipAddress)
    {
        return self::validateSubmission($data, $ipAddress, 'affiliate_registration');
    }

    /**
     * Validate entertainer registration
     */
    public static function validateEntertainerRegistration($data, $ipAddress)
    {
        return self::validateSubmission($data, $ipAddress, 'entertainer_registration');
    }
}
