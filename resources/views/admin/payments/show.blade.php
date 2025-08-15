<x-layouts.admin>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dettaglio Pagamento #{{ $payment->id }}
            </h2>
            <div class="flex items-center space-x-2">
                <a href="{{ route('admin.payments.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">Torna ai pagamenti</a>
                <a href="{{ route('admin.students.show', $payment->user_id) }}" class="bg-primary hover:bg-primary/90 text-white font-bold py-2 px-4 rounded">Vedi Studente</a>
                @if($payment->course)
                    <a href="{{ route('admin.courses.show', $payment->course_id) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Vedi Corso</a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Informazioni pagamento</h3>
                            <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <dt class="text-sm text-gray-500">Studente</dt>
                                    <dd class="text-sm text-gray-900">
                                        <a class="text-primary hover:underline" href="{{ route('admin.students.show', $payment->user_id) }}">{{ $payment->user->full_name ?? $payment->user->email }}</a>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm text-gray-500">Corso</dt>
                                    <dd class="text-sm text-gray-900">
                                        @if($payment->course)
                                            <a class="text-primary hover:underline" href="{{ route('admin.courses.show', $payment->course_id) }}">{{ $payment->course->name }}</a>
                                        @else
                                            <span class="text-gray-500">N/A</span>
                                        @endif
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm text-gray-500">Stripe Session ID</dt>
                                    <dd class="text-sm font-mono text-gray-900">{{ $payment->stripe_session_id }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm text-gray-500">Payment Intent ID</dt>
                                    <dd class="text-sm font-mono text-gray-900">{{ $payment->stripe_payment_intent_id ?: 'N/A' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm text-gray-500">Importo</dt>
                                    <dd class="text-sm text-gray-900">{{ number_format($payment->amount_total/100, 2, ',', '.') }} {{ strtoupper($payment->currency) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm text-gray-500">Stato</dt>
                                    <dd class="text-sm text-gray-900">
                                        @php
                                            $badge = match($payment->status) {
                                                'paid' => 'bg-green-100 text-green-800',
                                                'open' => 'bg-yellow-100 text-yellow-800',
                                                'complete' => 'bg-blue-100 text-blue-800',
                                                default => 'bg-gray-100 text-gray-800',
                                            };
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badge }}">{{ strtoupper($payment->status) }}</span>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm text-gray-500">Data creazione</dt>
                                    <dd class="text-sm text-gray-900">{{ $payment->created_at->format('d/m/Y H:i') }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Dati cliente (Stripe)</h3>
                            @if($payment->customer_details)
                                <pre class="text-xs bg-gray-50 border rounded p-4 overflow-auto">{{ json_encode($payment->customer_details, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
                            @else
                                <p class="text-sm text-gray-500">Nessun dato cliente disponibile.</p>
                            @endif
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Campi personalizzati (Stripe Checkout)</h3>
                            @if($payment->custom_fields)
                                <pre class="text-xs bg-gray-50 border rounded p-4 overflow-auto">{{ json_encode($payment->custom_fields, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
                            @else
                                <p class="text-sm text-gray-500">Nessun campo personalizzato rilevato.</p>
                            @endif
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Metadata</h3>
                            @if($payment->metadata)
                                <pre class="text-xs bg-gray-50 border rounded p-4 overflow-auto">{{ json_encode($payment->metadata, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
                            @else
                                <p class="text-sm text-gray-500">Nessun metadata.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h4 class="text-lg font-medium text-gray-900 mb-4">Riepilogo</h4>
                            <ul class="text-sm space-y-2">
                                <li><span class="text-gray-500">Email cliente:</span> <span class="text-gray-900">{{ $payment->customer_email ?: ($payment->user->email ?? 'N/A') }}</span></li>
                                <li><span class="text-gray-500">Studente:</span> <span class="text-gray-900">{{ $payment->user->full_name ?? $payment->user->email }}</span></li>
                                <li><span class="text-gray-500">Corso:</span> <span class="text-gray-900">{{ $payment->course->name ?? 'N/A' }}</span></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
