<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BakongKhqrController extends Controller
{
    private string $baseUrl = 'https://api-bakong.nbc.gov.kh';

    /**
     * Generate KHQR
     */
    public function khqrGenerate(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01'
        ]);

        $amount   = round((float) $request->amount, 2);
        $orderRef = 'POS' . now()->format('YmdHis') . random_int(1000, 9999);

        $accountId    = strtolower(trim(env('BAKONG_MERCHANT_ID')));
        $merchantName = strtoupper(trim(env('BAKONG_MERCHANT_NAME', 'ZSHOP')));
        $merchantCity = strtoupper(trim(env('BAKONG_MERCHANT_CITY', 'PHNOM PENH')));
        $currency     = strtoupper(trim(env('BAKONG_CURRENCY', 'USD')));

        $currencyCode = $currency === 'KHR' ? '116' : '840';

        try {
            $qr  = $this->buildKhqrString(
                $accountId,
                $merchantName,
                $merchantCity,
                $amount,
                $currencyCode,
                $orderRef
            );

            $md5 = md5($qr);

            return response()->json([
                'success'    => true,
                'qr'         => $qr,
                'md5'        => $md5,
                'amount'     => $amount,
                'currency'   => $currency,
                'order_ref'  => $orderRef,
                'expires_at' => now()->addMinutes(15)->toISOString(),
            ]);

        } catch (\Exception $e) {
            Log::error('KHQR Generation Failed', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate QR',
            ], 500);
        }
    }

    /**
     * Check payment via MD5
     */
    public function checkPayment(Request $request)
    {
        $request->validate([
            'md5' => 'required|string'
        ]);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('BAKONG_API_TOKEN'),
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/json',
            ])
            ->timeout(10)
            ->post($this->baseUrl . '/v1/checkTransactionByMD5', [
                'md5' => $request->md5,
            ]);

            $data = $response->json();
            $paid = isset($data['responseCode']) && $data['responseCode'] === 0;

            return response()->json([
                'paid' => $paid,
                'data' => $data,
            ]);

        } catch (\Exception $e) {
            Log::error('KHQR Payment Check Failed', ['error' => $e->getMessage()]);

            return response()->json([
                'paid'    => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Build a valid Individual/Solo-Merchant KHQR string.
     *
     * KEY FACTS from the official bakong-khqr-php SDK:
     *
     *   Tag 29 – Individual merchant:
     *     Sub-tag 00 = Bakong account ID directly (e.g. "phallaheang@aclb")
     *     Decoded proof: [29]=> array(1) { ["00"]=> "john_smith@devb" }
     *
     *   Tag 30 – Corporate merchant (NOT used here):
     *     Sub-tag 00 = Bakong account ID
     *     Sub-tag 01 = Merchant ID
     *     Sub-tag 02 = Acquiring Bank
     *
     *   Tag 99 – Timestamp in milliseconds (required for dynamic QR)
     *     Sub-tag 00 = creation timestamp ms
     *     Sub-tag 01 = expiration timestamp ms (optional but recommended)
     */
    private function buildKhqrString(
        string $accountId,
        string $merchantName,
        string $merchantCity,
        float  $amount,
        string $currencyCode,
        string $billNumber
    ): string {

        $tlv = static function (string $tag, string $value): string {
            return $tag . str_pad(strlen($value), 2, '0', STR_PAD_LEFT) . $value;
        };

        // ── Tag 29 – Individual Merchant Account ──────────────────────────────
        // Sub-tag 00 is the Bakong account ID. That is the ONLY sub-tag needed.
        $tag29 = $tlv('29', $tlv('00', $accountId));

        // ── Tag 62 – Additional Data ──────────────────────────────────────────
        $tag62 = $tlv('62', $tlv('01', $billNumber));

        // ── Tag 99 – Timestamp (ms) ───────────────────────────────────────────
        // Creation timestamp is required so Bakong can match the MD5 hash.
        $nowMs    = (string) (int) (microtime(true) * 1000);
        $expireMs = (string) ((int) $nowMs + 15 * 60 * 1000); // +15 min
        $tag99    = $tlv('99', $tlv('00', $nowMs) . $tlv('01', $expireMs));

        $amountStr    = number_format($amount, 2, '.', '');
        $merchantName = substr($merchantName, 0, 25); // EMVCo Tag 59 max
        $merchantCity = substr($merchantCity, 0, 15); // EMVCo Tag 60 max

        // ── Full body (without CRC value) ─────────────────────────────────────
        $body =
            '000201'                    .   // Payload Format Indicator
            '010212'                    .   // Point of Initiation (12 = dynamic)
            $tag29                      .   // Merchant Account Info
            $tlv('52', '5999')          .   // MCC
            $tlv('53', $currencyCode)   .   // Currency
            $tlv('54', $amountStr)      .   // Amount
            $tlv('58', 'KH')            .   // Country Code
            $tlv('59', $merchantName)   .   // Merchant Name
            $tlv('60', $merchantCity)   .   // Merchant City
            $tag62                      .   // Additional Data (bill number)
            $tag99                      .   // Timestamp
            '6304';                         // CRC tag + length placeholder

        $crc = strtoupper(str_pad(dechex($this->crc16($body)), 4, '0', STR_PAD_LEFT));

        return $body . $crc;
    }

    /**
     * CRC-16 / CCITT-FALSE  (Poly=0x1021, Init=0xFFFF, no reflection)
     */
    private function crc16(string $data): int
    {
        $crc = 0xFFFF;

        for ($i = 0, $len = strlen($data); $i < $len; $i++) {
            $crc ^= ord($data[$i]) << 8;

            for ($j = 0; $j < 8; $j++) {
                $crc = ($crc & 0x8000)
                    ? (($crc << 1) ^ 0x1021) & 0xFFFF
                    : ($crc << 1) & 0xFFFF;
            }
        }

        return $crc;
    }
}