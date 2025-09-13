<x-layouts.admin>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Import/Export Dati</h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-2 gap-6">
                <!-- Export -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold mb-4">Esporta Dati</h3>

                        <div class="space-y-3">
                            <div>
                                <div class="font-medium">Corsi (con Sezioni e Lezioni)</div>
                                <p class="text-sm text-gray-600 mb-2">Formato: JSON (struttura annidata)</p>
                                <a href="{{ route('admin.data.export.courses') }}" class="inline-flex items-center px-4 py-2 bg-primary text-white rounded hover:bg-primary/90">Scarica JSON</a>
                            </div>

                            <div class="border-t pt-4">
                                <div class="font-medium">Studenti</div>
                                <p class="text-sm text-gray-600 mb-2">Formato: CSV</p>
                                <a href="{{ route('admin.data.export.students') }}" class="inline-flex items-center px-4 py-2 bg-primary text-white rounded hover:bg-primary/90">Scarica CSV</a>
                            </div>

                            <div class="border-t pt-4">
                                <div class="font-medium">Iscrizioni</div>
                                <p class="text-sm text-gray-600 mb-2">Formato: CSV</p>
                                <a href="{{ route('admin.data.export.enrollments') }}" class="inline-flex items-center px-4 py-2 bg-primary text-white rounded hover:bg-primary/90">Scarica CSV</a>
                            </div>

                            <div class="border-t pt-4">
                                <div class="font-medium">Pagamenti/Ordini</div>
                                <p class="text-sm text-gray-600 mb-2">Formato: CSV</p>
                                <a href="{{ route('admin.data.export.payments') }}" class="inline-flex items-center px-4 py-2 bg-primary text-white rounded hover:bg-primary/90">Scarica CSV</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Import -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold mb-4">Importa Dati</h3>

                        <!-- Import Corsi -->
                        <div class="mb-8">
                            <div class="font-medium mb-2">Corsi (JSON)</div>
                            <p class="text-sm text-gray-600 mb-2">Carica un file JSON con la chiave <code>courses</code> e gli array annidati di <code>sections</code> e <code>lessons</code>.</p>
                            <form action="{{ route('admin.data.import.courses') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                                @csrf
                                <input type="file" name="file" accept="application/json,.json" required class="block w-full">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="replace_children" value="1" class="mr-2">
                                    <span class="text-sm">Sostituisci completamente sezioni e lezioni esistenti</span>
                                </label>
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Importa Corsi</button>
                            </form>
                        </div>

                        <!-- Import Studenti -->
                        <div class="mb-8 border-t pt-6">
                            <div class="font-medium mb-2">Studenti (CSV)</div>
                            <p class="text-sm text-gray-600 mb-2">Intestazioni supportate: <code>email,name,first_name,last_name,phone,is_active,tax_code,tax_id,billing_address_line1,billing_address_line2,billing_city,billing_state,billing_postal_code,billing_country,password</code></p>
                            <form action="{{ route('admin.data.import.students') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                                @csrf
                                <input type="file" name="file" accept="text/csv,.csv" required class="block w-full">
                                <div class="flex items-center gap-3">
                                    <label class="text-sm">Password di default (se assente nel CSV):</label>
                                    <input type="text" name="default_password" class="border rounded px-2 py-1" placeholder="es. 123456">
                                </div>
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Importa Studenti</button>
                            </form>
                        </div>

                        <!-- Import Iscrizioni -->
                        <div class="mb-8 border-t pt-6">
                            <div class="font-medium mb-2">Iscrizioni (CSV)</div>
                            <p class="text-sm text-gray-600 mb-2">Intestazioni supportate: <code>user_email,course_id,course_name,enrolled_at,expires_at,is_active,progress_percentage</code></p>
                            <form action="{{ route('admin.data.import.enrollments') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                                @csrf
                                <input type="file" name="file" accept="text/csv,.csv" required class="block w-full">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Importa Iscrizioni</button>
                            </form>
                        </div>

                        <!-- Import Pagamenti -->
                        <div class="border-t pt-6">
                            <div class="font-medium mb-2">Pagamenti/Ordini (CSV)</div>
                            <p class="text-sm text-gray-600 mb-2">Intestazioni supportate: <code>user_email,course_id,course_name,provider,amount_total,currency,status,stripe_session_id,stripe_payment_intent_id,paypal_order_id,paypal_capture_id,customer_email</code></p>
                            <form action="{{ route('admin.data.import.payments') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                                @csrf
                                <input type="file" name="file" accept="text/csv,.csv" required class="block w-full">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Importa Pagamenti</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
