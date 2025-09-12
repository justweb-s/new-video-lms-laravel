<x-layouts.admin>
<div class="container mx-auto px-6 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Galleria Media</h1>
            <p class="text-gray-600 mt-1">Gestisci immagini e video caricati. Puoi caricare nuovi file, selezionare, o eliminare.</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">Dashboard</a>
    </div>

    <!-- Toolbar -->
    <div class="bg-white rounded shadow p-4 mb-4 flex items-center justify-between">
        <div class="space-x-2">
            <button type="button" data-filter="all" class="media-filter bg-primary text-white px-3 py-1 rounded">Tutti</button>
            <button type="button" data-filter="image" class="media-filter bg-gray-200 text-gray-800 px-3 py-1 rounded">Immagini</button>
            <button type="button" data-filter="video" class="media-filter bg-gray-200 text-gray-800 px-3 py-1 rounded">Video</button>
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

    <!-- Grid -->
    <div class="bg-white rounded shadow p-4">
        <div id="media-grid" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 min-h-[160px]"></div>
    </div>
</div>

<script>
(function(){
    const csrfToken = '{{ csrf_token() }}';
    const grid = document.getElementById('media-grid');
    const uploadInput = document.getElementById('media-upload-input');
    const upWrap = document.getElementById('media-upload-progress');
    const upBar = document.getElementById('media-upload-progress-bar');
    const upText = document.getElementById('media-upload-progress-text');

    async function loadMedia(filter) {
        try {
            let url = '{{ route('admin.media.list') }}';
            if (filter && filter !== 'all') url += ('?type=' + encodeURIComponent(filter));
            const res = await fetch(url, { headers: { 'Accept':'application/json' } });
            const json = await res.json();
            renderItems(json.data || []);
        } catch(e) {
            grid.innerHTML = '<p class="col-span-full text-center text-red-600">Errore nel caricamento della galleria</p>';
        }
    }

    function renderItems(items) {
        grid.innerHTML = '';
        if (!items.length) {
            grid.innerHTML = '<p class="col-span-full text-center text-gray-500">Nessun media presente.</p>';
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
            const openBtn = document.createElement('a');
            openBtn.href = item.url;
            openBtn.target = '_blank';
            openBtn.className = 'text-primary text-xs hover:underline';
            openBtn.textContent = 'Apri';
            const delBtn = document.createElement('button');
            delBtn.type = 'button';
            delBtn.className = 'text-red-600 text-xs hover:underline';
            delBtn.textContent = 'Elimina';
            delBtn.addEventListener('click', ()=> deleteMedia(item.id, card));
            actions.appendChild(openBtn);
            actions.appendChild(delBtn);
            card.appendChild(preview);
            card.appendChild(name);
            card.appendChild(actions);
            grid.appendChild(card);
        });
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
            alert('Impossibile eliminare il media');
        }
    }

    document.addEventListener('click', function(e){
        if (e.target && e.target.classList.contains('media-filter')) {
            document.querySelectorAll('.media-filter').forEach(b=>{
                b.classList.remove('bg-primary','text-white');
                b.classList.add('bg-gray-200','text-gray-800');
            });
            e.target.classList.remove('bg-gray-200','text-gray-800');
            e.target.classList.add('bg-primary','text-white');
            const f = e.target.getAttribute('data-filter');
            loadMedia(f);
        }
    });

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
                        loadMedia('all');
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

    // initial load
    loadMedia('all');
})();
</script>
</x-layouts.admin>
