<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// Public routes
$routes->get('/', 'Home::index');
$routes->get('booking', 'Home::booking'); // Public booking form
$routes->post('booking/store', 'Booking::storePublic'); // Public booking submission
$routes->post('booking/get-available-slots', 'Booking::getAvailableSlots'); // Get available time slots

// Payment routes
$routes->get('payment/(:segment)', 'Payment::index/$1'); // Payment page by booking code
$routes->post('payment/process', 'Payment::process'); // Process payment
$routes->get('payment/success/(:segment)', 'Payment::success/$1'); // Payment success page

// Authentication routes
$routes->group('auth', function ($routes) {
    $routes->get('/', 'Auth::index');
    $routes->post('login', 'Auth::login');
    $routes->get('logout', 'Auth::logout');
    $routes->get('register', 'Auth::register');
    $routes->post('register', 'Auth::registerProcess');
    $routes->get('verify', 'Auth::verify');
    $routes->post('verify-otp', 'Auth::verifyOTP');
    $routes->post('resend-otp', 'Auth::resendOTP');
});

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
        $routes->get('edit/(:any)', 'Layanan::edit/$1');
        $routes->post('update/(:any)', 'Layanan::update/$1');
        $routes->get('delete/(:any)', 'Layanan::delete/$1');
        $routes->get('foto/(:any)', 'Layanan::foto/$1');
        $routes->post('getLayananByJenis', 'Layanan::getLayananByJenis');
    });

    // Booking Management (admin & pimpinan)
    $routes->group('booking', ['filter' => 'role:admin,pimpinan'], function ($routes) {
        $routes->get('/', 'Booking::index');
        $routes->get('create', 'Booking::adminCreate');
        $routes->post('store', 'Booking::adminStore');
        $routes->get('show/(:num)', 'Booking::show/$1');
        $routes->get('edit/(:num)', 'Booking::edit/$1');
        $routes->post('update/(:num)', 'Booking::update/$1');
        $routes->delete('delete/(:num)', 'Booking::delete/$1');
        $routes->delete('delete-transaction/(:num)', 'Booking::deleteTransaction/$1');
        $routes->post('approve-payment/(:num)', 'Booking::approvePayment/$1');
        $routes->post('reject-payment/(:num)', 'Booking::rejectPayment/$1');
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

    // Payment Management (admin & pimpinan)
    $routes->group('payment', ['filter' => 'role:admin,pimpinan'], function ($routes) {
        $routes->get('/', 'AdminPayment::index');
        $routes->get('detail/(:num)', 'AdminPayment::detail/$1');
        $routes->post('approve/(:num)', 'AdminPayment::approve/$1');
        $routes->post('reject/(:num)', 'AdminPayment::reject/$1');
        $routes->get('stats', 'AdminPayment::stats');
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

// Pelanggan Routes (Customer) - Focus on monitoring/tracking only
$routes->group('pelanggan', ['filter' => 'role:pelanggan'], function ($routes) {
    $routes->get('dashboard', 'Pelanggan::dashboard');
    $routes->get('profile', 'Pelanggan::profile');

    // Monitoring Routes - No booking creation, only viewing
    $routes->group('booking', function ($routes) {
        $routes->get('detail/(:num)', 'Booking::detail/$1');
        $routes->get('history', 'Booking::history');
        $routes->post('cancel/(:num)', 'Booking::cancel/$1');
        $routes->post('process-payment/(:num)', 'Booking::processPayment/$1');
    });

    // Antrian monitoring
    $routes->get('antrian', 'Pelanggan::antrian');

    // Transaction monitoring
    $routes->get('transaksi', 'Pelanggan::transaksi');
});
