<x-layouts.admin>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dettagli Studente: {{ $student->full_name }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('admin.students.edit', $student) }}" class="bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                    Modifica
                </a>
                <a href="{{ route('admin.students.enrollments', $student) }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    Gestisci Iscrizioni
                </a>
                <a href="{{ route('admin.students.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Torna alla Lista
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Student Details -->
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0 h-20 w-20 bg-gray-300 rounded-full flex items-center justify-center">
                                    <span class="text-2xl font-medium text-gray-700">{{ substr($student->name, 0, 1) }}</span>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $student->full_name }}</h3>
                                    <div class="flex items-center space-x-4 mb-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $student->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $student->is_active ? 'Attivo' : 'Inattivo' }}
                                        </span>
                                        <span class="text-sm text-gray-500">Registrato il {{ $student->created_at->format('d/m/Y') }}</span>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4 text-sm">
                                        <div>
                                            <span class="font-medium text-gray-700">Email:</span>
                                            <span class="text-gray-900">{{ $student->email }}</span>
                                        </div>
                                        <div>
                                            <span class="font-medium text-gray-700">Telefono:</span>
                                            <span class="text-gray-900">{{ $student->phone ?: 'N/A' }}</span>
                                        </div>
                                        <div>
                                            <span class="font-medium text-gray-700">Iscrizioni:</span>
                                            <span class="text-gray-900">{{ $student->enrollments_count }} corsi</span>
                                        </div>
                                        <div>
                                            <span class="font-medium text-gray-700">Ultimo accesso:</span>
                                            <span class="text-gray-900">{{ $student->last_login ? $student->last_login->format('d/m/Y H:i') : 'Mai' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Enrollments -->
                    @if($student->enrollments->count() > 0)
                        <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h4 class="text-lg font-medium text-gray-900 mb-4">Corsi Iscritti</h4>
                                <div class="space-y-4">
                                    @foreach($student->enrollments as $enrollment)
                                        <div class="border border-gray-200 rounded-lg p-4">
                                            <div class="flex justify-between items-start">
                                                <div class="flex-1">
                                                    <h5 class="font-medium text-gray-900">{{ $enrollment->course->name }}</h5>
                                                    <p class="text-sm text-gray-600 mt-1">{{ $enrollment->course->description }}</p>
                                                    <div class="flex items-center space-x-4 mt-2 text-xs text-gray-500">
                                                        <span>Iscritto il {{ $enrollment->enrolled_at->format('d/m/Y') }}</span>
                                                        <span>Progresso: {{ number_format($enrollment->progress_percentage, 1) }}%</span>
                                                        @if($enrollment->expires_at)
                                                            <span>Scade il {{ $enrollment->expires_at->format('d/m/Y') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="flex items-center space-x-2">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $enrollment->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                        {{ $enrollment->is_active ? 'Attiva' : 'Inattiva' }}
                                                    </span>
                                                    <div class="w-16 bg-gray-200 rounded-full h-2">
                                                        <div class="bg-primary h-2 rounded-full" style="width: {{ $enrollment->progress_percentage }}%"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Recent Progress -->
                    @if($recentProgress->count() > 0)
                        <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h4 class="text-lg font-medium text-gray-900 mb-4">Progresso Recente</h4>
                                <div class="space-y-3">
                                    @foreach($recentProgress as $progress)
                                        <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-b-0">
                                            <div class="flex-1">
                                                <p class="text-sm font-medium text-gray-900">{{ $progress->lesson->title }}</p>
                                                <p class="text-xs text-gray-500">{{ $progress->lesson->section->course->name }} - {{ $progress->lesson->section->name }}</p>
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                @if($progress->completed)
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        Completata
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                        {{ number_format($progress->progress_percentage, 1) }}%
                                                    </span>
                                                @endif
                                                <span class="text-xs text-gray-500">{{ $progress->updated_at->format('d/m H:i') }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Billing Data -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h4 class="text-lg font-medium text-gray-900 mb-4">Dati di Fatturazione</h4>
                            <dl class="text-sm space-y-2">
                                <div class="flex justify-between">
                                    <dt class="text-gray-600">Codice Fiscale</dt>
                                    <dd class="text-gray-900">{{ $student->tax_code ?: 'N/D' }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-600">P. IVA</dt>
                                    <dd class="text-gray-900">{{ $student->tax_id ?: 'N/D' }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-600">Telefono</dt>
                                    <dd class="text-gray-900">{{ $student->phone ?: 'N/D' }}</dd>
                                </div>
                                <div class="border-t my-2"></div>
                                <div>
                                    <dt class="text-gray-600">Indirizzo</dt>
                                    <dd class="text-gray-900">
                                        @php
                                            $addr = array_filter([
                                                $student->billing_address_line1,
                                                $student->billing_address_line2,
                                                $student->billing_postal_code,
                                                $student->billing_city,
                                                $student->billing_state,
                                                $student->billing_country,
                                            ]);
                                        @endphp
                                        {{ $addr ? implode(', ', $addr) : 'N/D' }}
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Statistics -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h4 class="text-lg font-medium text-gray-900 mb-4">Statistiche</h4>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Corsi iscritti:</span>
                                    <span class="text-sm font-medium text-gray-900">{{ $student->enrollments_count }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Lezioni completate:</span>
                                    <span class="text-sm font-medium text-gray-900">{{ $student->lessonProgress->where('completed', true)->count() }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Progresso totale:</span>
                                    <span class="text-sm font-medium text-gray-900">{{ $student->lessonProgress->count() > 0 ? number_format($student->lessonProgress->avg('progress_percentage'), 1) : 0 }}%</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Ultimo accesso:</span>
                                    <span class="text-sm font-medium text-gray-900">{{ $student->last_login ? $student->last_login->diffForHumans() : 'Mai' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Payments -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-lg font-medium text-gray-900">Ultimi pagamenti</h4>
                                <a href="{{ route('admin.payments.index', ['user' => $student->id]) }}" class="text-sm text-primary hover:underline">Vedi tutti</a>
                            </div>
                            @if(isset($recentPayments) && $recentPayments->count())
                                <ul class="divide-y divide-gray-100">
                                    @foreach($recentPayments as $pay)
                                        <li class="py-2 text-sm flex items-center justify-between">
                                            <div>
                                                <div class="font-medium text-gray-900">{{ $pay->course->name ?? 'Corso N/D' }}</div>
                                                <div class="text-gray-500">{{ $pay->created_at->format('d/m/Y H:i') }} • {{ strtoupper($pay->currency) }} {{ number_format($pay->amount_total/100, 2, ',', '.') }}</div>
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                @php
                                                    $badge = match($pay->status) {
                                                        'paid' => 'bg-green-100 text-green-800',
                                                        'open' => 'bg-yellow-100 text-yellow-800',
                                                        'complete' => 'bg-blue-100 text-blue-800',
                                                        default => 'bg-gray-100 text-gray-800',
                                                    };
                                                @endphp
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badge }}">{{ strtoupper($pay->status) }}</span>
                                                <a href="{{ route('admin.payments.show', $pay) }}" class="text-xs bg-gray-800 hover:bg-gray-900 text-white px-2 py-1 rounded">Dettagli</a>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-sm text-gray-500">Nessun pagamento recente.</p>
                            @endif
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h4 class="text-lg font-medium text-gray-900 mb-4">Azioni</h4>
                            <div class="space-y-2">
                                <a href="{{ route('admin.students.edit', $student) }}" class="block w-full text-center bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                                    Modifica Studente
                                </a>
                                <a href="{{ route('admin.students.enrollments', $student) }}" class="block w-full text-center bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                    Gestisci Iscrizioni
                                </a>
                                <form action="{{ route('admin.students.destroy', $student) }}" method="POST" onsubmit="return confirm('Sei sicuro di voler eliminare questo studente?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="block w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                        Elimina Studente
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
