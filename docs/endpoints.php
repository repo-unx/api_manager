<?php
/**
 * API Manager - API Endpoints Documentation
 */

// Start session
session_start();

// Include required files
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';

// Set current page for sidebar highlight
$currentPage = 'docs';
$pageTitle = 'API Endpoints Documentation';

// Include header
require_once __DIR__ . '/../includes/head.php';
require_once __DIR__ . '/../includes/sidebar.php';
?>

<!-- Page header -->
<div class="mb-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 mb-2">API Endpoints Documentation</h1>
            <p class="text-gray-600">Comprehensive guide for managing API Endpoints</p>
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
            <h2 class="text-xl font-bold text-gray-800 mb-4">Pengenalan API Endpoints</h2>
            <p class="text-gray-700 mb-4">
                API Endpoint adalah titik akses atau URL spesifik yang bisa diakses untuk melakukan interaksi dengan
                API. Setiap endpoint biasanya berhubungan dengan satu fungsi atau operasi tertentu pada API,
                seperti mendapatkan data, membuat data baru, memperbarui data, atau menghapus data.
            </p>
            
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-lightbulb text-yellow-500 text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">Contoh Endpoint</h3>
                        <p class="text-sm text-yellow-700 mt-1">
                            Pada API game, contoh endpoint bisa berupa: <code>/user_create</code> untuk membuat user baru,
                            <code>/user_deposit</code> untuk deposit saldo, atau <code>/game_launch</code> untuk meluncurkan game.
                        </p>
                    </div>
                </div>
            </div>
        </section>
        
        <section class="mb-8">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Komponen API Endpoint</h2>
            
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
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Aggregator</td>
                            <td class="px-6 py-4 text-sm text-gray-500">Penyedia API yang menjadi parent dari endpoint</td>
                            <td class="px-6 py-4 text-sm text-gray-500">"Nexus"</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Name</td>
                            <td class="px-6 py-4 text-sm text-gray-500">Nama deskriptif untuk endpoint</td>
                            <td class="px-6 py-4 text-sm text-gray-500">"Create New User"</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Endpoint URL</td>
                            <td class="px-6 py-4 text-sm text-gray-500">Path ke endpoint API, relatif terhadap Base URL</td>
                            <td class="px-6 py-4 text-sm text-gray-500">"/v1/users"</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Method</td>
                            <td class="px-6 py-4 text-sm text-gray-500">Metode HTTP untuk request (GET, POST, etc)</td>
                            <td class="px-6 py-4 text-sm text-gray-500">"POST"</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Request Body</td>
                            <td class="px-6 py-4 text-sm text-gray-500">Payload JSON yang dikirim saat request</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{"method": "user_create", "user_code": "{{user_code}}"}</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Headers</td>
                            <td class="px-6 py-4 text-sm text-gray-500">HTTP headers yang diperlukan untuk request</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{"Content-Type": "application/json"}</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Query Parameters</td>
                            <td class="px-6 py-4 text-sm text-gray-500">Parameter yang ditambahkan ke URL</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{"version": "v1", "format": "json"}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
        
        <section class="mb-8">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Mengelola API Endpoints</h2>
            
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">Menambahkan Endpoint Baru</h3>
                <ol class="list-decimal list-inside space-y-2">
                    <li class="text-gray-700">
                        Navigasi ke halaman API Endpoints melalui sidebar
                    </li>
                    <li class="text-gray-700">
                        Klik tombol "Add New" di bagian atas halaman
                    </li>
                    <li class="text-gray-700">
                        Pilih Aggregator dari dropdown list
                    </li>
                    <li class="text-gray-700">
                        Isi form dengan informasi yang diperlukan:
                        <ul class="list-disc list-inside ml-6 mt-1">
                            <li>Name: Nama deskriptif untuk endpoint</li>
                            <li>Endpoint URL: Path atau URL lengkap ke endpoint</li>
                            <li>Method: Metode HTTP (GET, POST, PUT, DELETE, etc)</li>
                        </ul>
                    </li>
                    <li class="text-gray-700">
                        Gunakan editor JSON untuk menambahkan:
                        <ul class="list-disc list-inside ml-6 mt-1">
                            <li>Request Body: Payload JSON untuk request</li>
                            <li>Headers: HTTP headers yang diperlukan</li>
                            <li>Query Parameters: Parameter URL query string</li>
                        </ul>
                    </li>
                    <li class="text-gray-700">
                        Pilih status (Active/Inactive)
                    </li>
                    <li class="text-gray-700">
                        Klik tombol "Save" untuk menyimpan endpoint baru
                    </li>
                </ol>
            </div>
            
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">Menggunakan Placeholders</h3>
                <p class="text-gray-700 mb-3">
                    Anda dapat menggunakan placeholder dalam format <code>{{placeholder_name}}</code> di URL, request body, headers, dan query parameters.
                    Placeholders ini akan digantikan dengan nilai sebenarnya saat melakukan request.
                </p>
                
                <div class="bg-gray-100 p-4 rounded-lg mb-3">
                    <h4 class="text-md font-medium text-gray-800 mb-2">Contoh Request Body dengan Placeholders:</h4>
                    <pre class="bg-gray-800 text-white p-3 rounded-md overflow-x-auto"><code>{
  "method": "user_create",
  "agent_code": "{{agent_code}}",
  "agent_token": "{{agent_token}}",
  "user_code": "{{user_code}}"
}</code></pre>
                </div>
                
                <p class="text-gray-700">
                    Saat melakukan request, placeholder seperti <code>{{agent_code}}</code> dan <code>{{agent_token}}</code> 
                    akan secara otomatis diisi dengan nilai dari Aggregator terkait, sementara placeholder lain seperti <code>{{user_code}}</code> 
                    perlu diisi secara manual.
                </p>
            </div>
            
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-3">Testing Endpoint</h3>
                <ol class="list-decimal list-inside space-y-2">
                    <li class="text-gray-700">
                        Buka halaman detail endpoint dengan mengklik ikon edit
                    </li>
                    <li class="text-gray-700">
                        Scroll ke bagian "Test Endpoint" di bagian bawah halaman
                    </li>
                    <li class="text-gray-700">
                        Isi nilai untuk semua placeholder yang dibutuhkan
                    </li>
                    <li class="text-gray-700">
                        Klik tombol "Send Request" untuk memulai test
                    </li>
                    <li class="text-gray-700">
                        Hasil request akan ditampilkan di bawah form, termasuk:
                        <ul class="list-disc list-inside ml-6 mt-1">
                            <li>Status code response</li>
                            <li>Response headers</li>
                            <li>Response body (diformat sebagai JSON jika memungkinkan)</li>
                            <li>Waktu eksekusi</li>
                        </ul>
                    </li>
                </ol>
                
                <div class="bg-green-50 border-l-4 border-green-500 p-4 mt-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-green-500"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700">
                                <strong>Tip:</strong> Semua test API akan otomatis dicatat di halaman API Request Logs
                                sehingga Anda dapat melihat riwayat test yang telah dilakukan.
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
                    <span class="font-medium">Pengelompokkan endpoint:</span> 
                    Organisasikan endpoint berdasarkan fungsi atau microservice untuk memudahkan manajemen
                </li>
                <li class="text-gray-700">
                    <span class="font-medium">Dokumentasi yang baik:</span> 
                    Berikan nama yang deskriptif dan jelas untuk setiap endpoint
                </li>
                <li class="text-gray-700">
                    <span class="font-medium">Placeholder:</span> 
                    Gunakan placeholder untuk nilai-nilai dinamis yang akan berubah di setiap request
                </li>
                <li class="text-gray-700">
                    <span class="font-medium">Pengujian:</span> 
                    Selalu lakukan test pada endpoint untuk memastikan berfungsi dengan benar
                </li>
                <li class="text-gray-700">
                    <span class="font-medium">Headers standar:</span> 
                    Gunakan header standar seperti Content-Type dan Accept untuk memastikan kompatibilitas
                </li>
                <li class="text-gray-700">
                    <span class="font-medium">Monitoring:</span> 
                    Periksa logs secara berkala untuk memantau performa dan mendeteksi error
                </li>
            </ul>
        </section>
    </div>
</div>

<div class="flex justify-between mt-6">
    <a href="/docs/aggregators.php" class="text-indigo-600 hover:text-indigo-900">
        <i class="fas fa-arrow-left mr-2"></i> Previous: API Aggregators
    </a>
    <a href="/docs/templates.php" class="text-indigo-600 hover:text-indigo-900">
        Next: Request Templates <i class="fas fa-arrow-right ml-2"></i>
    </a>
</div>

<?php
// Include footer
require_once __DIR__ . '/../includes/footer.php';
?>