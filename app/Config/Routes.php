<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Auth Routes
$routes->get('auth', 'Auth::index');
$routes->post('auth/login', 'Auth::login');
$routes->get('auth/logout', 'Auth::logout');

// Register Routes
$routes->get('auth/register', 'Auth::register');
$routes->post('auth/register', 'Auth::registerProcess');
$routes->get('auth/verify', 'Auth::verify');
$routes->post('auth/verify', 'Auth::verifyOTP');
$routes->post('auth/resend-otp', 'Auth::resendOTP');

// Admin Routes
$routes->group('admin', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Admin::index', ['filter' => 'role:admin,pimpinan']);

    // User Management (hanya admin)
    $routes->group('', ['filter' => 'role:admin'], function ($routes) {
        $routes->get('users', 'Admin::users');
        $routes->get('getUsers', 'Admin::getUsers');
        $routes->get('getUser/(:num)', 'Admin::getUser/$1');
        $routes->post('createUser', 'Admin::createUser');
        $routes->post('addUser', 'Admin::addUser');
        $routes->post('updateUser/(:num)', 'Admin::updateUser/$1');
        $routes->post('deleteUser/(:num)', 'Admin::deleteUser/$1');
        $routes->get('getRoles', 'Admin::getRoles');
    });

    // Karyawan Management (hanya admin)
    $routes->group('karyawan', ['filter' => 'role:admin'], function ($routes) {
        $routes->get('/', 'Karyawan::index');
        $routes->get('create', 'Karyawan::create');
        $routes->post('store', 'Karyawan::store');
        $routes->get('edit/(:segment)', 'Karyawan::edit/$1');
        $routes->post('update/(:segment)', 'Karyawan::update/$1');
        $routes->get('delete/(:segment)', 'Karyawan::delete/$1');
        $routes->get('getKaryawan', 'Karyawan::getKaryawan');
        $routes->get('getNewId', 'Karyawan::getNewId');
        $routes->get('getById/(:segment)', 'Karyawan::getKaryawanById/$1');
    });

    // Pelanggan Management (admin & pimpinan)
    $routes->group('pelanggan', ['filter' => 'role:admin,pimpinan'], function ($routes) {
        $routes->get('/', 'Pelanggan::index');
        $routes->get('create', 'Pelanggan::create');
        $routes->get('edit/(:segment)', 'Pelanggan::edit/$1');
        $routes->get('getPelanggan', 'Pelanggan::getPelanggan');
        $routes->get('getNewKode', 'Pelanggan::getNewKode');
        $routes->get('getUsers', 'Pelanggan::getUsers');
        $routes->get('getByKode/(:segment)', 'Pelanggan::getByKode/$1');
        $routes->post('save', 'Pelanggan::save');
        $routes->post('update/(:segment)', 'Pelanggan::update/$1');
        $routes->delete('delete/(:segment)', 'Pelanggan::delete/$1');
    });

    // Perlengkapan Management (hanya admin)
    $routes->group('perlengkapan', ['filter' => 'role:admin'], function ($routes) {
        $routes->get('/', 'Perlengkapan::index');
        $routes->get('create', 'Perlengkapan::create');
        $routes->get('edit/(:num)', 'Perlengkapan::edit/$1');
        $routes->get('getPerlengkapan', 'Perlengkapan::getPerlengkapan');
        $routes->get('getById/(:num)', 'Perlengkapan::getById/$1');
        $routes->get('getStokMenipis', 'Perlengkapan::getStokMenipis');
        $routes->post('save', 'Perlengkapan::save');
        $routes->post('update', 'Perlengkapan::update');
        $routes->post('updateStok', 'Perlengkapan::updateStok');
        $routes->delete('delete/(:num)', 'Perlengkapan::delete/$1');
    });

    // Pembelian routes (hanya admin)
    $routes->group('pembelian', ['filter' => 'role:admin'], function ($routes) {
        $routes->get('/', 'Pembelian::index');
        $routes->get('create', 'Pembelian::create');
        $routes->post('save', 'Pembelian::save');
        $routes->get('edit/(:segment)', 'Pembelian::edit/$1');
        $routes->post('update', 'Pembelian::update');
        $routes->get('detail/(:segment)', 'Pembelian::detail/$1');
        $routes->delete('delete/(:segment)', 'Pembelian::delete/$1');

        // API routes
        $routes->get('getPembelian', 'Pembelian::getPembelian');
        $routes->get('getPerlengkapan', 'Pembelian::getPerlengkapan');
        $routes->get('getDetailPembelian/(:segment)', 'Pembelian::getDetailPembelian/$1');
        $routes->post('saveDetail', 'Pembelian::saveDetail');
        $routes->delete('deleteDetail/(:num)', 'Pembelian::deleteDetail/$1'); // This still uses ID

        // Laporan
        $routes->get('laporan', 'Pembelian::laporan');
        $routes->get('getLaporanData', 'Pembelian::getLaporanData');
        $routes->get('exportExcel', 'Pembelian::exportExcel');
    });

    // Layanan Management (hanya admin)
    $routes->group('layanan', ['filter' => 'role:admin'], function ($routes) {
        $routes->get('/', 'Layanan::index');
        $routes->get('create', 'Layanan::create');
        $routes->post('store', 'Layanan::store');
        $routes->get('edit/(:num)', 'Layanan::edit/$1');
        $routes->post('update/(:num)', 'Layanan::update/$1');
        $routes->get('delete/(:num)', 'Layanan::delete/$1');
        $routes->post('getLayananByJenis', 'Layanan::getLayananByJenis');
    });

    // Booking Management (admin & pimpinan)
    $routes->group('booking', ['filter' => 'role:admin,pimpinan'], function ($routes) {
        $routes->get('/', 'Booking::index');
        $routes->get('create', 'Booking::create');
        $routes->post('store', 'Booking::store');
        $routes->get('show/(:num)', 'Booking::show/$1');
        $routes->get('edit/(:num)', 'Booking::edit/$1');
        $routes->post('update/(:num)', 'Booking::update/$1');
        $routes->get('cancel/(:num)', 'Booking::cancel/$1');
    });

    // Antrian Management (admin & pimpinan)
    $routes->group('antrian', ['filter' => 'role:admin,pimpinan'], function ($routes) {
        $routes->get('/', 'Antrian::index');
        $routes->get('create', 'Antrian::create');
        $routes->post('store', 'Antrian::store');
        $routes->get('show/(:num)', 'Antrian::show/$1');
        $routes->post('updateStatus/(:num)', 'Antrian::updateStatus/$1');
        $routes->post('assignKaryawan/(:num)', 'Antrian::assignKaryawan/$1');
    });

    // Transaksi Management (admin & pimpinan)
    $routes->group('transaksi', ['filter' => 'role:admin,pimpinan'], function ($routes) {
        $routes->get('/', 'Transaksi::index');
        $routes->get('create', 'Transaksi::create');
        $routes->post('store', 'Transaksi::store');
        $routes->get('show/(:num)', 'Transaksi::show/$1');
        $routes->get('edit/(:num)', 'Transaksi::edit/$1');
        $routes->post('update/(:num)', 'Transaksi::update/$1');
        $routes->post('updatePaymentStatus/(:num)', 'Transaksi::updatePaymentStatus/$1');
        $routes->get('printInvoice/(:num)', 'Transaksi::printInvoice/$1');
        $routes->get('report', 'Transaksi::report');
    });

    // Kendaraan Management (admin & pimpinan)
    $routes->group('kendaraan', ['filter' => 'role:admin,pimpinan'], function ($routes) {
        $routes->get('/', 'Kendaraan::index');
        $routes->get('create', 'Kendaraan::create');
        $routes->post('store', 'Kendaraan::store');
        $routes->get('edit/(:num)', 'Kendaraan::edit/$1');
        $routes->post('update/(:num)', 'Kendaraan::update/$1');
        $routes->get('delete/(:num)', 'Kendaraan::delete/$1');
    });
});

