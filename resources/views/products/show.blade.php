@extends('layouts.master')

@section('pageTitle')
    Show Product
@endsection

@section('headerBlock')
    <link rel="stylesheet" href="{{ URL::asset('css/main.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('css/show-products.css') }}">
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
    </style>
@endsection

@section('content')

    {{-- Loading Overlay --}}
    <div id="loading-overlay">
        <div class="spinner"></div>
        <div id="loading-text">Going back...</div>
    </div>

    <div class="details-card" role="main" aria-label="Product Details">
        <a href="{{ route('products.index') }}" id="backBtn" class="btn btn-back" style="margin-bottom:20px; display:inline-block;">
            <i class="fas fa-chevron-left"></i> Back
        </a>
        <h2><i class="fas fa-box-open"></i> Product Details</h2>

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
                    <div class="thumbnail-list" role="list">
                        @foreach ($product->images as $index => $img)
                            <img
                                src="{{ asset('images/products/' . $img->image) }}"
                                alt="{{ $product->name . ' image ' . ($index + 1) }}"
                                class="{{ $index === 0 ? 'selected' : '' }}"
                                role="listitem"
                                onclick="document.getElementById('mainImage').src=this.src; selectThumbnail(this)"
                            >
                        @endforeach
                    </div>
                @else
                    <div>No images available</div>
                @endif
            </div>

            {{-- Product Information --}}
            <div class="product-info">
                <div class="details-row">
                    <div class="details-group">
                        <label>Product Name:</label>
                        <div>{{ $product->name }}</div>
                    </div>
                    <div class="details-group">
                        <label>Brand:</label>
                        <div>{{ $product->category->name ?? 'N/A' }}</div>
                    </div>
                    <div class="details-group">
                        <label>Price:</label>
                        <div>${{ number_format($product->price, 2) }}</div>
                    </div>
                </div>

                <div class="details-row">
                    <div class="details-group">
                        <label>Stock:</label>
                        <div>{{ $product->stock }}</div>
                    </div>
                    <div class="details-group">
                        <label>Status:</label>
                        <span class="status-badge status-{{ strtolower($product->status) }}">
                            {{ ucfirst($product->status) }}
                        </span>
                    </div>
                    <div class="details-group" style="flex: 1 1 100%;">
                        <label>Description:</label>
                        {{ $product->description ?: 'No description.' }}
                    </div>
                </div>

                <div class="details-row">
                    <div class="details-group">
                        <label>Created:</label>
                        {{ $product->created_at->setTimezone('Asia/Phnom_Penh')->format('M d, Y \a\t g:i A') }}
                    </div>
                    <div class="details-group">
                        <label>Last Updated:</label>
                        {{ $product->updated_at->setTimezone('Asia/Phnom_Penh')->format('M d, Y \a\t g:i A') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Back button → show loading then navigate
        document.getElementById('backBtn').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('loading-overlay').style.display = 'flex';
            window.location.href = this.getAttribute('href');
        });

        // Thumbnail selector
        function selectThumbnail(el) {
            document.querySelectorAll('.thumbnail-list img').forEach(function(img) {
                img.classList.remove('selected');
            });
            el.classList.add('selected');
        }
    </script>

@endsection