<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PaymentController extends Controller
{
    public function home()
    {
        return view('index'); // index.blade.php
    }

    public function buyNow(Request $request)
{
    $product = $request->input('name');

    // Set default price if not provided
    $price = $request->input('price', 5.00); // default 5.00 USD

    // Validate
    if (!is_numeric($price) || $price <= 0) {
        return back()->withErrors(['price' => 'Invalid amount']);
    }

    // If product name is empty, you can set a default name
    if (!$product) {
        $product = 'Default Product';
    }

    // Mock KHQR payload
    $qrPayload = "bank_account=phallaheang@aclb&merchant_name=PHALLA&merchant_city=Phnom Penh&amount={$price}&currency=USD&store_label=KRShop&bill_number=TRX01234567&terminal_label=Cashier-01";

    // Generate MD5 filename based on product name
    $filenameMd5 = md5($product);
    $filename = "qr/{$filenameMd5}_qr.png";

    // Ensure qr folder exists in storage/app/public
    Storage::disk('public')->makeDirectory('qr');

    // Generate QR code and save
    $img = QrCode::format('png')
                ->size(300)
                ->generate($qrPayload);

    Storage::disk('public')->put($filename, $img);

    // Redirect to show QR page
    return redirect()->route('show_qr', [
        'product' => $product,
        'price'   => $price
    ]);
}


    public function showQr(Request $request)
    {
        $product = $request->query('product');
        $price   = $request->query('price');

        $filenameMd5 = md5($product);
        $qrUrl = asset("storage/qr/{$filenameMd5}_qr.png");

        return view('show_qr', compact('product', 'price', 'qrUrl'));
    }
}
