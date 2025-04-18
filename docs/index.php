<?php
/**
 * API Manager - Documentation Index
 */

// Start session
session_start();

// Include required files
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';

// Set current page for sidebar highlight
$currentPage = 'docs';
$pageTitle = 'Documentation';

// Include header
require_once __DIR__ . '/../includes/head.php';
require_once __DIR__ . '/../includes/sidebar.php';
?>

<!-- Page header -->
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-2">API Manager Documentation</h1>
    <p class="text-gray-600">Comprehensive guide for using the API Manager system</p>
</div>

<?php displayMessage(); ?>

<!-- Documentation Content -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="border-b border-gray-200">
        <nav class="flex overflow-x-auto p-4">
            <a href="#overview" class="px-4 py-2 bg-indigo-50 text-indigo-700 rounded-md mr-2 whitespace-nowrap">
                Overview
            </a>
            <a href="#aggregators" class="px-4 py-2 hover:bg-gray-100 rounded-md mr-2 whitespace-nowrap">
                API Aggregators
            </a>
            <a href="#endpoints" class="px-4 py-2 hover:bg-gray-100 rounded-md mr-2 whitespace-nowrap">
                API Endpoints
            </a>
            <a href="#templates" class="px-4 py-2 hover:bg-gray-100 rounded-md mr-2 whitespace-nowrap">
                Request Templates
            </a>
            <a href="#logs" class="px-4 py-2 hover:bg-gray-100 rounded-md mr-2 whitespace-nowrap">
                API Logs
            </a>
            <a href="/docs/db_requests.php" class="px-4 py-2 hover:bg-gray-100 rounded-md mr-2 whitespace-nowrap flex items-center">
                <i class="fas fa-database mr-1 text-indigo-500"></i> DB Examples
            </a>
        </nav>
    </div>
    
    <div class="p-6">
        <!-- Overview Section -->
        <section id="overview" class="mb-10">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Overview</h2>
            <p class="text-gray-700 mb-4">
                API Manager adalah sistem manajemen API yang memungkinkan Anda untuk mengelola berbagai API dari provider yang berbeda dari satu dashboard terpusat. 
                Sistem ini dirancang untuk mempermudah integrasi, dokumentasi, dan monitoring API.
            </p>
            
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-500"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            API Manager mendukung database PostgreSQL dan MySQL, yang dapat dikonfigurasi melalui file <code>.env</code>.
                        </p>
                    </div>
                </div>
            </div>
            
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Fitur Utama:</h3>
            <ul class="list-disc list-inside space-y-2 mb-4">
                <li class="text-gray-700">
                    <span class="font-medium">Manajemen API Aggregator:</span> 
                    Kelola berbagai penyedia API (aggregator) dalam satu sistem
                </li>
                <li class="text-gray-700">
                    <span class="font-medium">Dokumentasi Endpoint:</span> 
                    Simpan dan dokumentasikan semua endpoint API
                </li>
                <li class="text-gray-700">
                    <span class="font-medium">Request Templates:</span> 
                    Buat template untuk request API yang sering digunakan
                </li>
                <li class="text-gray-700">
                    <span class="font-medium">Logging dan Monitoring:</span> 
                    Pantau semua request API dan responsenya
                </li>
            </ul>
        </section>
        
        <!-- API Aggregators Section -->
        <section id="aggregators" class="mb-10">
            <h2 class="text-xl font-bold text-gray-800 mb-4">API Aggregators</h2>
            <p class="text-gray-700 mb-4">
                API Aggregator adalah penyedia layanan API yang mengumpulkan berbagai layanan API di bawah satu platform.
                Sistem ini memungkinkan Anda mengelola kredensial dan konfigurasi untuk setiap aggregator.
            </p>
            
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Mengelola API Aggregators:</h3>
            <ol class="list-decimal list-inside space-y-2 mb-4">
                <li class="text-gray-700">
                    <span class="font-medium">Menambahkan Aggregator Baru:</span>
                    <ul class="list-disc list-inside ml-6 mt-1">
                        <li>Klik tombol "Add New" di halaman API Aggregators</li>
                        <li>Isi semua field yang diperlukan (nama, URL base, kode agen, token)</li>
                        <li>Klik "Save" untuk menyimpan perubahan</li>
                    </ul>
                </li>
                <li class="text-gray-700">
                    <span class="font-medium">Mengedit Aggregator:</span>
                    <ul class="list-disc list-inside ml-6 mt-1">
                        <li>Klik ikon edit pada baris aggregator yang ingin diubah</li>
                        <li>Ubah informasi yang diinginkan</li>
                        <li>Klik "Save" untuk menyimpan perubahan</li>
                    </ul>
                </li>
                <li class="text-gray-700">
                    <span class="font-medium">Mengaktifkan/Menonaktifkan Aggregator:</span>
                    <ul class="list-disc list-inside ml-6 mt-1">
                        <li>Klik ikon toggle pada baris aggregator</li>
                        <li>Status akan berubah antara aktif dan nonaktif</li>
                    </ul>
                </li>
                <li class="text-gray-700">
                    <span class="font-medium">Menghapus Aggregator:</span>
                    <ul class="list-disc list-inside ml-6 mt-1">
                        <li>Klik ikon hapus pada baris aggregator</li>
                        <li>Konfirmasi penghapusan</li>
                        <li>Catatan: Aggregator tidak dapat dihapus jika memiliki endpoint terkait</li>
                    </ul>
                </li>
            </ol>
            
            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-yellow-500"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            Jaga keamanan token API. Pastikan Anda menyimpan kredensial dengan aman dan tidak membagikannya.
                        </p>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- API Endpoints Section -->
        <section id="endpoints" class="mb-10">
            <h2 class="text-xl font-bold text-gray-800 mb-4">API Endpoints</h2>
            <p class="text-gray-700 mb-4">
                API Endpoints adalah titik akses spesifik dari sebuah API yang dapat Anda gunakan untuk berinteraksi dengan layanan penyedia.
                Setiap endpoint terhubung dengan aggregator tertentu dan memiliki konfigurasi spesifik.
            </p>
            
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Mengelola API Endpoints:</h3>
            <ol class="list-decimal list-inside space-y-2 mb-4">
                <li class="text-gray-700">
                    <span class="font-medium">Menambahkan Endpoint Baru:</span>
                    <ul class="list-disc list-inside ml-6 mt-1">
                        <li>Klik tombol "Add New" di halaman API Endpoints</li>
                        <li>Pilih Aggregator yang terkait</li>
                        <li>Isi nama, URL, dan metode request (GET, POST, dll)</li>
                        <li>Tentukan body request, header, dan parameter (jika ada)</li>
                        <li>Klik "Save" untuk menyimpan</li>
                    </ul>
                </li>
                <li class="text-gray-700">
                    <span class="font-medium">Menggunakan JSON Editor:</span>
                    <ul class="list-disc list-inside ml-6 mt-1">
                        <li>Editor JSON tersedia untuk field request body, headers, dan query parameters</li>
                        <li>Anda dapat beralih antara mode 'tree', 'code', dan 'text'</li>
                        <li>Gunakan validasi otomatis untuk memastikan JSON valid</li>
                    </ul>
                </li>
                <li class="text-gray-700">
                    <span class="font-medium">Testing Endpoint:</span>
                    <ul class="list-disc list-inside ml-6 mt-1">
                        <li>Setelah menyimpan endpoint, Anda dapat mengujinya</li>
                        <li>Gunakan form test untuk mengirim request dan melihat response</li>
                        <li>Semua aktivitas testing akan tercatat di log</li>
                    </ul>
                </li>
            </ol>
            
            <div class="bg-indigo-50 border-l-4 border-indigo-500 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-lightbulb text-indigo-500"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-indigo-700">
                            Gunakan variabel placeholder seperti <code>{{variable_name}}</code> di URL dan body untuk membuat endpoint yang lebih dinamis.
                            Variabel ini akan diganti dengan nilai aktual saat request dilakukan.
                        </p>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- Request Templates Section -->
        <section id="templates" class="mb-10">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Request Templates</h2>
            <p class="text-gray-700 mb-4">
                Request Templates memungkinkan Anda menyimpan pola request yang sering digunakan. Template ini dapat digunakan untuk mempercepat pembuatan endpoint baru atau testing.
            </p>
            
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Menggunakan Request Templates:</h3>
            <ol class="list-decimal list-inside space-y-2 mb-4">
                <li class="text-gray-700">
                    <span class="font-medium">Membuat Template Baru:</span>
                    <ul class="list-disc list-inside ml-6 mt-1">
                        <li>Klik tombol "Add New" di halaman Request Templates</li>
                        <li>Tentukan nama template yang deskriptif</li>
                        <li>Pilih metode HTTP (GET, POST, PUT, DELETE)</li>
                        <li>Isi pola URL dengan variabel placeholder jika perlu</li>
                        <li>Tentukan header, query parameter, dan body default</li>
                        <li>Klik "Save" untuk menyimpan template</li>
                    </ul>
                </li>
                <li class="text-gray-700">
                    <span class="font-medium">Menggunakan Template:</span>
                    <ul class="list-disc list-inside ml-6 mt-1">
                        <li>Saat membuat endpoint baru, klik "Load from Template"</li>
                        <li>Pilih template yang ingin digunakan</li>
                        <li>Field akan otomatis terisi dengan nilai dari template</li>
                        <li>Modifikasi sesuai kebutuhan</li>
                    </ul>
                </li>
            </ol>
        </section>
        
        <!-- API Logs Section -->
        <section id="logs">
            <h2 class="text-xl font-bold text-gray-800 mb-4">API Logs</h2>
            <p class="text-gray-700 mb-4">
                API Logs mencatat semua aktivitas request API yang dilakukan melalui sistem. Fitur ini memungkinkan Anda memantau, melacak, dan mengatasi masalah.
            </p>
            
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Menggunakan API Logs:</h3>
            <ul class="list-disc list-inside space-y-2 mb-4">
                <li class="text-gray-700">
                    <span class="font-medium">Melihat Log:</span> Semua request API tercatat dengan informasi detail seperti URL, metode, body, response code, dan waktu
                </li>
                <li class="text-gray-700">
                    <span class="font-medium">Filtering:</span> Gunakan filter untuk mencari log berdasarkan aggregator, endpoint, atau status response
                </li>
                <li class="text-gray-700">
                    <span class="font-medium">Melihat Detail:</span> Klik pada log untuk melihat informasi lengkap termasuk request body dan response
                </li>
                <li class="text-gray-700">
                    <span class="font-medium">Debugging:</span> Gunakan log untuk mengidentifikasi dan mengatasi masalah dengan API
                </li>
            </ul>
            
            <div class="bg-red-50 border-l-4 border-red-500 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-500"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700">
                            Pastikan untuk secara berkala membersihkan log lama untuk menjaga performa database. 
                            Log yang terlalu banyak dapat memperlambat sistem.
                        </p>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<?php
// Include footer
require_once __DIR__ . '/../includes/footer.php';
?>