<x-layouts.admin>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Gift Card
            </h2>
            <form method="GET" action="{{ route('admin.giftcards.index') }}" class="flex flex-wrap items-center gap-2">
                <input type="text" name="code" value="{{ request('code') }}" placeholder="Codice" class="border rounded px-3 py-2 text-sm" />
                <input type="text" name="email" value="{{ request('email') }}" placeholder="Email (dest/buyer/redeemer)" class="border rounded px-3 py-2 text-sm" />
                <select name="status" class="border rounded px-3 py-2 text-sm">
                    <option value="">Tutti gli stati</option>
                    @php $statuses = ['pending','paid','redeemed','canceled']; @endphp
                    @foreach($statuses as $st)
                        <option value="{{ $st }}" @selected(request('status')===$st)>{{ strtoupper($st) }}</option>
                    @endforeach
                </select>
                <select name="course_id" class="border rounded px-3 py-2 text-sm">
                    <option value="">Tutti i corsi</option>
                    @foreach($courses as $c)
                        <option value="{{ $c->id }}" @selected((string)request('course_id')===(string)$c->id)>{{ $c->name }}</option>
                    @endforeach
                </select>
                <button class="bg-primary hover:bg-primary/90 text-white font-semibold px-4 py-2 rounded">Filtra</button>
            </form>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Codice</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Corso</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acquirente</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Destinatario</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Importo</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stato</th>
                                    <th class="px-4 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($giftcards as $g)
                                    <tr>
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ optional($g->issued_at ?? $g->created_at)->format('d/m/Y H:i') }}</td>
                                        <td class="px-4 py-3 text-sm font-mono">{{ $g->code }}</td>
                                        <td class="px-4 py-3 text-sm">
                                            @if($g->course)
                                                <a class="text-primary hover:underline" href="{{ route('admin.courses.show', $g->course_id) }}">{{ $g->course->name }}</a>
                                            @else
                                                <span class="text-gray-500">N/A</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            @if($g->buyer)
                                                <a class="text-primary hover:underline" href="{{ route('admin.students.show', $g->buyer_user_id) }}">{{ $g->buyer->full_name ?? $g->buyer->email }}</a>
                                            @else
                                                <span class="text-gray-500">N/A</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            <div>{{ $g->recipient_name }}</div>
                                            <div class="text-gray-500 text-xs">{{ $g->recipient_email }}</div>
                                        </td>
                                        <td class="px-4 py-3 text-sm">{{ number_format($g->amount/100, 2, ',', '.') }} {{ strtoupper($g->currency) }}</td>
                                        <td class="px-4 py-3 text-sm">
                                            @php
                                                $status = $g->status;
                                                $badge = match($status) {
                                                    'paid' => 'bg-green-100 text-green-800',
                                                    'redeemed' => 'bg-blue-100 text-blue-800',
                                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                                    'canceled' => 'bg-red-100 text-red-800',
                                                    default => 'bg-gray-100 text-gray-800',
                                                };
                                            @endphp
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badge }}">{{ strtoupper($status) }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <a href="{{ route('admin.giftcards.show', $g) }}" class="text-sm bg-gray-800 hover:bg-gray-900 text-white px-3 py-1.5 rounded">Dettagli</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-4 py-6 text-center text-gray-500">Nessuna gift card trovata.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $giftcards->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
