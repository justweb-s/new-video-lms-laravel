<x-layouts.admin>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Gift Card: {{ $giftcard->code }}
                </h2>
                <p class="text-sm text-gray-500">ID #{{ $giftcard->id }}</p>
            </div>
            <div class="flex items-center gap-2">
                <form method="POST" action="{{ route('admin.giftcards.resend', $giftcard) }}" onsubmit="return confirm('Reinviare l\'email a {{ $giftcard->recipient_email }}?');">
                    @csrf
                    <button type="submit" class="text-sm bg-primary hover:bg-primary/90 text-white px-3 py-2 rounded">Reinvia email</button>
                </form>
                <a href="{{ route('admin.giftcards.index') }}" class="text-sm bg-gray-100 hover:bg-gray-200 text-gray-800 px-3 py-2 rounded">Torna all'elenco</a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Dettagli principali -->
                <div class="lg:col-span-2 bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 space-y-6">
                        <div class="flex items-center gap-3">
                            @php
                                $badge = match($giftcard->status) {
                                    'paid' => 'bg-green-100 text-green-800',
                                    'redeemed' => 'bg-blue-100 text-blue-800',
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'canceled' => 'bg-red-100 text-red-800',
                                    default => 'bg-gray-100 text-gray-800',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badge }}">{{ strtoupper($giftcard->status) }}</span>
                            <span class="text-sm text-gray-500">Creata: {{ $giftcard->created_at?->format('d/m/Y H:i') }}</span>
                            @if($giftcard->issued_at)
                                <span class="text-sm text-gray-500">Emessa: {{ $giftcard->issued_at->format('d/m/Y H:i') }}</span>
                            @endif
                            @if($giftcard->redeemed_at)
                                <span class="text-sm text-gray-500">Riscattata: {{ $giftcard->redeemed_at->format('d/m/Y H:i') }}</span>
                            @endif
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Codice</h3>
                                <p class="mt-1 font-mono text-lg">{{ $giftcard->code }}</p>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Valore</h3>
                                <p class="mt-1 text-lg font-semibold">{{ number_format($giftcard->amount/100, 2, ',', '.') }} {{ strtoupper($giftcard->currency) }}</p>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Corso</h3>
                                @if($giftcard->course)
                                    <a class="mt-1 inline-block text-primary hover:underline" href="{{ route('admin.courses.show', $giftcard->course_id) }}">{{ $giftcard->course->name }}</a>
                                @else
                                    <p class="mt-1 text-gray-500">N/A</p>
                                @endif
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Acquirente</h3>
                                @if($giftcard->buyer)
                                    <a class="mt-1 inline-block text-primary hover:underline" href="{{ route('admin.students.show', $giftcard->buyer_user_id) }}">{{ $giftcard->buyer->full_name ?? $giftcard->buyer->email }}</a>
                                @else
                                    <p class="mt-1 text-gray-500">N/A</p>
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Destinatario</h3>
                                <p class="mt-1">{{ $giftcard->recipient_name }}</p>
                                <p class="text-sm text-gray-500">{{ $giftcard->recipient_email }}</p>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Utilizzatore</h3>
                                @if($giftcard->redeemer)
                                    <a class="mt-1 inline-block text-primary hover:underline" href="{{ route('admin.students.show', $giftcard->redeemer_user_id) }}">{{ $giftcard->redeemer->full_name ?? $giftcard->redeemer->email }}</a>
                                @else
                                    <p class="mt-1 text-gray-500">—</p>
                                @endif
                            </div>
                        </div>

                        @if(!empty($giftcard->message))
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Messaggio</h3>
                                <div class="mt-1 p-3 rounded bg-gray-50 border border-gray-200 whitespace-pre-wrap">{{ $giftcard->message }}</div>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Stripe Session</h3>
                                <p class="mt-1 font-mono text-sm">{{ $giftcard->stripe_session_id ?? '—' }}</p>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Payment Intent</h3>
                                <p class="mt-1 font-mono text-sm">{{ $giftcard->stripe_payment_intent_id ?? '—' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Riepilogo pagamento -->
                <div class="bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="font-semibold text-gray-800">Pagamento</h3>
                        @if($payment)
                            <dl class="mt-4 space-y-2">
                                <div>
                                    <dt class="text-sm text-gray-500">Data</dt>
                                    <dd class="text-sm">{{ $payment->created_at?->format('d/m/Y H:i') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm text-gray-500">Importo</dt>
                                    <dd class="text-sm">{{ number_format($payment->amount_total/100, 2, ',', '.') }} {{ strtoupper($payment->currency) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm text-gray-500">Stato</dt>
                                    @php
                                        $pBadge = match($payment->status) {
                                            'paid' => 'bg-green-100 text-green-800',
                                            'open' => 'bg-yellow-100 text-yellow-800',
                                            'complete' => 'bg-blue-100 text-blue-800',
                                            default => 'bg-gray-100 text-gray-800',
                                        };
                                    @endphp
                                    <dd class="text-sm"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $pBadge }}">{{ strtoupper($payment->status) }}</span></dd>
                                </div>
                                <div class="pt-2">
                                    <a href="{{ route('admin.payments.show', $payment) }}" class="inline-flex items-center text-sm bg-gray-800 hover:bg-gray-900 text-white px-3 py-1.5 rounded">Vai al pagamento</a>
                                </div>
                            </dl>
                        @else
                            <p class="mt-2 text-sm text-gray-500">Nessun pagamento collegato trovato.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