// Pelanggan Routes (untuk akses pelanggan)
$routes->group('pelanggan', ['filter' => 'auth'], function ($routes) {
    $routes->get('profile', 'Pelanggan::profile', ['filter' => 'role:pelanggan']);
    $routes->post('updateProfile', 'Pelanggan::updateProfile', ['filter' => 'role:pelanggan']);

    // Kendaraan Routes untuk Pelanggan
    $routes->group('kendaraan', ['filter' => 'role:pelanggan'], function ($routes) {
        $routes->get('/', 'Kendaraan::myVehicles');
        $routes->get('add', 'Kendaraan::addVehicle');
        $routes->post('save', 'Kendaraan::saveVehicle');
        $routes->get('edit/(:num)', 'Kendaraan::editVehicle/$1');
        $routes->post('update/(:num)', 'Kendaraan::updateVehicle/$1');
        $routes->get('delete/(:num)', 'Kendaraan::deleteVehicle/$1');
    });

    // Booking Routes untuk Pelanggan
    $routes->group('booking', ['filter' => 'role:pelanggan'], function ($routes) {
        $routes->get('create', 'Booking::createBooking');
        $routes->post('store', 'Booking::storeBooking');
        $routes->get('history', 'Booking::history');
        $routes->get('detail/(:num)', 'Booking::detail/$1');
    });

    // Transaksi Routes untuk Pelanggan
    $routes->group('transaksi', ['filter' => 'role:pelanggan'], function ($routes) {
        $routes->get('history', 'Transaksi::history');
        $routes->get('detail/(:num)', 'Transaksi::detail/$1');
    });
});
