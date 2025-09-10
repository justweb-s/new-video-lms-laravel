@extends('layouts.public')

@push('styles')
<style>
    .hero-section { background: linear-gradient(135deg, #f6e849 0%, #f4e030 100%); padding: 60px 0; position: relative; overflow: hidden; }
    .hero-title { background-color: #36583d; color: #fff; padding: 1rem 2rem; border-radius: 12px; font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 2rem; text-transform: uppercase; letter-spacing: 1px; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
    .section-spacing { padding: 3rem 0; }
    .container-responsive { max-width: 1200px; margin: 0 auto; padding: 0 1rem; }
    .section-title { font-family: 'Montserrat', sans-serif; font-weight: 700; color: #36583d; text-transform: uppercase; letter-spacing: 1px; text-align: center; margin-bottom: 1rem; }
    .section-underline { width: 90px; height: 4px; background: #f6e849; margin: 0.75rem auto 2rem; border-radius: 2px; }

    .contact-info-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.25rem; }
    @media (max-width: 1023px) { .contact-info-grid { grid-template-columns: 1fr 1fr; } }
    @media (max-width: 640px) { .contact-info-grid { grid-template-columns: 1fr; } }
    .info-card { background: #fff; border-radius: 14px; padding: 1.5rem; box-shadow: 0 8px 24px rgba(0,0,0,0.08); border: 1px solid #eee; display: flex; align-items: center; gap: 1rem; }
    .info-icon { width: 44px; height: 44px; border-radius: 50%; background: #36583d; color: #f6e849; display:flex; align-items:center; justify-content:center; box-shadow: 0 6px 14px rgba(54,88,61,0.25); }
    .info-card h3 { margin: 0; font-family: 'Montserrat', sans-serif; color: #36583d; font-size: 1.1rem; }
    .info-card a, .info-card p { color: #2f5233; font-family: 'Source Sans Pro', sans-serif; text-decoration: none; }
    .info-card a:hover { text-decoration: underline; }

    .contact-form { background: #fff; border-radius: 16px; padding: 2rem; box-shadow: 0 10px 30px rgba(0,0,0,0.08); border: 1px solid #eee; }
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
    @media (max-width: 768px) { .form-grid { grid-template-columns: 1fr; } }
    .form-label { display:block; font-family: 'Montserrat', sans-serif; font-weight:600; color:#36583d; margin-bottom: .35rem; }
    .form-input, .form-textarea { width:100%; border:1px solid #ddd; border-radius:10px; padding: .8rem 1rem; font-family: 'Source Sans Pro', sans-serif; color:#2f5233; outline:none; transition: border-color .2s, box-shadow .2s; }
    .form-input:focus, .form-textarea:focus { border-color:#36583d; box-shadow: 0 0 0 3px rgba(54,88,61,.15); }
    .primary-btn { background:#36583d; color:#fff; font-family:'Montserrat',sans-serif; font-weight:700; text-transform:uppercase; border:none; border-radius:10px; padding:.9rem 1.4rem; cursor:pointer; transition:all .25s; letter-spacing:.5px; box-shadow:0 6px 16px rgba(0,0,0,.18); }
    .primary-btn:hover { background:#2a4530; transform: translateY(-2px); box-shadow:0 10px 24px rgba(0,0,0,.22); }
    .status-box { background:#ecfdf5; border:1px solid #34d399; color:#065f46; padding:.75rem 1rem; border-radius:10px; font-family:'Source Sans Pro',sans-serif; }
    .error-text { color:#b91c1c; font-size:.9rem; margin-top:.35rem; font-family:'Source Sans Pro',sans-serif; }
    .map-card { background:#fff; border-radius:16px; padding: 0; overflow:hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.08); border:1px solid #eee; }
    .map-iframe { width:100%; height:420px; border:0; }
</style>
@endpush

@section('content')
<!-- Hero -->
<div class="hero-section">
    <div class="container-responsive">
        <h1 class="hero-title">Contatti</h1>
    </div>
</div>

<!-- Info principali -->
<div class="section-spacing">
    <div class="container-responsive">
        <h2 class="section-title">Parliamo di te</h2>
        <div class="section-underline"></div>

        <div class="contact-info-grid">
            <div class="info-card">
                <div class="info-icon" aria-hidden="true">📞</div>
                <div>
                    <h3>Telefono</h3>
                    <a href="tel:{{ preg_replace('/[^0-9+]/','', $phone) }}">{{ $phone }}</a>
                </div>
            </div>

            <div class="info-card">
                <div class="info-icon" aria-hidden="true">📍</div>
                <div>
                    <h3>Indirizzo</h3>
                    <a href="https://maps.google.com/?q={{ urlencode($address) }}" target="_blank" rel="noopener">{{ $address }}</a>
                </div>
            </div>

            <div class="info-card">
                <div class="info-icon" aria-hidden="true">✉️</div>
                <div>
                    <h3>Email</h3>
                    <a href="mailto:{{ $email }}">{{ $email }}</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Form Contatti -->
<div class="section-spacing" style="padding-top: 0;">
    <div class="container-responsive">
        <div class="contact-form">
            @if(session('status'))
                <div class="status-box" role="status">{{ session('status') }}</div>
            @endif
            <form action="{{ route('static.contact.submit') }}" method="POST" novalidate>
                @csrf
                <div class="form-grid">
                    <div>
                        <label for="name" class="form-label">Nome</label>
                        <input type="text" id="name" name="name" class="form-input" placeholder="Il tuo nome" value="{{ old('name') }}" required>
                        @error('name')<div class="error-text">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" class="form-input" placeholder="La tua email" value="{{ old('email') }}" required>
                        @error('email')<div class="error-text">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="form-grid" style="margin-top:1rem;">
                    <div>
                        <label for="phone" class="form-label">Telefono (opzionale)</label>
                        <input type="text" id="phone" name="phone" class="form-input" placeholder="Il tuo telefono" value="{{ old('phone') }}">
                        @error('phone')<div class="error-text">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label for="subject" class="form-label">Oggetto (opzionale)</label>
                        <input type="text" id="subject" name="subject" class="form-input" placeholder="Oggetto" value="{{ old('subject') }}">
                        @error('subject')<div class="error-text">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div style="margin-top:1rem;">
                    <label for="message" class="form-label">Messaggio</label>
                    <textarea id="message" name="message" rows="5" class="form-textarea" placeholder="Scrivi il tuo messaggio" required>{{ old('message') }}</textarea>
                    @error('message')<div class="error-text">{{ $message }}</div>@enderror
                </div>
                <div style="margin-top:1.25rem; display:flex; gap:.75rem; align-items:center;">
                    <button type="submit" class="primary-btn">Invia Messaggio</button>
                    <a href="https://www.instagram.com/emy__pt/" target="_blank" rel="noopener" class="primary-btn" style="background:#f6e849;color:#36583d;">Instagram</a>
                </div>
            </form>
        </div>
    </div>
    
</div>

<!-- Mappa -->
<div class="section-spacing" style="padding-top: 0;">
    <div class="container-responsive">
        <h2 class="section-title">Dove Siamo</h2>
        <div class="section-underline"></div>
        <div class="map-card">
            <iframe class="map-iframe" src="{{ $mapEmbed }}" loading="lazy" referrerpolicy="no-referrer-when-downgrade" aria-label="Mappa"></iframe>
        </div>
    </div>
</div>
@endsection
