<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        // Check if user is logged in
        $session = session();
        $user = null;
        $isLoggedIn = false;

        if ($session->get('user_id')) {
            $userModel = new \App\Models\UserModel();
            $user = $userModel->find($session->get('user_id'));
            $isLoggedIn = true;
        }

        // Load LayananModel to get real services data
        $layananModel = new \App\Models\LayananModel();

        // Get active services grouped by vehicle type
        $services = $layananModel->where('status', 'aktif')->findAll();

        // Group services by vehicle type
        $groupedServices = [
            'motor' => [],
            'mobil' => [],
            'lainnya' => []
        ];

        foreach ($services as $service) {
            $vehicleType = strtolower($service['jenis_kendaraan']);
            if (isset($groupedServices[$vehicleType])) {
                $groupedServices[$vehicleType][] = $service;
            }
        }

        // Get statistics from database
        $userModel = new \App\Models\UserModel();
        $bookingModel = new \App\Models\BookingModel();
        $transaksiModel = new \App\Models\TransaksiModel();

        $stats = [
            'total_customers' => $userModel->where('role', 'pelanggan')->countAllResults(),
            'total_services' => $layananModel->where('status', 'aktif')->countAllResults(),
            'total_bookings' => $bookingModel->countAllResults(),
            'total_transactions' => $transaksiModel->countAllResults()
        ];

        $data = [
            'title' => 'TiaraWash - Layanan Cuci Kendaraan Premium',
            'services' => $services,
            'grouped_services' => $groupedServices,
            'stats' => $stats,
            'user' => $user,
            'isLoggedIn' => $isLoggedIn
        ];

        return view('welcome_message', $data);
    }

    public function booking()
    {
        // Load required models
        $layananModel = new \App\Models\LayananModel();
        $pelangganModel = new \App\Models\PelangganModel();

        // Get active services
        $services = $layananModel->where('status', 'aktif')->findAll();

        // Group services by vehicle type
        $groupedServices = [];
        foreach ($services as $service) {
            $groupedServices[$service['jenis_kendaraan']][] = $service;
        }

        // Check if user is logged in as customer
        $user = null;
        $pelanggan = null;
        $isLoggedIn = false;

        if (session()->get('logged_in') && session()->get('role') === 'pelanggan') {
            $userModel = new \App\Models\UserModel();
            $user = $userModel->find(session()->get('user_id'));
            $pelanggan = $pelangganModel->where('user_id', session()->get('user_id'))->first();
            $isLoggedIn = true;
        }

        $data = [
            'title' => 'Booking Layanan - TiaraWash',
            'services' => $services,
            'grouped_services' => $groupedServices,
            'user' => $user,
            'pelanggan' => $pelanggan,
            'isLoggedIn' => $isLoggedIn
        ];

        return view('booking/form', $data);
    }
}
