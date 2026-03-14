@extends('layouts.master')

@section('pageTitle')
    Product Details
@endsection

@section('headerBlock')
    <link rel="stylesheet" href="{{ URL::asset('css/main.css') }}">
    <script src="{{ URL::asset('js/form.js') }}"></script>
    <style>
        #loading-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(255,255,255,0.85);
            display: none;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            z-index: 99999;
        }
        .spinner {
            border: 6px solid #f3f3f3;
            border-top: 6px solid #3498db;
            border-radius: 50%;
            width: 60px; height: 60px;
            animation: spin 1s linear infinite;
        }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        #loading-text { margin-top: 15px; font-size: 16px; color: #333; }

        .info-row {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
            margin-bottom: 16px;
        }

        .info-box {
            padding: 14px 18px;
            border-radius: 10px;
            border: 1px solid #e5e7eb;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .info-box:hover {
            border-color: #a5b4fc;
            box-shadow: 0 4px 12px rgba(99,102,241,0.08);
        }

        .info-box.full {
            grid-column: span 2;
        }

        .info-label {
            font-size: 11px;
            font-weight: 700;
            color: #4338ca;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .info-label i {
            font-size: 11px;
            color: #6366f1;
        }

        .info-value {
            font-size: 14px;
            color: #1f2937;
            font-weight: 500;
        }

        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .status-active   { background: #dcfce7; color: #16a34a; border: 1px solid #bbf7d0; }
        .status-inactive { background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }

        .pos-yes { background: #dbeafe; color: #1d4ed8; border: 1px solid #bfdbfe; }
        .pos-no  { background: #f3f4f6; color: #6b7280; border: 1px solid #e5e7eb; }

        /* Image Gallery */
        .product-layout {
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 24px;
            margin-bottom: 20px;
        }

        .image-gallery {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .main-image {
            width: 100%;
            height: 220px;
            object-fit: cover;
            border-radius: 10px;
            border: 1px solid #e5e7eb;
        }

        .thumbnail-list {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .thumbnail-list img {
            width: 58px;
            height: 58px;
            object-fit: cover;
            border-radius: 7px;
            border: 2px solid #e5e7eb;
            cursor: pointer;
            transition: border-color 0.2s;
        }

        .thumbnail-list img:hover,
        .thumbnail-list img.selected {
            border-color: #6366f1;
        }

        .no-image {
            width: 100%;
            height: 220px;
            border-radius: 10px;
            border: 1px dashed #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #9ca3af;
            font-size: 13px;
        }
    </style>
@endsection

@section('content')

    {{-- Loading Overlay --}}
    <div id="loading-overlay">
        <div class="spinner"></div>
        <div id="loading-text">Going back...</div>
    </div>

    <div class="modal-content" role="main" aria-label="Product Details">
        <a href="{{ route('products.index') }}" id="backBtn" class="btn btn-back">
            <i class="fas fa-chevron-left"></i> Back
        </a>

        <h2><i class="fas fa-box-open"></i> Product Details</h2>

        {{-- Image + Info Layout --}}
        <div class="product-layout">

            {{-- Image Gallery --}}
            <div class="image-gallery">
                @if ($product->images->count() > 0)
                    <img
                        id="mainImage"
                        src="{{ asset('images/products/' . $product->images->first()->image) }}"
                        alt="{{ $product->name }}"
                        class="main-image"
                    >
                    <div class="thumbnail-list">
                        @foreach ($product->images as $index => $img)
                            <img
                                src="{{ asset('images/products/' . $img->image) }}"
                                alt="{{ $product->name . ' image ' . ($index + 1) }}"
                                class="{{ $index === 0 ? 'selected' : '' }}"
                                onclick="document.getElementById('mainImage').src=this.src; selectThumbnail(this)"
                            >
                        @endforeach
                    </div>
                @else
                    <div class="no-image">
                        <i class="fas fa-image" style="margin-right:6px;"></i> No images available
                    </div>
                @endif
            </div>

            {{-- Product Info --}}
            <div>
                {{-- Row 1: Name & SKU --}}
                <div class="info-row">
                    <div class="info-box">
                        <div class="info-label"><i class="fas fa-box"></i> Product Name</div>
                        <div class="info-value">{{ $product->name }}</div>
                    </div>
                    <div class="info-box">
                        <div class="info-label"><i class="fas fa-barcode"></i> SKU</div>
                        <div class="info-value">{{ $product->sku ?? '-' }}</div>
                    </div>
                </div>

                {{-- Row 2: Brand & Price --}}
                <div class="info-row">
                    <div class="info-box">
                        <div class="info-label"><i class="fas fa-tag"></i> Brand</div>
                        <div class="info-value">{{ $product->category->name ?? 'N/A' }}</div>
                    </div>
                    <div class="info-box">
                        <div class="info-label"><i class="fas fa-dollar-sign"></i> Price</div>
                        <div class="info-value">${{ number_format($product->price, 2) }}</div>
                    </div>
                </div>

                {{-- Row 3: Stock & Status --}}
                <div class="info-row">
                    <div class="info-box">
                        <div class="info-label"><i class="fas fa-cubes"></i> Stock</div>
                        <div class="info-value">{{ $product->stock }}</div>
                    </div>
                    <div class="info-box">
                        <div class="info-label"><i class="fas fa-circle"></i> Status</div>
                        <div class="info-value">
                            <span class="status-badge status-{{ strtolower($product->status) }}">
                                {{ ucfirst($product->status) }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Row 4: Add to POS & Description --}}
                <div class="info-row">
                    <div class="info-box">
                        <div class="info-label"><i class="fas fa-cash-register"></i> Add to POS</div>
                        <div class="info-value">
                            <span class="status-badge {{ $product->add_to_pos ? 'pos-yes' : 'pos-no' }}">
                                {{ $product->add_to_pos ? 'Yes' : 'No' }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Row 5: Description full width --}}
                <div class="info-row">
                    <div class="info-box full">
                        <div class="info-label"><i class="fas fa-align-left"></i> Description</div>
                        <div class="info-value">{{ $product->description ?: 'No description.' }}</div>
                    </div>
                </div>

                {{-- Row 6: Created & Updated --}}
                <div class="info-row">
                    <div class="info-box">
                        <div class="info-label"><i class="fas fa-calendar-plus"></i> Created</div>
                        <div class="info-value">{{ $product->created_at->setTimezone('Asia/Phnom_Penh')->format('M d, Y \a\t g:i A') }}</div>
                    </div>
                    <div class="info-box">
                        <div class="info-label"><i class="fas fa-clock"></i> Last Updated</div>
                        <div class="info-value">{{ $product->updated_at->setTimezone('Asia/Phnom_Penh')->format('M d, Y \a\t g:i A') }}</div>
                    </div>
                </div>

            </div>
        </div>

    </div>

    <script>
        document.getElementById('backBtn').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('loading-overlay').style.display = 'flex';
            window.location.href = this.getAttribute('href');
        });

        function selectThumbnail(el) {
            document.querySelectorAll('.thumbnail-list img').forEach(function(img) {
                img.classList.remove('selected');
            });
            el.classList.add('selected');
        }
    </script>

@endsection