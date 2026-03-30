<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $entertainer->display_name ?: $entertainer->user->name }} - Entertainer Page</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.7/css/bootstrap.min.css">
    <style>
        body {
            background: #0b0e1a;
            color: #e8eaf6;
        }
        .shell {
            max-width: 1040px;
            margin: 32px auto;
            padding: 0 16px;
        }
        .hero, .cardx {
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 16px;
            background: rgba(255,255,255,0.03);
        }
        .hero {
            padding: 22px;
            margin-bottom: 18px;
            position: relative;
            overflow: hidden;
            background-size: cover;
            background-position: center;
        }
        .hero::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(145deg, rgba(8, 12, 24, 0.9), rgba(8, 12, 24, 0.72));
            pointer-events: none;
        }
        .hero-content {
            position: relative;
            z-index: 1;
        }
        .hero-head {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 10px;
        }
        .avatar {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #ffcc00;
            background: rgba(255,255,255,0.08);
        }
        .avatar-fallback {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            font-weight: 700;
            border: 2px solid #ffcc00;
            background: rgba(255,255,255,0.08);
        }
        .gallery {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
            margin-bottom: 18px;
        }
        .gallery img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 12px;
            border: 1px solid rgba(255,255,255,0.1);
            background: rgba(255,255,255,0.05);
        }
        @media (max-width: 767.98px) {
            .gallery {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
        .cardx {
            padding: 16px;
            margin-bottom: 12px;
        }
        .muted { color: #b9bfd5; }
        .price {
            color: #ffcc00;
            font-weight: 800;
        }
    </style>
</head>
<body>
<div class="shell">
    <div class="hero" @if(!empty($entertainer->banner_image)) style="background-image: url('{{ asset('uploads/' . $entertainer->banner_image) }}');" @endif>
        <div class="hero-content">
            <div class="hero-head">
                @if(!empty($entertainer->profile_image))
                    <img src="{{ asset('uploads/' . $entertainer->profile_image) }}" alt="{{ $entertainer->display_name ?: $entertainer->user->name }}" class="avatar">
                @else
                    <div class="avatar-fallback">{{ strtoupper(substr($entertainer->display_name ?: $entertainer->user->name, 0, 2)) }}</div>
                @endif
                <div>
                    <h2 class="mb-1">{{ $entertainer->display_name ?: $entertainer->user->name }}</h2>
                    <div class="muted mb-0">{{ $entertainer->website->name ?? 'Club' }}</div>
                </div>
            </div>
            <p class="mb-0">{{ $entertainer->description ?: 'Explore my selected packages from this club.' }}</p>
        </div>
    </div>

    @if(!empty($entertainer->gallery_images) && count((array) $entertainer->gallery_images))
        <div class="gallery">
            @foreach((array) $entertainer->gallery_images as $galleryImage)
                <img src="{{ asset('uploads/' . $galleryImage) }}" alt="Gallery image">
            @endforeach
        </div>
    @endif

    <h4 class="mb-3">Selected Packages</h4>
    @forelse($packageMappings as $mapping)
        <div class="cardx d-flex justify-content-between align-items-center gap-3">
            <div>
                <div class="fw-semibold">{{ $mapping->package->name }}</div>
                <div class="muted">{{ $mapping->package->website->name ?? '' }}</div>
            </div>
            <div class="price">${{ number_format($mapping->package->price, 2) }}</div>
        </div>
    @empty
        <div class="cardx muted">No packages selected yet.</div>
    @endforelse
</div>
</body>
</html>
