<?php
/**
 * API Manager - API Aggregators Documentation
 */

// Start session
session_start();

// Include required files
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';

// Set current page for sidebar highlight
$currentPage = 'docs';
$pageTitle = 'API Aggregators Documentation';

// Include header
require_once __DIR__ . '/../includes/head.php';
require_once __DIR__ . '/../includes/sidebar.php';
?>

<!-- Page header -->
<div class="mb-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 mb-2">API Aggregators Documentation</h1>
            <p class="text-gray-600">Comprehensive guide for managing API Aggregators</p>
        </div>
        
        <a href="/docs/index.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-2"></i> Back to Docs
        </a>
    </div>
</div>

<?php displayMessage(); ?>

<!-- Documentation Content -->
<div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
    <div class="p-6">
        <section class="mb-8">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Apa itu API Aggregator?</h2>
            <p class="text-gray-700 mb-4">
                API Aggregator adalah penyedia layanan API yang mengumpulkan berbagai layanan API di bawah satu platform.
                Dalam konteks sistem ini, Aggregator adalah entitas utama yang berisi informasi koneksi untuk berinteraksi
                dengan layanan API eksternal.
            </p>
            
            <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4 mb-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-lightbulb text-indigo-500 text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-indigo-800">Contoh Penggunaan:</h3>
                        <p class="text-sm text-indigo-700 mt-1">
                            Jika Anda bekerja dengan beberapa penyedia game (seperti Pragmatic Play, Microgaming, dll), 
                            setiap penyedia tersebut dapat dianggap sebagai "Aggregator" terpisah dengan kredensial akses 
                            dan endpoint yang unik.
                        </p>
                    </div>
                </div>
            </div>
        </section>
        
        <section class="mb-8">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Field Penting pada API Aggregator</h2>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Field</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contoh</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Name</td>
                            <td class="px-6 py-4 text-sm text-gray-500">Nama aggregator untuk identifikasi internal</td>
                            <td class="px-6 py-4 text-sm text-gray-500">"Nexus Gaming API"</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">API Base URL</td>
                            <td class="px-6 py-4 text-sm text-gray-500">URL dasar untuk semua endpoint API</td>
                            <td class="px-6 py-4 text-sm text-gray-500">"https://api.nexusggr.com/"</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Agent Code</td>
                            <td class="px-6 py-4 text-sm text-gray-500">Kode agen atau username untuk autentikasi</td>
                            <td class="px-6 py-4 text-sm text-gray-500">"agentxyz"</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Agent Token</td>
                            <td class="px-6 py-4 text-sm text-gray-500">Token atau password untuk autentikasi</td>
                            <td class="px-6 py-4 text-sm text-gray-500">"82a084e79888b482034f2d4599e0251e"</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">API Version</td>
                            <td class="px-6 py-4 text-sm text-gray-500">Versi API yang digunakan (opsional)</td>
                            <td class="px-6 py-4 text-sm text-gray-500">"v1" atau "2.0"</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Status</td>
                            <td class="px-6 py-4 text-sm text-gray-500">Status aktif/nonaktif aggregator</td>
                            <td class="px-6 py-4 text-sm text-gray-500">Aktif/Nonaktif</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
        
        <section class="mb-8">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Langkah-langkah Mengelola API Aggregators</h2>
            
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">Menambahkan API Aggregator Baru</h3>
                <ol class="list-decimal list-inside space-y-2">
                    <li class="text-gray-700">
                        Navigasi ke halaman API Aggregators melalui sidebar
                    </li>
                    <li class="text-gray-700">
                        Klik tombol "Add New" di bagian atas halaman
                    </li>
                    <li class="text-gray-700">
                        Isi form dengan informasi yang diperlukan:
                        <ul class="list-disc list-inside ml-6 mt-1">
                            <li>Name: Nama deskriptif untuk aggregator</li>
                            <li>API Base URL: URL dasar API (termasuk trailing slash "/")</li>
                            <li>Agent Code: Kode agen yang diberikan oleh penyedia API</li>
                            <li>Agent Token: Token autentikasi yang diberikan oleh penyedia API</li>
                            <li>API Version: (Opsional) Versi API yang digunakan</li>
                        </ul>
                    </li>
                    <li class="text-gray-700">
                        Pilih status (Active/Inactive)
                    </li>
                    <li class="text-gray-700">
                        Klik tombol "Save" untuk menyimpan aggregator baru
                    </li>
                </ol>
                
                <div class="mt-4 mb-6">
                    <img src="/docs/images/aggregator-form.jpg" alt="Aggregator Form Example" class="rounded-lg shadow-md border border-gray-200 max-w-full">
                </div>
            </div>
            
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">Mengedit API Aggregator</h3>
                <ol class="list-decimal list-inside space-y-2">
                    <li class="text-gray-700">
                        Pada halaman API Aggregators, temukan aggregator yang ingin diedit
                    </li>
                    <li class="text-gray-700">
                        Klik ikon edit (pensil) pada kolom Actions
                    </li>
                    <li class="text-gray-700">
                        Ubah informasi yang diperlukan
                    </li>
                    <li class="text-gray-700">
                        Klik "Save" untuk menyimpan perubahan
                    </li>
                </ol>
            </div>
            
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">Mengaktifkan/Menonaktifkan API Aggregator</h3>
                <p class="text-gray-700 mb-3">
                    Untuk mengubah status aggregator dengan cepat:
                </p>
                <ol class="list-decimal list-inside space-y-2">
                    <li class="text-gray-700">
                        Pada halaman API Aggregators, temukan aggregator yang ingin diubah statusnya
                    </li>
                    <li class="text-gray-700">
                        Klik ikon toggle pada kolom Actions
                    </li>
                    <li class="text-gray-700">
                        Status akan berubah secara instan (Active ↔ Inactive)
                    </li>
                </ol>
            </div>
            
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-3">Menghapus API Aggregator</h3>
                <p class="text-gray-700 mb-3">
                    Untuk menghapus aggregator yang tidak lagi dibutuhkan:
                </p>
                <ol class="list-decimal list-inside space-y-2">
                    <li class="text-gray-700">
                        Pada halaman API Aggregators, temukan aggregator yang ingin dihapus
                    </li>
                    <li class="text-gray-700">
                        Klik ikon delete (tempat sampah) pada kolom Actions
                    </li>
                    <li class="text-gray-700">
                        Konfirmasi penghapusan pada dialog yang muncul
                    </li>
                </ol>
                
                <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mt-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-yellow-500"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                <strong>Penting:</strong> Aggregator tidak dapat dihapus jika memiliki endpoint API terkait.
                                Hapus terlebih dahulu semua endpoint terkait sebelum menghapus aggregator.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
        <section>
            <h2 class="text-xl font-bold text-gray-800 mb-4">Tips dan Praktik Terbaik</h2>
            
            <ul class="list-disc list-inside space-y-3">
                <li class="text-gray-700">
                    <span class="font-medium">Penamaan yang konsisten:</span> 
                    Gunakan konvensi penamaan yang konsisten untuk memudahkan pencarian dan pengelolaan
                </li>
                <li class="text-gray-700">
                    <span class="font-medium">Keamanan kredensial:</span> 
                    Pastikan token dan kredensial API disimpan dengan aman dan tidak dibagikan
                </li>
                <li class="text-gray-700">
                    <span class="font-medium">Organisasi yang baik:</span> 
                    Kelompokkan endpoint API yang terkait dengan aggregator yang sama untuk manajemen yang lebih mudah
                </li>
                <li class="text-gray-700">
                    <span class="font-medium">Pemantauan status:</span> 
                    Nonaktifkan aggregator yang sedang dalam pemeliharaan atau tidak digunakan daripada menghapusnya
                </li>
                <li class="text-gray-700">
                    <span class="font-medium">Dokumentasi:</span> 
                    Tambahkan informasi versi API dan dokumentasi lain yang relevan untuk referensi di masa mendatang
                </li>
            </ul>
        </section>
    </div>
</div>

<div class="flex justify-between mt-6">
    <a href="/docs/index.php" class="text-indigo-600 hover:text-indigo-900">
        <i class="fas fa-arrow-left mr-2"></i> Back to Documentation Index
    </a>
    <a href="/docs/endpoints.php" class="text-indigo-600 hover:text-indigo-900">
        Next: API Endpoints Documentation <i class="fas fa-arrow-right ml-2"></i>
    </a>
</div>

<?php
// Include footer
require_once __DIR__ . '/../includes/footer.php';
?>