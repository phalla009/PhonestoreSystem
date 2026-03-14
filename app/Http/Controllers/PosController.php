<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    /**
     * Show the POS page.
     * Only show products that are active AND marked as Add to POS.
     */
    public function index()
    {
        $products = Product::with(['images', 'category'])
            ->where('add_to_pos', 1)  // ✅ Show all products marked as Add to POS (regardless of status)
            ->get();

        return view('pos.index', compact('products'));
    }

    /**
     * Process the POS checkout.
     * Saves each cart item as a separate Order row, matching OrderController pattern.
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'items' => 'required|string',
            'total' => 'required|numeric|min:0',
        ]);

        $items = json_decode($request->input('items'), true);

        if (empty($items) || !is_array($items)) {
            return redirect()->route('pos.index')->withErrors(['items' => 'Cart is empty.']);
        }

        $posRef = 'POS-' . strtoupper(substr(uniqid(), -6)) . '-' . now()->format('His');

        $savedCount = 0;
        $totalSaved = 0;
        $errors     = [];

        DB::transaction(function () use ($items, $posRef, &$savedCount, &$totalSaved, &$errors) {

            foreach ($items as $item) {

                $productId = $item['id']       ?? null;
                $qty       = (int)($item['qty']    ?? 1);
                $discount  = $item['discount']     ?? '0';

                if (!$productId || $qty < 1) continue;

                $product = Product::find($productId);

                if (!$product) {
                    $errors[] = "Product ID {$productId} not found.";
                    continue;
                }

                // ✅ Extra guard: skip if product is not marked for POS
                if (!$product->add_to_pos) {
                    $errors[] = "\"{$product->name}\" is not available in POS.";
                    continue;
                }

                if ($product->stock < $qty) {
                    $errors[] = "Insufficient stock for \"{$product->name}\" (available: {$product->stock}).";
                    continue;
                }

                // ✅ Always use the price from the DATABASE, not from the request
                $price     = (float) $product->price;
                $lineTotal = $price * $qty;

                // Apply discount
                if ($discount !== '0' && $discount !== '') {
                    if (str_contains($discount, '%')) {
                        $pct       = (float) $discount;
                        $lineTotal -= $lineTotal * ($pct / 100);
                    } elseif (str_contains($discount, '$')) {
                        // ✅ Fixed: flat discount applied once per line, not multiplied by qty
                        $flat      = (float) $discount;
                        $lineTotal -= $flat;
                    }
                    if ($lineTotal < 0) $lineTotal = 0;
                }

                // Generate order number
                $lastOrder   = Order::where('order_number', 'like', 'ORD-KR%')
                                    ->orderBy('id', 'desc')
                                    ->lockForUpdate()
                                    ->first();
                $newNumber   = $lastOrder ? intval(substr($lastOrder->order_number, 6)) + 1 : 1;
                $orderNumber = 'ORD-KR' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);

                Order::create([
                    'order_number' => $orderNumber,
                    'customer_id'  => null,
                    'product_id'   => $product->id,
                    'quantity'     => $qty,
                    'total_amount' => round($lineTotal, 2),
                    'status'       => 'completed',
                    'order_date'   => now()->toDateString(),
                    'note'         => "POS sale · ref: {$posRef}" . ($discount !== '0' ? " · discount: {$discount}" : ''),
                ]);

                // Deduct stock
                $product->stock -= $qty;
                $product->save();

                $savedCount++;
                $totalSaved += $lineTotal;
            }
        });

        if ($savedCount === 0) {
            return redirect()->route('pos.index')
                ->withErrors($errors)
                ->withInput();
        }

        $message = "{$savedCount} item(s) saved · Total: $" . number_format($totalSaved, 2);

        if (!empty($errors)) {
            $message .= ' · ' . count($errors) . ' item(s) skipped due to stock issues.';
        }

        return redirect()->route('pos.index')->with('pos_success', $message);
    }
}