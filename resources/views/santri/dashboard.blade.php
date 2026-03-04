{{-- filepath: resources/views/santri/dashboard.blade.php --}}
@extends('layouts.santri')

@section('content')
    <div class="space-y-6">
        <!-- Single Blue Card Full Width -->
        <div class="bg-blue-600 rounded-lg shadow p-8">
            <div class="flex items-center gap-4 text-white">
                <div class="text-5xl"><i class="fa fa-users"></i></div>
                <div>
                    <div class="font-bold text-2xl">Total Santri</div>
                    <div class="text-4xl font-bold">400</div>
                </div>
            </div>
        </div>

        <!-- Notifikasi & Kalender Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Notifikasi -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="font-bold text-xl mb-6">Notifikasi</div>
                <div class="space-y-4">
                    <div class="flex items-center justify-between border-b pb-3">
                        <div class="flex items-center gap-3">
                            <i class="fa fa-check-circle text-green-500 text-lg"></i>
                            <span class="font-medium">Pembayaran sudah terverifikasi</span>
                        </div>
                        <span class="text-sm text-gray-500">Jam : 00:01</span>
                    </div>
                    <div class="flex items-center justify-between border-b pb-3">
                        <div class="flex items-center gap-3">
                            <i class="fa fa-check-circle text-green-500 text-lg"></i>
                            <span class="font-medium">Pembayaran sudah terverifikasi</span>
                        </div>
                        <span class="text-sm text-gray-500">Jam : 00:01</span>
                    </div>
                </div>
            </div>

            <!-- Kalender -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="font-bold text-xl mb-4 text-center">Kalender</div>
                <div class="text-center text-sm text-gray-600 mb-4">Februari 2025</div>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-gray-500 border-b">
                            <th class="py-2 text-center">Sen</th>
                            <th class="py-2 text-center">Sel</th>
                            <th class="py-2 text-center">Rab</th>
                            <th class="py-2 text-center">Kam</th>
                            <th class="py-2 text-center">Jum</th>
                            <th class="py-2 text-center">Sab</th>
                            <th class="py-2 text-center">Ming</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        <tr>
                            <td class="py-1"></td>
                            <td class="py-1"></td>
                            <td class="py-1"></td>
                            <td class="py-1"></td>
                            <td class="py-1"></td>
                            <td class="py-1">1</td>
                            <td class="py-1">2</td>
                        </tr>
                        <tr>
                            <td class="py-1">3</td>
                            <td class="py-1">4</td>
                            <td class="py-1">5</td>
                            <td class="py-1">6</td>
                            <td class="py-1">7</td>
                            <td class="py-1">8</td>
                            <td class="py-1">9</td>
                        </tr>
                        <tr>
                            <td class="py-1">10</td>
                            <td class="py-1">11</td>
                            <td class="py-1">12</td>
                            <td class="py-1">13</td>
                            <td class="py-1">14</td>
                            <td class="py-1">15</td>
                            <td class="py-1">16</td>
                        </tr>
                        <tr>
                            <td class="py-1">17</td>
                            <td class="py-1">18</td>
                            <td class="py-1">19</td>
                            <td class="py-1">20</td>
                            <td class="py-1">21</td>
                            <td class="py-1">22</td>
                            <td class="py-1">23</td>
                        </tr>
                        <tr>
                            <td class="py-1">24</td>
                            <td class="py-1">25</td>
                            <td class="py-1">26</td>
                            <td class="py-1">27</td>
                            <td class="py-1">28</td>
                            <td class="py-1"></td>
                            <td class="py-1"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
