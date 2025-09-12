<x-layouts.admin>
<div class="container mx-auto px-6 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Nuova Lezione</h1>
            <p class="text-gray-600 mt-2">Sezione: {{ $section->name }} - Corso: {{ $course->name }}</p>
        </div>
        <a href="{{ route('admin.courses.sections.lessons.index', [$course, $section]) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            Torna alle Lezioni
        </a>
    </div>

    <!-- Form -->
    <div class="bg-white shadow-md rounded-lg p-6">
                <form id="lesson-form" action="{{ route('admin.courses.sections.lessons.store', [$course, $section]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Titolo -->
                <div class="md:col-span-2">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Titolo Lezione *</label>
                    <input type="text" 
                           name="title" 
                           id="title" 
                           value="{{ old('title') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary @error('title') border-red-500 @enderror"
                           placeholder="Es: Introduzione agli esercizi base"
                           required>
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Ordine -->
                <div>
                    <label for="lesson_order" class="block text-sm font-medium text-gray-700 mb-2">Ordine *</label>
                    <input type="number" 
                           name="lesson_order" 
                           id="lesson_order" 
                           value="{{ old('lesson_order', $section->lessons->max('lesson_order') + 1) }}"
                           min="1"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary @error('lesson_order') border-red-500 @enderror"
                           required>
                    @error('lesson_order')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Ordine di visualizzazione della lezione nella sezione</p>
                </div>

                <!-- Durata -->
                <div>
                    <label for="duration_minutes" class="block text-sm font-medium text-gray-700 mb-2">Durata (minuti) *</label>
                    <input type="number" 
                           name="duration_minutes" 
                           id="duration_minutes" 
                           value="{{ old('duration_minutes') }}"
                           min="1"
                           max="300"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary @error('duration_minutes') border-red-500 @enderror"
                           placeholder="Es: 15"
                           required>
                    @error('duration_minutes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Video File -->
                <div class="md:col-span-2">
                    <label for="video_file" class="block text-sm font-medium text-gray-700 mb-2">Carica Video *</label>
                    <input type="file" 
                           name="video_file" 
                           id="video_file" 
                           accept="video/mp4,video/avi,video/mpeg"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary @error('video_file') border-red-500 @enderror">
                    @error('video_file')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Carica il file video della lezione (max 1GB). Tipi supportati: MP4, AVI, MPEG.</p>

                    <div class="mt-3">
                        <span class="text-sm text-gray-500">Oppure</span>
                        <button type="button" id="open-media-modal" class="ml-2 inline-flex items-center px-3 py-2 bg-primary text-white rounded hover:bg-primary/90">Scegli dalla Galleria</button>
                    </div>

                    <input type="hidden" name="video_url" id="video_url" value="{{ old('video_url') }}">
                    <div id="selected-media" class="mt-2 hidden">
                        <span class="text-sm text-gray-600">Selezionato:</span>
                        <a id="selected-media-url" href="#" target="_blank" class="text-primary text-sm break-all"></a>
                        <button type="button" id="clear-selected-media" class="ml-2 text-sm text-red-600 hover:underline">Rimuovi</button>
                    </div>
                </div>

                <!-- Upload Progress -->
                <div id="upload-progress" class="md:col-span-2 hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Caricamento video...</label>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div id="upload-progress-bar" class="bg-primary h-3 rounded-full" style="width:0%"></div>
                    </div>
                    <p id="upload-progress-text" class="mt-1 text-sm text-gray-600">0%</p>
                </div>

                <!-- Stato -->
                <div class="flex items-center md:col-span-2">
                    <div class="flex items-center h-5">
                        <input type="checkbox" 
                               name="is_active" 
                               id="is_active" 
                               value="1"
                               {{ old('is_active', true) ? 'checked' : '' }}
                               class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="is_active" class="font-medium text-gray-700">Lezione Attiva</label>
                        <p class="text-gray-500">Se disattivata, la lezione non sarà visibile agli studenti</p>
                    </div>
                </div>

                <!-- Descrizione -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Descrizione</label>
                    <textarea name="description" 
                              id="description" 
                              rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary @error('description') border-red-500 @enderror"
                              placeholder="Descrizione dettagliata della lezione...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Contenuto -->
                <div class="md:col-span-2">
                    <label for="content" class="block text-sm font-medium text-gray-700 mb-2">Contenuto della Lezione</label>
                    <textarea name="content" 
                              id="content" 
                              rows="8"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary @error('content') border-red-500 @enderror"
                              placeholder="Contenuto dettagliato della lezione, istruzioni, note per gli studenti...">{{ old('content') }}</textarea>
                    @error('content')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Section & Course Info -->
            <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                <h3 class="text-sm font-medium text-gray-700 mb-2">Informazioni Sezione e Corso</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500">Corso:</span>
                        <span class="ml-2 font-medium">{{ $course->name }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Sezione:</span>
                        <span class="ml-2 font-medium">{{ $section->name }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Ordine sezione:</span>
                        <span class="ml-2 font-medium">{{ $section->section_order }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Lezioni esistenti:</span>
                        <span class="ml-2 font-medium">{{ $section->lessons->count() }}</span>
                    </div>
                </div>
            </div>

            <!-- Error Summary -->
            @if ($errors->any())
                <div class="mt-6 bg-red-50 border border-red-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Ci sono errori nel form:</h3>
                            <div class="mt-2 text-sm text-red-700">
                                <ul class="list-disc pl-5 space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Actions -->
            <div class="mt-8 flex justify-end space-x-4">
                <a href="{{ route('admin.courses.sections.lessons.index', [$course, $section]) }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                    Annulla
                </a>
                <button type="submit" class="bg-primary hover:bg-primary/90 text-white font-bold py-2 px-4 rounded">
                    Crea Lezione
                </button>
            </div>
        </form>
    </div>

    <!-- Navigation Breadcrumb -->
    <div class="mt-8 bg-white shadow rounded-lg p-6">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('admin.courses.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-primary">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                        </svg>
                        Corsi
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <a href="{{ route('admin.courses.show', $course) }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-primary md:ml-2">{{ Str::limit($course->name, 30) }}</a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <a href="{{ route('admin.courses.sections.show', [$course, $section]) }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-primary md:ml-2">{{ Str::limit($section->name, 30) }}</a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <a href="{{ route('admin.courses.sections.lessons.index', [$course, $section]) }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-primary md:ml-2">Lezioni</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Nuova Lezione</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-focus sul campo titolo
    document.getElementById('title').focus();
    
    // Validazione client-side per l'ordine
    const orderInput = document.getElementById('lesson_order');
    orderInput.addEventListener('input', function() {
        if (this.value < 1) {
            this.value = 1;
        }
    });
    
    // Validazione client-side per la durata
    const durationInput = document.getElementById('duration_minutes');
    durationInput.addEventListener('input', function() {
        if (this.value < 1) {
            this.value = 1;
        } else if (this.value > 300) {
            this.value = 300;
        }
    });

    // Upload con barra di progresso
    const form = document.getElementById('lesson-form');
    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const progressWrap = document.getElementById('upload-progress');
            const progressBar = document.getElementById('upload-progress-bar');
            const progressText = document.getElementById('upload-progress-text');
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn ? submitBtn.textContent : '';

            if (progressWrap) progressWrap.classList.remove('hidden');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Caricamento...';
            }

            const xhr = new XMLHttpRequest();
            xhr.open(form.method, form.action, true);
            xhr.setRequestHeader('Accept', 'application/json');

            xhr.upload.onprogress = function (evt) {
                if (evt.lengthComputable) {
                    const percent = Math.round((evt.loaded / evt.total) * 100);
                    if (progressBar) progressBar.style.width = percent + '%';
                    if (progressText) progressText.textContent = percent + '%';
                }
            };

            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4) {
                    if (xhr.status >= 200 && xhr.status < 300) {
                        try {
                            const res = JSON.parse(xhr.responseText);
                            if (res.redirect) {
                                window.location.href = res.redirect;
                                return;
                            }
                        } catch (e) {}
                        window.location.reload();
                    } else {
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.textContent = originalBtnText || 'Crea Lezione';
                        }
                        if (progressText) {
                            progressText.textContent = 'Errore durante il caricamento';
                            progressText.classList.add('text-red-600');
                        }
                        if (xhr.status === 422) {
                            try {
                                const json = JSON.parse(xhr.responseText);
                                if (json.errors) {
                                    alert(Object.values(json.errors).flat().join('\n'));
                                }
                            } catch (e) {}
                        }
                    }
                }
            };

            const formData = new FormData(form);
            xhr.send(formData);
        });
    }

    // ====== Media Library Modal ======
    const csrfToken = '{{ csrf_token() }}';
    const openModalBtn = document.getElementById('open-media-modal');
    const selectedWrap = document.getElementById('selected-media');
    const selectedUrl = document.getElementById('selected-media-url');
    const clearSelectedBtn = document.getElementById('clear-selected-media');
    const hiddenVideoUrl = document.getElementById('video_url');
    const fileInput = document.getElementById('video_file');

    function toggleRequired() {
        // Se è stata selezionata una URL dalla galleria, il file non è richiesto
        if (hiddenVideoUrl.value) {
            fileInput.removeAttribute('required');
        } else {
            // opzionale: non forziamo required lato client, la validazione server richiede almeno uno dei due
        }
    }

    function setSelectedMedia(url) {
        hiddenVideoUrl.value = url;
        if (selectedWrap && selectedUrl) {
            selectedUrl.textContent = url;
            selectedUrl.href = url;
            selectedWrap.classList.remove('hidden');
        }
        if (fileInput) {
            fileInput.value = '';
        }
        toggleRequired();
    }

    if (clearSelectedBtn) {
        clearSelectedBtn.addEventListener('click', function(){
            hiddenVideoUrl.value = '';
            if (selectedWrap) selectedWrap.classList.add('hidden');
            toggleRequired();
        });
    }

    // Modal markup
    const modalHtml = `
    <div id="media-modal" class="fixed inset-0 z-50 hidden">
      <div class="absolute inset-0 bg-black/50"></div>
      <div class="relative mx-auto mt-16 bg-white rounded-lg shadow-xl w-11/12 max-w-5xl">
        <div class="flex items-center justify-between px-4 py-3 border-b">
          <h3 class="text-lg font-semibold">Galleria Media</h3>
          <button type="button" id="media-modal-close" class="text-gray-500 hover:text-gray-700">✕</button>
        </div>
        <div class="p-4">
          <div class="flex items-center justify-between mb-4">
            <div class="space-x-2">
              <button type="button" data-filter="video" class="media-filter bg-primary text-white px-3 py-1 rounded">Video</button>
              <button type="button" data-filter="image" class="media-filter bg-gray-200 text-gray-800 px-3 py-1 rounded">Immagini</button>
              <button type="button" data-filter="all" class="media-filter bg-gray-200 text-gray-800 px-3 py-1 rounded">Tutti</button>
            </div>
            <div>
              <label class="inline-flex items-center px-3 py-2 bg-gray-100 border rounded cursor-pointer hover:bg-gray-200">
                <input type="file" id="media-upload-input" class="hidden" accept="image/*,video/*">
                <span>Carica Nuovo</span>
              </label>
            </div>
          </div>
          <div id="media-upload-progress" class="hidden mb-3">
            <div class="w-full bg-gray-200 rounded-full h-2">
              <div id="media-upload-progress-bar" class="bg-primary h-2 rounded-full" style="width:0%"></div>
            </div>
            <p id="media-upload-progress-text" class="text-sm text-gray-600 mt-1">0%</p>
          </div>
          <div id="media-grid" class="grid grid-cols-2 md:grid-cols-4 gap-4 min-h-[120px]"></div>
        </div>
      </div>
    </div>`;

    document.body.insertAdjacentHTML('beforeend', modalHtml);
    const mediaModal = document.getElementById('media-modal');
    const mediaGrid = document.getElementById('media-grid');
    const modalClose = document.getElementById('media-modal-close');
    const uploadInput = document.getElementById('media-upload-input');
    const upWrap = document.getElementById('media-upload-progress');
    const upBar = document.getElementById('media-upload-progress-bar');
    const upText = document.getElementById('media-upload-progress-text');

    function openModal() {
        mediaModal.classList.remove('hidden');
        loadMedia('video');
    }
    function closeModal() { mediaModal.classList.add('hidden'); }
    if (openModalBtn) openModalBtn.addEventListener('click', openModal);
    if (modalClose) modalClose.addEventListener('click', closeModal);
    mediaModal.addEventListener('click', (e)=>{ if (e.target === mediaModal.firstElementChild) closeModal(); });

    function renderItems(items) {
        mediaGrid.innerHTML = '';
        if (!items || !items.length) {
            mediaGrid.innerHTML = '<p class="col-span-full text-center text-gray-500">Nessun media disponibile.</p>';
            return;
        }
        items.forEach(item => {
            const card = document.createElement('div');
            card.className = 'border rounded p-2 flex flex-col';
            const preview = document.createElement(item.type === 'image' ? 'img' : 'video');
            preview.className = 'w-full h-28 object-cover rounded';
            preview.src = item.url;
            if (item.type !== 'image') preview.controls = false;
            const name = document.createElement('div');
            name.className = 'mt-2 text-xs break-all';
            name.textContent = item.filename;
            const actions = document.createElement('div');
            actions.className = 'mt-2 flex justify-between';
            const selectBtn = document.createElement('button');
            selectBtn.type = 'button';
            selectBtn.className = 'text-white bg-primary px-2 py-1 rounded text-xs';
            selectBtn.textContent = 'Seleziona';
            selectBtn.addEventListener('click', ()=>{ setSelectedMedia(item.url); closeModal(); });
            const delBtn = document.createElement('button');
            delBtn.type = 'button';
            delBtn.className = 'text-red-600 text-xs hover:underline';
            delBtn.textContent = 'Elimina';
            delBtn.addEventListener('click', ()=>{ deleteMedia(item.id, card); });
            actions.appendChild(selectBtn);
            actions.appendChild(delBtn);
            card.appendChild(preview);
            card.appendChild(name);
            card.appendChild(actions);
            mediaGrid.appendChild(card);
        });
    }

    async function loadMedia(filter) {
        let url = '{{ route('admin.media.list') }}';
        if (filter && filter !== 'all') url += ('?type=' + encodeURIComponent(filter));
        const res = await fetch(url, { headers: { 'Accept':'application/json' } });
        const json = await res.json();
        renderItems(json.data || []);
    }

    async function deleteMedia(id, cardEl) {
        if (!confirm('Eliminare definitivamente questo media?')) return;
        const res = await fetch(`{{ url('admin/media') }}/${id}`, {
            method: 'DELETE',
            headers: { 'Accept':'application/json', 'X-CSRF-TOKEN': csrfToken }
        });
        if (res.ok) {
            cardEl.remove();
        } else {
            alert('Errore durante l\'eliminazione');
        }
    }

    // Filter buttons
    document.addEventListener('click', function(e){
        if (e.target && e.target.classList.contains('media-filter')) {
            const t = e.target.getAttribute('data-filter');
            loadMedia(t === 'all' ? undefined : t);
            document.querySelectorAll('.media-filter').forEach(b=>{
                b.classList.remove('bg-primary','text-white');
                b.classList.add('bg-gray-200','text-gray-800');
            });
            e.target.classList.remove('bg-gray-200','text-gray-800');
            e.target.classList.add('bg-primary','text-white');
        }
    });

    // Upload inside modal
    if (uploadInput) {
        uploadInput.addEventListener('change', function(){
            const file = this.files && this.files[0];
            if (!file) return;
            if (upWrap) upWrap.classList.remove('hidden');
            if (upBar) upBar.style.width = '0%';
            if (upText) upText.textContent = '0%';
            const xhr = new XMLHttpRequest();
            xhr.open('POST', '{{ route('admin.media.store') }}', true);
            xhr.setRequestHeader('Accept','application/json');
            xhr.upload.onprogress = function(evt){
                if (evt.lengthComputable) {
                    const percent = Math.round((evt.loaded / evt.total) * 100);
                    if (upBar) upBar.style.width = percent + '%';
                    if (upText) upText.textContent = percent + '%';
                }
            };
            xhr.onreadystatechange = function(){
                if (xhr.readyState === 4) {
                    if (xhr.status >= 200 && xhr.status < 300) {
                        try { const res = JSON.parse(xhr.responseText); } catch(e) {}
                        loadMedia('video');
                    } else {
                        alert('Errore durante l\'upload media');
                    }
                    uploadInput.value = '';
                }
            };
            const fd = new FormData();
            fd.append('file', file);
            fd.append('_token', csrfToken);
            xhr.send(fd);
        });
    }
});
</script>
</x-layouts.admin>
