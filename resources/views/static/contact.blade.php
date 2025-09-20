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

    /* Layout per form e social */
    .contact-content-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 2rem; align-items: start; }
    @media (max-width: 768px) { .contact-content-grid { grid-template-columns: 1fr; gap: 1.5rem; } }

    .contact-form { background: #fff; border-radius: 16px; padding: 2rem; box-shadow: 0 10px 30px rgba(0,0,0,0.08); border: 1px solid #eee; }
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
    @media (max-width: 768px) { .form-grid { grid-template-columns: 1fr; } }
    .form-label { display:block; font-family: 'Montserrat', sans-serif; font-weight:600; color:#36583d; margin-bottom: .35rem; }
    .form-input, .form-textarea { width:100%; border:1px solid #ddd; border-radius:10px; padding: .8rem 1rem; font-family: 'Source Sans Pro', sans-serif; color:#2f5233; outline:none; transition: border-color .2s, box-shadow .2s; }
    .form-input:focus, .form-textarea:focus { border-color:#36583d; box-shadow: 0 0 0 3px rgba(54,88,61,.15); }
    .primary-btn { background:#36583d; color:#fff; font-family:'Montserrat',sans-serif; font-weight:700; text-transform:uppercase; border:none; border-radius:10px; padding:.9rem 1.4rem; cursor:pointer; transition:all .25s; letter-spacing:.5px; box-shadow:0 6px 16px rgba(0,0,0,.18); text-decoration: none; display: inline-block; text-align: center; }
    .primary-btn:hover { background:#2a4530; transform: translateY(-2px); box-shadow:0 10px 24px rgba(0,0,0,.22); }
    .status-box { background:#ecfdf5; border:1px solid #34d399; color:#065f46; padding:.75rem 1rem; border-radius:10px; font-family:'Source Sans Pro',sans-serif; }
    .error-text { color:#b91c1c; font-size:.9rem; margin-top:.35rem; font-family:'Source Sans Pro',sans-serif; }

    /* Sezione social */
    .social-section { background: #fff; border-radius: 16px; padding: 2rem; box-shadow: 0 10px 30px rgba(0,0,0,0.08); border: 1px solid #eee; text-align: center; }
    .social-title { font-family: 'Montserrat', sans-serif; font-weight: 700; color: #36583d; font-size: 1.5rem; margin-bottom: 1rem; }
    .social-text { font-family: 'Source Sans Pro', sans-serif; color: #2f5233; line-height: 1.6; margin-bottom: 1.5rem; }
    .instagram-btn { background: linear-gradient(45deg, #405de6, #5851db, #833ab4, #c13584, #e1306c, #fd1d1d); color: #fff; font-family: 'Montserrat', sans-serif; font-weight: 700; text-transform: uppercase; border: none; border-radius: 10px; padding: 1rem 1.5rem; cursor: pointer; transition: all .25s; letter-spacing: .5px; box-shadow: 0 6px 16px rgba(0,0,0,.18); text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; }
    .instagram-btn:hover { transform: translateY(-2px); box-shadow: 0 10px 24px rgba(0,0,0,.22); }
    .instagram-icon { width: 20px; height: 20px; }

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

<!-- Form Contatti e Social -->
<div class="section-spacing" style="padding-top: 0;">
    <div class="container-responsive">
        <div class="contact-content-grid">
            <!-- Form Contatti -->
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
                    <div style="margin-top:1.25rem;">
                        <button type="submit" class="primary-btn">Invia Messaggio</button>
                    </div>
                </form>
            </div>

            <!-- Sezione Social -->
            <div class="social-section">
                <h3 class="social-title">Scrivimi!</h3>
                <p class="social-text">Se vuoi farmi qualsiasi domanda riguardo i miei programmi di allenamento, o semplicemente vuoi avere delle informazioni in più, non esitare a contattarmi</p>
                <a href="https://www.instagram.com/emy_workout_/" target="_blank" rel="noopener" class="instagram-btn">
                    <svg class="instagram-icon" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                    </svg>
                    Instagram
                </a>
            </div>
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