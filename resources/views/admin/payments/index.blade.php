<x-layouts.admin>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Pagamenti
            </h2>
            <form method="GET" action="{{ route('admin.payments.index') }}" class="flex items-center space-x-2">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cerca session/intent" class="border rounded px-3 py-2 text-sm" />
                <select name="status" class="border rounded px-3 py-2 text-sm">
                    <option value="">Tutti gli stati</option>
                    @php $statuses = ['paid','unpaid','no_payment_required','open','complete']; @endphp
                    @foreach($statuses as $st)
                        <option value="{{ $st }}" @selected(request('status')===$st)>{{ strtoupper($st) }}</option>
                    @endforeach
                </select>
                @if(request()->filled('user'))
                    <input type="hidden" name="user" value="{{ (int)request('user') }}" />
                @endif
                @if(request()->filled('course'))
                    <input type="hidden" name="course" value="{{ (int)request('course') }}" />
                @endif
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
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Studente</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Corso</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Importo</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stato</th>
                                    <th class="px-4 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($payments as $p)
                                    <tr>
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ $p->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="px-4 py-3 text-sm">
                                            <a class="text-primary hover:underline" href="{{ route('admin.students.show', $p->user_id) }}">{{ $p->user->full_name ?? $p->user->email }}</a>
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            @if($p->course)
                                                <a class="text-primary hover:underline" href="{{ route('admin.courses.show', $p->course_id) }}">{{ $p->course->name }}</a>
                                            @else
                                                <span class="text-gray-500">N/A</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm">{{ number_format($p->amount_total/100, 2, ',', '.') }} {{ strtoupper($p->currency) }}</td>
                                        <td class="px-4 py-3 text-sm">
                                            @php
                                                $badge = match($p->status) {
                                                    'paid' => 'bg-green-100 text-green-800',
                                                    'open' => 'bg-yellow-100 text-yellow-800',
                                                    'complete' => 'bg-blue-100 text-blue-800',
                                                    default => 'bg-gray-100 text-gray-800',
                                                };
                                            @endphp
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badge }}">{{ strtoupper($p->status) }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <a href="{{ route('admin.payments.show', $p) }}" class="text-sm bg-gray-800 hover:bg-gray-900 text-white px-3 py-1.5 rounded">Dettagli</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-6 text-center text-gray-500">Nessun pagamento trovato.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $payments->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
