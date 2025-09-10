<x-layouts.admin>
    <style>
        .container-responsive { max-width: 900px; margin: 0 auto; padding: 0 1rem; }
        .section-spacing { padding: 2rem 0; }
        .card { background:#fff; border:1px solid #eee; border-radius:12px; padding:1.25rem; box-shadow:0 6px 16px rgba(0,0,0,.06); }
        .form-grid { display:grid; grid-template-columns:1fr; gap:1rem; }
        .form-label { display:block; font-family:'Montserrat',sans-serif; font-weight:600; color:#36583d; margin-bottom:.35rem; }
        .form-input, .form-textarea { width:100%; border:1px solid #ddd; border-radius:10px; padding:.7rem 1rem; font-family:'Source Sans Pro',sans-serif; outline:none; }
        .form-input:focus, .form-textarea:focus { border-color:#36583d; box-shadow:0 0 0 3px rgba(54,88,61,.15); }
        .primary-btn { background:#36583d; color:#fff; font-family:'Montserrat',sans-serif; font-weight:700; text-transform:uppercase; border:none; border-radius:10px; padding:.8rem 1.2rem; cursor:pointer; }
        .status-box { background:#ecfdf5; border:1px solid #34d399; color:#065f46; padding:.6rem .9rem; border-radius:10px; font-family:'Source Sans Pro',sans-serif; }
    </style>

    <div class="section-spacing">
        <div class="container-responsive">
            <h1 style="font-family:'Montserrat',sans-serif;color:#36583d;">Impostazioni Contatti</h1>
            @if(session('status'))
                <div class="status-box" role="status">{{ session('status') }}</div>
            @endif
            <div class="card" style="margin-top:1rem;">
                <form method="POST" action="{{ route('admin.settings.contact.update') }}" novalidate>
                    @csrf
                    <div class="form-grid">
                        <div>
                            <label class="form-label" for="phone">Telefono</label>
                            <input class="form-input" id="phone" name="phone" type="text" value="{{ old('phone', $phone) }}">
                            @error('phone')<div style="color:#b91c1c;">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label class="form-label" for="email">Email</label>
                            <input class="form-input" id="email" name="email" type="email" value="{{ old('email', $email) }}">
                            @error('email')<div style="color:#b91c1c;">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label class="form-label" for="recipient_email">Destinatario form</label>
                            <input class="form-input" id="recipient_email" name="recipient_email" type="email" value="{{ old('recipient_email', $recipient_email) }}" required>
                            @error('recipient_email')<div style="color:#b91c1c;">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label class="form-label" for="address">Indirizzo</label>
                            <input class="form-input" id="address" name="address" type="text" value="{{ old('address', $address) }}">
                            @error('address')<div style="color:#b91c1c;">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label class="form-label" for="map_embed">Mappa (src o iframe)</label>
                            <textarea class="form-textarea" id="map_embed" name="map_embed" rows="4">{{ old('map_embed', $map_embed) }}</textarea>
                            @error('map_embed')<div style="color:#b91c1c;">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div style="margin-top:1rem;">
                        <button type="submit" class="primary-btn">Salva impostazioni</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.admin>
