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
                                    <dt class="text-sm text-gray-500">Provider</dt>
                                    <dd class="text-sm text-gray-900">{{ strtoupper($payment->provider ?? 'stripe') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm text-gray-500">PayPal Order ID</dt>
                                    <dd class="text-sm font-mono text-gray-900">{{ $payment->paypal_order_id ?: 'N/A' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm text-gray-500">PayPal Capture ID</dt>
                                    <dd class="text-sm font-mono text-gray-900">{{ $payment->paypal_capture_id ?: 'N/A' }}</dd>
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
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Dati cliente</h3>
                            @if($payment->customer_details)
                                @php $details = $payment->customer_details; @endphp
                                <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-2 text-sm">
                                    <div class="md:col-span-2"><dt class="text-gray-500">Nome/Ragione Sociale</dt><dd class="text-gray-900 font-medium">{{ $details['name'] ?? 'N/D' }}</dd></div>
                                    <div><dt class="text-gray-500">Email</dt><dd class="text-gray-900">{{ $details['email'] ?? 'N/D' }}</dd></div>
                                    <div><dt class="text-gray-500">Telefono</dt><dd class="text-gray-900">{{ $details['phone'] ?? 'N/D' }}</dd></div>
                                    @if(isset($details['address']) && is_array($details['address']))
                                        <div class="md:col-span-2">
                                            <dt class="text-gray-500">Indirizzo</dt>
                                            <dd class="text-gray-900">{{ collect([$details['address']['line1'], $details['address']['line2'], $details['address']['city'], $details['address']['state'], $details['address']['postal_code'], $details['address']['country']])->filter()->implode(', ') ?: 'Non specificato' }}</dd>
                                        </div>
                                    @endif
                                    @if(isset($details['tax_ids']) && !empty($details['tax_ids']))
                                        @foreach($details['tax_ids'] as $taxId)
                                            <div>
                                                <dt class="text-gray-500">{{ $taxId['type'] === 'eu_vat' ? 'P.IVA (EU VAT)' : 'ID Fiscale' }}</dt>
                                                <dd class="text-gray-900 font-mono">{{ $taxId['value'] }}</dd>
                                            </div>
                                        @endforeach
                                    @endif
                                    <div><dt class="text-gray-500">Esenzione Fiscale</dt><dd class="text-gray-900">{{ $details['tax_exempt'] ?? 'N/D' }}</dd></div>
                                </dl>
                            @else
                                <p class="text-sm text-gray-500">Nessun dato cliente disponibile.</p>
                            @endif
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Campi personalizzati (Stripe Checkout)</h3>
                            @if($payment->custom_fields)
                                <div class="overflow-x-auto">
                                    <table class="min-w-full text-sm">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-4 py-2 text-left font-medium text-gray-600">Etichetta</th>
                                                <th class="px-4 py-2 text-left font-medium text-gray-600">Valore</th>
                                                <th class="px-4 py-2 text-left font-medium text-gray-600">Chiave</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($payment->custom_fields as $field)
                                                <tr>
                                                    <td class="px-4 py-2">{{ $field['label']['custom'] ?? 'N/D' }}</td>
                                                    <td class="px-4 py-2 font-mono">{{ $field['text']['value'] ?? 'N/D' }}</td>
                                                    <td class="px-4 py-2 font-mono text-gray-500">{{ $field['key'] }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-sm text-gray-500">Nessun campo personalizzato rilevato.</p>
                            @endif
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Metadata</h3>
                            @if($payment->metadata)
                                <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-2 text-sm">
                                    @foreach($payment->metadata as $key => $value)
                                        <div>
                                            <dt class="text-gray-500 capitalize">{{ str_replace('_', ' ', $key) }}</dt>
                                            <dd class="text-gray-900 font-mono">{{ $value }}</dd>
                                        </div>
                                    @endforeach
                                </dl>
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
