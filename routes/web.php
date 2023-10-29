<?php

use App\Http\Controllers\AppSettingController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Route::controller(AuthenticationController::class)->group(function () {
    Route::get('/login', 'index')->name('login')->middleware('guest');
    Route::post('/login', 'authenticate')->name('login.action')->middleware('guest');
    Route::post('/logout', 'logout')->name('logout')->middleware('auth');
});

Route::middleware('auth')->group(function () {

    Route::get('/', function () {
        return view('home');
    })->name('home');

    Route::get('/dashboard', function () {
        return view('dashboard-menu');
    })->name('dashboard')->middleware('permissions:dashboard');

    Route::post('app-setting/selalu-cetak', [AppSettingController::class, 'selalucetak']);

    Route::prefix('barang')->group(function () {
        Route::controller(KategoriController::class)->group(function () {
            Route::prefix('kategori')->group(function () {
                Route::get('cari', 'search');
                Route::get('/', 'index')->name('kategori.index')->middleware('permissions:lihat_kategori');
                Route::post('/', 'store')->middleware('permissions:tambah_kategori');
                Route::get('/{kategori}', 'show')->middleware('permissions:lihat_kategori');
                Route::put('/{kategori}', 'update')->middleware('permissions:edit_kategori');
                Route::delete('/{kategori}', 'destroy')->middleware('permissions:hapus_kategori');
            });
        });

        Route::controller(SupplierController::class)->group(function () {
            Route::prefix('supplier')->group(function () {
                Route::get('cari', 'search');
                Route::get('/', 'index')->name('supplier.index')->middleware('permissions:lihat_supplier');
                Route::post('/', 'store')->middleware('permissions:tambah_supplier');
                Route::get('/{supplier}', 'show')->middleware('permissions:lihat_supplier');
                Route::put('/{supplier}', 'update')->middleware('permissions:edit_supplier');
                Route::delete('/{supplier}', 'destroy')->middleware('permissions:hapus_supplier');
            });
        });

        Route::controller(BarangController::class)->group(function () {
            Route::get('/', 'index')->middleware('permissions:lihat_barang');
            Route::post('/', 'store')->middleware('permissions:tambah_barang');
            Route::get('/{barang}', 'show')->middleware('permissions:lihat_barang');
            Route::put('/{barang}', 'update')->middleware('permissions:edit_barang');
            Route::delete('/{barang}', 'destroy')->middleware('permissions:hapus_barang');
        });
    });

    Route::controller(PenjualanController::class)->group(function () {
        Route::get('/penjualan', 'index')->name('penjualan')->middleware('permissions:lihat_penjualan');
        Route::get('/penjualan/tambah/searchbarang', 'searchBarang');
        Route::get('/penjualan/tambah/getdatabarang', 'getDatabarang');
        Route::get('/penjualan/tambah', 'create')->middleware('permissions:tambah_penjualan');
        Route::post('/penjualan/tambah', 'store')->middleware('permissions:tambah_penjualan');
    });

    Route::prefix('kasir')->group(function () {
        Route::get('/', [KasirController::class, 'index']);
        // Route::post('/', [KasirController::class, 'store']);
        Route::get('/getnotrans', [KasirController::class, 'getNoTrans'])->withoutMiddleware('permission:kasir');
        Route::get('/getdatabarang', [KasirController::class, 'getDataBarang'])->name('kasir.getDataBarang');
        Route::get('/histori', [KasirController::class, 'histori']);
        Route::get('/gethistori', [KasirController::class, 'getHistori'])->name('penjualan.getDataHistori');
        Route::get('/gethistori/{notrans}', [KasirController::class, 'getDetailHistori'])->name('penjualan.getDetailDataHistori');
        Route::get('/{id}', [KasirController::class, 'struk']);
    })->middleware('permissions:kasir');

    Route::prefix('pengguna')->group(function () {
        Route::prefix('role')->group(function () {
            Route::controller(RoleController::class)->group(function () {
                Route::get('/', 'index')->name('role');
                Route::get('/{role}', 'show');
                Route::post('/', 'store');
                Route::put('/{role}', 'update');
                Route::delete('/{role}', 'destroy');
            });
        });

        Route::controller(PenggunaController::class)->group(function () {
            Route::get('/', 'index')->name('pengguna');
            Route::get('/detail/{pengguna}', 'detail');
            Route::get('/{pengguna}', 'show');
            Route::post('/', 'store');
            Route::put('/{pengguna}', 'update');
            Route::patch('/{pengguna}', 'resetPassword');
            Route::delete('/{pengguna}', 'destroy');
        });
    })->middleware('onlySuperAdmin');
});
