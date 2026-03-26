<?php

/**
 * TOTP Implementation following RFC 6238
 * - HMAC-SHA1
 * - 60-second time step
 * - 6-digit OTP
 * - ±1 window drift tolerance in verification
 */

// ─── Constants ────────────────────────────────────────────────────────────────

const OTP_STEP       = 60;       // Time step in seconds (RFC 6238 calls this T1)
const OTP_DIGITS     = 6;        // Number of OTP digits
const OTP_ALGORITHM  = 'sha1';   // HMAC algorithm
//แบบเดิม เอไอแม่งเสือกลบออก เลยต้องใส่ใหม่เอง
const OTP_DURATION   = OTP_STEP; // Alias for clarity in remaining time calculation

// ─── Secret Generation ────────────────────────────────────────────────────────

/**
 * Generate a random Base32-encoded secret.
 * Both machines MUST share this exact same string.
 */
function generateSecret(int $length = 16): string
{
    $chars  = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    $secret = '';
    for ($i = 0; $i < $length; $i++) {
        $secret .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return $secret;
}

//ขี้เกียจเขียนใหม่เลยใช้ชื่อเดิมละกัน

function getTOTP(string $secret, ?int $timeSlice = null): string
{
    return generateTOTP($secret, $timeSlice);
}

// ─── Base32 Decode ────────────────────────────────────────────────────────────

/**
 * Decode a Base32 string into raw binary.
 *
 * TOTP secrets are stored as Base32 strings (e.g. in Google Authenticator QR
 * codes). HMAC requires the raw binary key, so we must decode first.
 *
 * @param  string $secret  Uppercase Base32 string (with or without '=' padding)
 * @return string|false    Raw binary string, or false on invalid input
 */
function base32Decode(string $secret): string|false
{
    if (empty($secret)) return false;

    // Remove padding characters; they are optional per RFC 4648
    $secret = strtoupper(str_replace('=', '', $secret));

    $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    $lookup   = array_flip(str_split($alphabet));

    // Step 1: Convert each Base32 character to its 5-bit value, then to binary
    $bits = '';
    for ($i = 0; $i < strlen($secret); $i++) {
        $char = $secret[$i];
        if (!isset($lookup[$char])) {
            return false; // Invalid character
        }
        // Pad each value to exactly 5 bits
        $bits .= str_pad(decbin($lookup[$char]), 5, '0', STR_PAD_LEFT);
    }

    // Step 2: Split the bit string into 8-bit chunks and convert to bytes
    $decoded = '';
    foreach (str_split($bits, 8) as $byte) {
        if (strlen($byte) === 8) {
            $decoded .= chr(bindec($byte));
        }
        // Trailing partial bytes (padding artifacts) are intentionally discarded
    }

    return $decoded;
}


// ─── OTP Generation ───────────────────────────────────────────────────────────

/**
 * Compute the current time slice.
 *
 * Both machines MUST produce the same value here. Differences arise when:
 *  - Clocks are skewed (use NTP on both machines)
 *  - OTP_STEP differs between machines
 *  - time() returns different values due to timezone settings
 *    (time() is always UTC Unix epoch, so timezone does NOT affect it)
 *
 * @param  int|null $forceTimestamp  Override time() for testing
 */
function getTimeSlice(?int $forceTimestamp = null): int
{
    $timestamp = $forceTimestamp ?? time();
    return (int) floor($timestamp / OTP_STEP);
}

/**
 * Generate a TOTP code per RFC 6238.
 *
 * Steps:
 *  1. Decode the Base32 secret to binary (the HMAC key)
 *  2. Pack the time slice as a big-endian 64-bit integer (8 bytes)
 *  3. Compute HMAC-SHA1 of the packed time with the binary key
 *  4. Dynamic truncation: use the last nibble as an offset
 *  5. Extract 4 bytes at that offset and mask to 31 bits
 *  6. Modulo 10^digits to get the final OTP
 *
 * @param  string   $secret     Base32-encoded shared secret
 * @param  int|null $timeSlice  Override time slice (for testing or verification window)
 * @return string               Zero-padded OTP string (e.g. "048271")
 */
function generateTOTP(string $secret, ?int $timeSlice = null): string
{
    // Step 1: Decode secret from Base32 → raw binary
    $binaryKey = base32Decode($secret);
    if ($binaryKey === false) {
        throw new InvalidArgumentException('Invalid Base32 secret.');
    }

    // Step 2: Use provided time slice or compute from current time
    $slice = $timeSlice ?? getTimeSlice();

    // Step 3: Pack time slice as big-endian unsigned 64-bit integer (8 bytes)
    // RFC 6238 §4 requires the counter to be a 64-bit big-endian value.
    // PHP lacks a native 64-bit pack format on 32-bit builds, so we split
    // into two 32-bit big-endian halves: high word (always 0 for current
    // Unix timestamps) and low word.
    $timePacked = pack('N*', 0) . pack('N*', $slice);

    // Step 4: Compute HMAC-SHA1
    $hash = hash_hmac(OTP_ALGORITHM, $timePacked, $binaryKey, true /* raw binary */);

    // Step 5: Dynamic truncation
    // The offset is the low 4 bits of the last byte of the hash
    $offset = ord($hash[-1]) & 0x0F;

    // Extract 4 bytes starting at offset and interpret as big-endian 32-bit int.
    // Mask the most-significant bit (0x7F) to avoid signed integer issues.
    $code =
        ((ord($hash[$offset])     & 0x7F) << 24) |
        ((ord($hash[$offset + 1]) & 0xFF) << 16) |
        ((ord($hash[$offset + 2]) & 0xFF) <<  8) |
        ( ord($hash[$offset + 3]) & 0xFF);

    // Step 6: Reduce to OTP_DIGITS digits and zero-pad
    return str_pad((string) ($code % (10 ** OTP_DIGITS)), OTP_DIGITS, '0', STR_PAD_LEFT);
}


// ─── Verification ─────────────────────────────────────────────────────────────

/**
 * Verify a TOTP code, allowing ±$discrepancy time windows to handle clock drift.
 *
 * @param  string $secret      Base32-encoded shared secret
 * @param  string $inputOTP    The OTP provided by the user
 * @param  int    $discrepancy Number of time steps to allow on either side (default 1)
 * @return bool
 */
function verifyTOTP(string $secret, string $inputOTP, int $discrepancy = 1): bool
{
    if (empty($secret) || !ctype_digit($inputOTP)) return false;

    $currentSlice = getTimeSlice();
    $paddedInput  = str_pad($inputOTP, OTP_DIGITS, '0', STR_PAD_LEFT);

    for ($i = -$discrepancy; $i <= $discrepancy; $i++) {
        $candidate = generateTOTP($secret, $currentSlice + $i);
        if (hash_equals($candidate, $paddedInput)) {
            return true;
        }
    }
    return false;
}

/**
 * Verify a TOTP code against a specific reference timestamp (useful for testing).
 *
 * @param  string $secret      Base32-encoded shared secret
 * @param  string $inputOTP    The OTP provided by the user
 * @param  int    $timestamp   Unix timestamp to use as reference
 * @param  int    $discrepancy Number of time steps to allow on either side
 * @return bool
 */
function verifyTOTPAtTime(string $secret, string $inputOTP, int $timestamp, int $discrepancy = 1): bool
{
    if (empty($secret) || !ctype_digit($inputOTP)) return false;

    $baseSlice   = getTimeSlice($timestamp);
    $paddedInput = str_pad($inputOTP, OTP_DIGITS, '0', STR_PAD_LEFT);

    for ($i = -$discrepancy; $i <= $discrepancy; $i++) {
        $candidate = generateTOTP($secret, $baseSlice + $i);
        if (hash_equals($candidate, $paddedInput)) {
            return true;
        }
    }
    return false;
}
/*

// ─── Debug & Demo ─────────────────────────────────────────────────────────────

// Shared secret — BOTH machines must use this exact string
$sharedSecret = 'JBSWY3DPEHPK3PXP';

$now       = time();
$timeSlice = getTimeSlice($now);
$otp       = generateTOTP($sharedSecret, $timeSlice);

echo "=== TOTP Debug Output ===" . PHP_EOL;
echo "Shared Secret  : {$sharedSecret}" . PHP_EOL;
echo "Unix Timestamp : {$now}" . PHP_EOL;
echo "Time Step      : " . OTP_STEP . " seconds" . PHP_EOL;
echo "Time Slice     : {$timeSlice}   ← both machines must print this same value" . PHP_EOL;
echo "Generated OTP  : {$otp}" . PHP_EOL;

// Simulate Machine B using the same secret and same forced time slice
$machineBOTP = generateTOTP($sharedSecret, $timeSlice);
echo PHP_EOL;
echo "=== Cross-Machine Consistency Test ===" . PHP_EOL;
echo "Machine A OTP  : {$otp}" . PHP_EOL;
echo "Machine B OTP  : {$machineBOTP}" . PHP_EOL;
echo "Match          : " . ($otp === $machineBOTP ? "YES ✓" : "NO ✗") . PHP_EOL;

// Verification with ±1 window
echo PHP_EOL;
echo "=== Verification Test ===" . PHP_EOL;
$verified = verifyTOTP($sharedSecret, $otp);
echo "Verify current OTP   : " . ($verified ? "PASS ✓" : "FAIL ✗") . PHP_EOL;

// Test with a fixed known timestamp (deterministic, same on every machine)
$fixedTime  = 1700000000;
$fixedSlice = getTimeSlice($fixedTime);
$fixedOTP   = generateTOTP($sharedSecret, $fixedSlice);
echo PHP_EOL;
echo "=== Fixed Timestamp Test (deterministic) ===" . PHP_EOL;
echo "Fixed Timestamp: {$fixedTime}" . PHP_EOL;
echo "Fixed Slice    : {$fixedSlice}" . PHP_EOL;
echo "Fixed OTP      : {$fixedOTP}   ← must always be the same on any machine" . PHP_EOL;
echo "Verify fixed   : " . (verifyTOTPAtTime($sharedSecret, $fixedOTP, $fixedTime) ? "PASS ✓" : "FAIL ✗") . PHP_EOL;
*/