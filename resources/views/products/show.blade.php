@extends('layouts.master')

@section('pageTitle')
    Show Product
@endsection

@section('headerBlock')
    <link rel="stylesheet" href="{{ URL::asset('css/main.css') }}">
    <script src="{{ URL::asset('js/form.js') }}"></script>
    <style>
        .details-card {
            max-width: 100%;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            padding: 30px 40px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #2c3e50;
        }

        .details-card h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #34495e;
            font-weight: 700;
            font-size: 2rem;
        }

        .product-layout {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            margin-top: 30px;
        }

        .image-gallery {
            flex: 1 1 40%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .product-info {
            flex: 1 1 55%;
        }

        .details-row {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .details-group {
            flex: 1 1 30%;
            margin: 0 10px 10px 0;
            font-size: 1.1rem;
            word-wrap: break-word;
        }

        .details-group label {
            font-weight: bold;
            color: #2980b9;
            display: block;
            margin-bottom: 5px;
        }

        .details-group div {
            font-size: 1.1rem;
            white-space: pre-wrap; /* Wrap long text and preserve line breaks */
            word-break: break-word;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 4px;
            color: #fff;
            font-weight: bold;
            text-transform: capitalize;
        }

        .status-active {
            background-color: #27ae60;
        }

        .status-inactive {
            background-color: #c0392b;
        }

        /* Image gallery styles */
        .image-gallery .main-image {
            width: 400px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            object-fit: contain;
            margin-bottom: 15px;
        }

        .image-gallery .thumbnail-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, 70px);
            grid-auto-rows: 70px;
            grid-gap: 10px;
            max-height: 250px;
            width: 350px;
            overflow-y: auto;
            box-sizing: border-box;
        }

        .image-gallery .thumbnail-list img {
            width: 70px;
            height: 70px;
            border-radius: 5px;
            cursor: pointer;
            object-fit: cover;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            border: 2px solid transparent;
            transition: border-color 0.3s ease;
        }

        .image-gallery .thumbnail-list img.selected {
            border-color: #2980b9;
        }

        @media (max-width: 768px) {
            .product-layout {
                flex-direction: column;
                align-items: center;
            }

            .details-group {
                flex: 1 1 100%;
                white-space: normal;
            }

            .image-gallery .thumbnail-list {
                flex-direction: row !important;
                max-height: none !important;
                overflow-y: visible !important;
                margin-top: 10px;
            }
        }
    </style>
@endsection

@section('content')
<div class="details-card" role="main" aria-label="Product Details">
    <a href="{{ route('products.index') }}" class="btn btn-back" style="margin-bottom:20px; display:inline-block;">
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
    function selectThumbnail(el) {
        document.querySelectorAll('.thumbnail-list img').forEach(img => {
            img.classList.remove('selected');
        });
        el.classList.add('selected');
    }
</script>
@endsection
